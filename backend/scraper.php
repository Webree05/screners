<?php
// backend/scraper.php
// AI-Powered Market Data Scraper - Background Worker
// Script ini akan dipanggil otomatis oleh run_bot.bat

set_time_limit(0);
error_reporting(E_ALL);
date_default_timezone_set('Asia/Jakarta');

function log_message($msg) {
    echo "[" . date('H:i:s') . "] " . $msg . "\n";
}

function get_yahoo_crumb(&$cookie, &$crumb, $userAgent) {
    if (!empty($cookie) && !empty($crumb)) return true;
    
    log_message("Requesting Yahoo session cookies & crumb...");
    $ch = curl_init('https://fc.yahoo.com/');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $res = curl_exec($ch);
    if (preg_match('/^Set-Cookie:\s*(A3=[^;]+)/im', $res, $match)) {
        $cookie = $match[1];
    }
    

    
    if (empty($cookie)) return false;
    
    $ch2 = curl_init('https://query1.finance.yahoo.com/v1/test/getcrumb');
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch2, CURLOPT_COOKIE, $cookie);
    curl_setopt($ch2, CURLOPT_USERAGENT, $userAgent);
    curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
    $crumb = curl_exec($ch2);
    

    
    return !empty($crumb);
}


$SECTOR_MAPPING = [
    "TECHNOLOGY" => ["GOTO", "EMTK", "BUKA", "WIFI", "MLPT", "DCII", "BELI", "GLVA", "AWAN", "TRON", "PGJO", "TECH", "KREN", "MCAS", "NFCX", "DIVA", "LUCK", "ZATA", "SEGE", "IRSX"],
    "ENERGY" => ["BREN", "BYAN", "ADRO", "PTBA", "PGAS", "MEDC", "AKRA", "ITMG", "HRUM", "PTRO", "ENRG", "TOBA", "INDY", "DOID", "ABMM", "DEWA", "BUMI", "BOSS", "BIPI", "APEX", "SMMT", "DSSA", "RUIS", "SGER", "GTBO", "TCPI", "WINS", "MBSS", "BULL", "CANI", "ELSA", "ESSA", "PKPK", "FIRE", "KOPI", "SURE", "DWGL", "CNKO", "MITI", "PSAB"],
    "BASIC-IND" => ["AMMN", "INTP", "SMGR", "ANTM", "MDKA", "INCO", "BRPT", "TPIA", "INKP", "TKIM", "ESSA", "KRAS", "SMBR", "DKFT", "MBMA", "ARCI", "CITA", "IFSH", "ZINC", "TINS", "NIKL", "JKSW", "ALMI", "ALDO", "FASW", "SPMA", "KDSI", "SWAT", "IGAR", "TALF", "TRST", "AKPI", "SRSN", "ETWA", "SULI", "TIRT"],
    "INFRASTRUC" => ["TLKM", "TOWR", "TBIG", "EXCL", "ISAT", "JSMR", "MTEL", "WIKA", "PTPP", "ADHI", "WSKT", "META", "CMNP", "SUPR", "BALI", "CENT", "WEGE", "WTON", "PPRE", "ACST", "DGIK", "TOPS", "NRCA", "JKON", "TOTL", "CSIS", "BUKK", "OASA", "KBLI", "KBLM", "VOKS", "SCCO", "CCSI", "PORT", "BATA", "KIJA", "DSSA"],
    "HEALTH" => ["KLBF", "HEAL", "MIKA", "SILO", "PRDA", "SIDO", "PEHA", "KAEF", "INAF", "OMED", "CARE", "SAME", "RSGK", "BMHS", "IRRA", "DGNS", "MEJA", "SRAJ", "DVLA", "PURA", "TSPC", "PYFA", "MERK", "SCPI"],
    "INDUSTRIAL" => ["ASII", "UNTR", "HEXA", "MARK", "BMHS", "KINO", "MLBI", "AUTO", "SMSM", "IMAS", "GJTL", "MASA", "GDYR", "BRAM", "LPIN", "SMCB", "ARNA", "KIAS", "LION", "TRIS", "BELL", "RICY", "SBMA", "TFCO", "IKBI", "BTON", "JRSPT", "AMFG", "TOTO"],
    "TRANSPORT" => ["BIRD", "GIAA", "ASLC", "TMAS", "BPTR", "SMDR", "IPCM", "CASS", "TRJA", "HAIS", "BLTA", "CANI", "SAPX", "ASHA", "GGRP", "TAXI", "WEHA", "LRNA", "SAFE", "CMPP", "IATA", "LEAD", "PTIS", "TPMA", "HITS", "BULL"],
    "FINANCE" => ["BBCA", "BMRI", "BBNI", "BBRI", "ARTO", "BRIS", "PNLF", "BDMN", "BNGA", "NISP", "BBTN", "BBYB", "AGRO", "BBHI", "MEGA", "PNBN", "MAYA", "BJBR", "BJTM", "BSIM", "BBKP", "MCOR", "BINA", "BACA", "BVIC", "AGRS", "DNAR", "NOBU", "BKSW", "BGTG", "BPFI", "BFIN", "CFIN", "CLIP", "MFIN", "TIFA", "VVIC", "SMCB", "TRIM", "YULE", "RELI", "LPGI", "AMAG", "ASBI", "ASRM", "AHAP"],
    "CYCLICAL" => ["MAPA", "MAPI", "ACES", "MSIN", "SCMA", "LPPF", "RALS", "ERAA", "CSAP", "GLOB", "HOME", "KOIN", "SONA", "DSFI", "PTSP", "GZCO", "WAPO", "FAST", "PZZA", "CLEO", "MINA", "PSDN", "CAMP", "PCAR", "IPAC", "JSPT", "PGLI", "SHID", "PANR", "DART", "BAYU"],
    "PROPERTY" => ["CTRA", "BSDE", "PWON", "SMRA", "ASRI", "PANI", "DILD", "KIJA", "APLN", "OMRE", "BKSL", "LPCK", "LPKR", "BEST", "SSIA", "GPRA", "JRPT", "MDLN", "MMLP", "MTLA", "NIRO", "PLIN", "RODA", "MKPI", "FMII", "BAPA", "GMTD", "LCGP", "MIND", "SCBD"],
    "NON-CYCLICAL" => ["UNVR", "ICBP", "INDF", "CPIN", "MYOR", "AMRT", "MIDI", "JPFA", "CMRY", "GOOD", "CLEO", "AISA", "ALTO", "CAMP", "CEKA", "DLTA", "MLBI", "PSDN", "ROTI", "SKBM", "SKLT", "STTP", "ULTJ", "HOKI", "KEJU", "BISI", "CPRO", "DSFI", "WMUU", "PMMP", "WMPP"],
    "JASA-KONSUMEN" => ["MAPA", "MAPI", "ACES", "LPPF", "RALS", "ERAA", "SCMA", "CSAP", "PZZA", "FAST", "SHID", "PANR", "DART", "BAYU", "PGLI", "JSPT", "IPAC", "GLOB", "HOME", "KOIN"],
    "SYARIAH" => ["TLKM", "ASII", "BRIS", "ICBP", "INDF", "UNVR", "KLBF", "AKRA", "CPIN", "UNTR", "PTBA", "INCO", "MDKA", "TPIA", "BRPT", "SMGR", "INTP", "MIKA", "SILO", "HEAL", "SIDO", "PGAS", "ITMG"],
    "DAY-TRADE" => ["PANI", "BREN", "CUAN", "AMMN", "GOTO", "STRK", "AWAN", "TPIA", "BRPT", "WIFI", "DOID", "BUMI", "BRMS", "VKTR", "MUTU", "CGAS", "OASA", "PGEO", "FREN", "BBKP", "BSBK", "PSAB", "LMAX", "NATC"]
];

