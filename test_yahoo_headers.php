<?php
$url = 'https://query1.finance.yahoo.com/v8/finance/spark?symbols=GOTO.JK,BBCA.JK&interval=1d&range=5d';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
$headers = [
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
    'Accept-Language: en-US,en;q=0.9',
    'Connection: keep-alive',
    'Cache-Control: max-age=0'
];
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$res = curl_exec($ch);
$http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
echo "HTTP CODE: " . $http . "\n";
echo "RESPONSE SIZE: " . strlen($res) . "\n";
if ($http != 200) {
    echo substr($res, 0, 500) . "\n";
}
