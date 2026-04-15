<?php
function get_yahoo_crumb(&$cookie, &$crumb) {
    echo "Getting cookie...\n";
    $ch = curl_init('https://fc.yahoo.com/');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $res = curl_exec($ch);
    if (preg_match('/^Set-Cookie:\s*(A3=[^;]+)/im', $res, $match)) {
        $cookie = $match[1];
    }
    

    if (empty($cookie)) return false;
    echo "Cookie: $cookie\n";
    
    $ch2 = curl_init('https://query1.finance.yahoo.com/v1/test/getcrumb');
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch2, CURLOPT_COOKIE, $cookie);
    curl_setopt($ch2, CURLOPT_USERAGENT, 'Mozilla/5.0');
    curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
    $crumb = curl_exec($ch2);
    

    echo "Crumb: $crumb\n";
    return !empty($crumb);
}

$cookie=''; $crumb='';
get_yahoo_crumb($cookie, $crumb);

$symbols_str = "GOTO.JK,BBCA.JK,BMRI.JK,BBNI.JK,TLKM.JK,ASII.JK,AMMN.JK,BREN.JK,CUAN.JK,TPIA.JK,BRPT.JK,ADRO.JK,PTBA.JK,ITMG.JK,PGAS.JK,UNTR.JK,KLBF.JK,ICBP.JK,INDF.JK,UNVR.JK";

$url = "https://query2.finance.yahoo.com/v8/finance/spark?symbols=" . urlencode($symbols_str) . "&interval=1d&range=5d&crumb=" . urlencode(trim($crumb));

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
curl_setopt($ch, CURLOPT_COOKIE, $cookie);
$headers = [
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
    'Accept-Language: en-US,en;q=0.5'
];
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$res = curl_exec($ch);
echo "HTTP: " . curl_getinfo($ch, CURLINFO_HTTP_CODE) . "\n";
echo "Response starts with: " . substr($res, 0, 100);