$VALID_IDX_DATABASE_STRING = "AADI,AALI,ABBA,ABDA,ABMM,ACES,ACRO,ACST,ADCP,ADES,ADHI,ADMF,ADMG,ADRO,AEGS,AGAR,AGRO,AGRS,AHAP,AIMS,AISA,AKKU,AKPI,AKRA,AKSI,ALDO,ALII,ALKA,ALMI,ALTO,AMAG,AMAN,AMAR,AMFG,AMIN,AMMN,AMOR,AMPT,AMRT,ANDI,ANJT,ANTM,APEX,APII,APLI,APLN,ARCI,AREA,ARGO,ARKA,ARKO,ARNA,ARTA,ARTI,ARTO,ASBI,ASDM,ASGR,ASHA,ASII,ASJT,ASLC,ASLI,ASMI,ASPR,ASRI,ASRM,ASSA,ATIC,ATLA,AUTO,AYLS,BABP,BACA,BAJA,BALI,BANK,BAPA,BAPI,BATA,BATO,BATR,BAYU,BBCA,BBHI,BBKP,BBLD,BBMD,BBNI,BBRI,BBRM,BBSI,BBSS,BBTN,BBYB,BCAP,BCIC,BCIP,BDMN,BEKS,BELL,BESS,BEST,BFIN,BGTG,BHAT,BHTK,BIKA,BIMA,BINA,BIPI,BIPP,BIRD,BISI,BISN,BKDP,BKSL,BKSW,BLES,BLOG,BLTA,BLTZ,BMAS,BMHS,BMRI,BMSR,BMTR,BNBA,BNBR,BNGA,BNII,BNLI,BOBA,BOGA,BOSS,BPFI,BPII,BPTR,BRAM,BRIS,BRMS,BRNA,BRPT,BRRC,BSBK,BSDE,BSIM,BSML,BSSR,BSWD,BTEK,BTEL,BTON,BTPN,BTPS,BUDI,BUKA,BUKK,BULL,BUMI,BVIC,BWPT,BYAN,CAKK,CAMP,CANI,CARE,CASS,CBDK,CBMF,CCSI,CDIA,CEKA,CENT,CFIN,CGAS,CHEK,CINT,CITA,CITY,CLEO,CLPI,CMNP,CMPP,CMRY,CNKO,CNTX,COAL,COCO,COIN,CPIN,CPRO,CRAB,CSAP,CSIS,CSMI,CSRA,CTBN,CTRA,CTTH,CUAN,DADA,DART,DATA,DAYA,DCII,DEAL,DEFI,DEWA,DFAM,DGIK,DGNS,DGWG,DIKA,DILD,DIVA,DKFT,DKHH,DLTA,DMAS,DMMX,DNAR,DOID,DPNS,DPUM,DSFI,DSNG,DSSA,DUTI,DVLA,DWGL,EAST,ECII,EDSA,EIKON,EKAD,ELJA,ELSA,ELTY,EMAS,EMDE,EMTK,ENRG,ENVY,EPMT,ERAA,ERTX,ESIP,ESSA,ESTA,ETWA,EVIN,EXCL,FAST,FASW,FILM,FIMI,FIRE,FISH,FIT,FITT,FLMC,FMII,FOOD,FORE,FORZ,FPNI,FPTI,FREN,GAMA,GDST,GDYR,GEMA,GEMS,GGRM,GIAA,GJTL,GLOB,GLVA,GMCW,GMTD,GOLD,GOLL,GOOD,GOTO,GPRA,GRLN,GRPH,GRPN,GSMF,GTBO,GTSI,GUNA,GWSA,GYLN,HADE,HAIS,HDIT,HDTX,HEAL,HELI,HERO,HEXA,HGII,HITS,HKMU,HMSP,HOKI,HOME,HOMI,HOPE,HOTL,HRME,HRTA,HRUM,HYGN,IACT,IATA,IBFN,IBST,ICBP,ICON,IDPR,IFSH,IGAR,IIKP,IKAI,IKAN,IKBI,IMAS,IMJS,IMPC,INAF,INAI,INCF,INCO,INDA,INDF,INDP,INDR,INDS,INDY,INFO,INKP,INMF,INPC,INPP,INPS,INRU,INTA,INTD,INTP,IPAC,IPCC,IPCM,IPSG,IPTV,IRRA,ISA,ISAT,ISSP,ITIC,ITMA,ITMG,JAST,JAWA,JAYA,JECC,JIHD,JKON,JKSW,JMAS,JPFA,JRPT,JSPT,JTPE,KAEF,KAQI,KARK,KAYU,KBAG,KBLI,KBLM,KBLV,KDSI,KEJU,KICI,KIJA,KINO,KIOS,KLBF,KMTR,KOBX,KOIN,KONI,KOPI,KOTA,KPAL,KPIG,KRAK,KRAS,KREN,KSIX,KUAS,LABA,LAPD,LCGP,LCKM,LEAD,LFLO,LINK,LION,LIVE,LMAS,LMPI,LMSH,LPCK,LPGI,LPIN,LPKR,LPLI,LPPF,LRN,LRNA,LSIP,LTC,LUCK,LUX,MAGP,MAIN,MALL,MANG,MAPA,MAPI,MARD,MARK,MASA,MAYA,MBAP,MBSS,MBTO,MCAS,MCOR,MDKA,MDLA,MDLN,MDRN,MEDC,MEGA,MERI,MERK,META,MFIN,MFMI,MGLV,MGNA,MGRO,MHKI,MICE,MIDI,MIKA,MINA,MIND,MINE,MIRA,MITI,MKAP,MKNT,MKPI,MLBI,MLIA,MLPT,MNCN,MOLI,MORA,MPMX,MPOO,MSIN,MSJA,MSKY,MTDL,MTEL,MTFN,MTLA,MTPS,MTRA,MTSM,MYOH,MYOR,MYRX,MYTX,NANO,NAPS,NASA,NATO,NELY,NFCX,NICE,NICK,NICL,NIPS,NIRO,NISP,NOBU,NRCA,NSSS,NUSA,NZIA,OASA,OBAT,OBMD,OCAP,OILS,OKAS,OMRE,OPMS,PADI,PALM,PAMG,PANI,PANR,PANS,PART,PBRX,PBSA,PCAR,PDES,PEGE,PEHA,PGAS,PGJO,PGLI,PGRX,PICO,PJAA,PJHB,PKPK,PLAS,PLIN,PMMP,PMUI,PNBN,PNBS,PNIN,PNLF,PNSE,POLA,POLI,POLL,POLU,POLY,POOL,PORT,POSA,PPRE,PRAS,PRDA,PRIM,PSAB,PSAT,PSDN,PSGO,PSKT,PTBA,PTIS,PTPP,PTRO,PTSN,PTSP,PURA,PURE,PZZA,RAJA,RALS,RANC,RATU,RBMS,RDTX,REAL,RELI,RICY,RIGS,RIMO,RLCO,RMBA,RMKE,RODA,ROTI,RUIS,SAFE,SAME,SAPX,SBMA,SCBD,SCCO,SCMA,SCNP,SCPI,SDMU,SDPC,SIDO,SILO,SIMA,SIMP,SINI,SITARA,SJA,SJMU,SKBM,SKLT,SKRN,SMCB,SMDM,SMDR,SMGR,SMLE,SMMA,SMMT,SMRA,SMRU,SMSM,SNLK,SOCI,SOLA,SONA,SOSS,SOTS,SPMA,SPRE,SQMI,SRAJ,SRIL,SRSN,SSIA,SSMS,SSTK,STTP,SUGI,SULI,SUPA,SUPR,SURE,SWAT,TAXI,TBIG,TBMS,TCID,TCPI,TEBE,TECH,TELE,TFCO,TGKA,TIFA,TINS,TIRA,TIRT,TKIM,TLKM,TMAS,TMPO,TOBA,TOPS,TOTO,TOWR,TPIA,TPMA,TRAM,TRIL,TRIM,TRIN,TRIO,TRIS,TRJA,TRON,TRST,TRUS,TSPC,TUGU,TURI,UANG,UCID,UFOE,ULTJ,UNIC,UNIQ,UNIT,UNSP,UNTR,UNVR,VISI,VIVA,VOKS,VRNA,WAPO,WEGE,WEHA,WICO,WIFI,WIKA,WINS,WIRG,WMPP,WMUU,WOMF,WOOD,WOWS,WSBP,WSKT,WTON,YELO,YOII,YPAS,YULE,YUPI,ZATA,ZBRA,ZINC";


