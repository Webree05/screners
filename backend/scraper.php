<?php
// backend/scraper.php
// AI-Powered Market Data Scraper - Background Worker
// Script ini akan dipanggil otomatis oleh run_bot.bat

set_time_limit(0);
error_reporting(E_ALL);

function log_message($msg) {
    echo "[" . date('H:i:s') . "] " . $msg . "\n";
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
$ALL_SECTOR_TICKERS = array_values(array_unique(array_merge($API_TICKERS, $ALL_VALID_TICKERS)));

$total_tickers = count($ALL_SECTOR_TICKERS);
log_message("Memulai proses scraping untuk $total_tickers saham...");

// Batas aman API Yahoo Spark adalah 100-200 simbol per request
$batch_size = 100;

while (true) {
    if (date('H') < 8 || date('H') > 22) {
        log_message("Market closed. Standby 300 detik...");
        sleep(300);
        continue;
    }

    $chunks = array_chunk($ALL_SECTOR_TICKERS, $batch_size);
    $market_data = [];
    $total_chunks = count($chunks);

    log_message("Memulai siklus update data ($total_chunks batches)...");

    foreach ($chunks as $index => $chunk) {
        $symbols = array_map(function($t) { return $t . '.JK'; }, $chunk);
        $symbols_str = implode(',', $symbols);
        // Optimized interval & range for performance but keep 5d for pct calculations
        $url = "https://query1.finance.yahoo.com/v8/finance/spark?symbols=" . urlencode($symbols_str) . "&interval=1d&range=5d";
        
        $max_retries = 2;
        $success = false;

        for ($retry = 0; $retry <= $max_retries; $retry++) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36');
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200 && $response) {
                $json = json_decode($response, true);
                if ($json && is_array($json)) {
                    $market_data = array_merge($market_data, $json);
                    $success = true;
                    break;
                }
            }
            
            if ($retry < $max_retries) {
                log_message("Retry batch " . ($index + 1) . "... ($retry)");
                sleep(1);
            }
        }

        if (!$success) {
            log_message("Warning: Batch ".($index + 1)." GAGAL setelah $max_retries retries.");
        }
        
        // Jeda sangat singkat untuk efisiensi tinggi
        usleep(300000); // 0.3 detik
    }

    if (count($market_data) > 0) {
        $filePath = __DIR__ . '/../market_data.json';
        file_put_contents($filePath, json_encode($market_data));
        log_message("UP-TO-DATE: Berhasil menyimpan " . count($market_data) . " emiten ke market_data.json");
    } else {
        log_message("Error: Seluruh request gagal, mengecek koneksi...");
    }

    log_message("Siklus selesai. Standby 5 detik...");
    sleep(5); 
}
?>
