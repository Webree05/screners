<?php
$ch = curl_init('https://fc.yahoo.com/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_NOBODY, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$res = curl_exec($ch);
$cookie = '';
if (preg_match('/^Set-Cookie:\s*(A3=[^;]+)/im', $res, $match)) {
    $cookie = $match[1];
}



if (!$cookie) { echo "Failed to get cookie\n$res\n"; exit; }
echo "Cookie: $cookie\n";

$ch2 = curl_init('https://query1.finance.yahoo.com/v1/test/getcrumb');
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch2, CURLOPT_COOKIE, $cookie);
curl_setopt($ch2, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0');
curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
$crumb = curl_exec($ch2);



echo "Crumb: $crumb\n";

$url = "https://query1.finance.yahoo.com/v8/finance/spark?symbols=GOTO.JK&interval=1d&range=5d&crumb=" . urlencode($crumb);
$ch3 = curl_init($url);
curl_setopt($ch3, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch3, CURLOPT_COOKIE, $cookie);
curl_setopt($ch3, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0');
curl_setopt($ch3, CURLOPT_SSL_VERIFYPEER, false);
$data = curl_exec($ch3);
echo "Data Code: " . curl_getinfo($ch3, CURLINFO_HTTP_CODE) . "\n";
echo substr($data, 0, 100);