$API_TICKERS = [];
foreach ($SECTOR_MAPPING as $sec => $tickers) {
    $API_TICKERS = array_merge($API_TICKERS, $tickers);
}
$ALL_VALID_TICKERS = explode(",", $VALID_IDX_DATABASE_STRING);
$MACRO_GLOBAL = ["^JKSE", "USDIDR=X", "GOLD=F"]; // IHSG, Rupiah, Gold
$ALL_SECTOR_TICKERS = array_values(array_filter(array_unique(array_merge($API_TICKERS, $ALL_VALID_TICKERS, $MACRO_GLOBAL))));

$total_tickers = count($ALL_SECTOR_TICKERS);
log_message("Memulai proses scraping untuk $total_tickers saham...");

// Batas aman API Yahoo Spark adalah maks 20 simbol per request (API error 400 jika lebih dari 20)
$batch_size = 20;


// Optimized for both CLI/Background loop and Online/Cron Job modes
$is_cron = (php_sapi_name() != "cli" || (isset($argv) && in_array("--once", $argv)));

while (true) {
    static $yahoo_cookie = '';
    static $yahoo_crumb = '';
    static $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36';

    if (!get_yahoo_crumb($yahoo_cookie, $yahoo_crumb, $userAgent)) {
         log_message("Warning: Gagal mendapatkan cookie Yahoo. Lanjut tanpa crumb.");
    }

    // Bot sekarang berjalan 24/7 nonstop secara agresif seperti standar Crypto.
    // Logic Standby (Market Closed) dihapus agar bisa mengupdate/menyinkronkan data terus menerus meskipun weekend.

    $chunks = array_chunk($ALL_SECTOR_TICKERS, $batch_size);
    $market_data = [];
    $total_chunks = count($chunks);

    log_message("Memulai siklus update data ($total_chunks batches)...");

    foreach ($chunks as $index => $chunk) {
        $symbols = array_map(function($t) { 
            $t = trim($t);
            if (strpos($t, '^') === 0 || strpos($t, '=') !== false) return $t; 
            return $t . '.JK'; 
        }, $chunk);
        $symbols_str = implode(',', $symbols);
        $hosts = ['query1.finance.yahoo.com', 'query2.finance.yahoo.com'];
        $host = $hosts[array_rand($hosts)];
        // Optimized interval & range for performance but keep 5d for pct calculations
        $url = "https://" . $host . "/v8/finance/spark?symbols=" . urlencode($symbols_str) . "&interval=1d&range=5d";
        if (!empty($yahoo_crumb)) {
            $url .= "&crumb=" . urlencode(trim($yahoo_crumb));
        }
        
        $start_ua = $userAgent;
        
        $max_retries = 2;
        $success = false;

        for ($retry = 0; $retry <= $max_retries; $retry++) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, $start_ua);
            
            $headers = [
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
                'Accept-Language: en-US,en;q=0.5'
            ];
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            
            if (!empty($yahoo_cookie)) {
                curl_setopt($ch, CURLOPT_COOKIE, $yahoo_cookie);
            }
            
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            


            if ($httpCode === 200 && $response) {
                $json = json_decode($response, true);
                if ($json && is_array($json)) {
                    $market_data = array_merge($market_data, $json);
                    $success = true;
                    break;
                }
            }
            
            if ($retry < $max_retries) {
                log_message("Retry batch " . ($index + 1) . "... ($retry) HTTP CODE: " . (isset($httpCode) ? $httpCode : 'N/A'));
                sleep(1);
            }
        }

        if (!$success) {
            log_message("Warning: Batch ".($index + 1)." GAGAL setelah $max_retries retries | HTTP CODE: " . (isset($httpCode) ? $httpCode : 'N/A'));
            // Reset cookie and crumb if blocked (429) or unauthorized (401)
            if (isset($httpCode) && ($httpCode == 429 || $httpCode == 401)) {
                $yahoo_cookie = '';
                $yahoo_crumb = '';
            }
        }

        
        // Jeda sangat aman agar tidak terkena Too Many Requests (429) filter
        usleep(1500000); // 1.5 detik
    }

    if (count($market_data) > 0) {
        $filePath = __DIR__ . '/../market_data.json';
        $existing_data = [];
        if (file_exists($filePath)) {
            $existing_raw = @file_get_contents($filePath);
            if (!empty($existing_raw)) {
                $existing_json = json_decode($existing_raw, true);
                if (is_array($existing_json)) {
                    $existing_data = $existing_json;
                }
            }
        }
        
        $merged_data = array_merge($existing_data, $market_data);
        $json_payload = json_encode($merged_data);
        
        if ($json_payload !== false) {
            // Atomic safe write (mencegah korupsi file jika bot distop paksa)
            $tempFile = $filePath . '.tmp';
            file_put_contents($tempFile, $json_payload);
            
            if (file_exists($tempFile)) {
                @unlink($filePath); // Bersihkan file lama jika ada (optional, rename akan overwrite)
                rename($tempFile, $filePath);
                log_message("UP-TO-DATE: Sukses menyimpan " . count($market_data) . " emiten. Total database: " . count($merged_data));
                
                // Trigger AI Intelligence Engine Processing
                log_message("Mengaktifkan AI Smart Engine untuk mengolah data...");
                $ch_intel = curl_init('http://localhost:8000/api/v1/intel/process');
                curl_setopt($ch_intel, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch_intel, CURLOPT_TIMEOUT, 5);
                $res_intel = curl_exec($ch_intel);
                $httpCode_intel = curl_getinfo($ch_intel, CURLINFO_HTTP_CODE);
                if ($httpCode_intel === 200) {
                    log_message("AI ENGINE: Sukses memproses data pasar terbaru.");
                } else {
                    log_message("AI ENGINE: Gagal memicu pemrosesan data (HTTP $httpCode_intel). Pastikan Python Engine berjalan.");
                }
                curl_close($ch_intel);
            } else {
                log_message("Error: Gagal membuat file temporary untuk market data.");
            }
        } else {
            log_message("Error: Gagal melakukan JSON Encode pada data market.");
        }
    } else {
        log_message("Warning: Siklus ini gagal mengambil data baru. Mengecek koneksi Yahoo...");
    }

    log_message("Siklus selesai. Standby 10 detik...");
    if ($is_cron) break; // Exit loop for Cron Job compatibility
    sleep(10); 
}
?>
