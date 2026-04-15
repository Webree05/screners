<?php
$ticker = 'WIKA.JK';
$url = "https://query1.finance.yahoo.com/v8/finance/spark?symbols=$ticker&interval=1d&range=5d";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$res = curl_exec($ch);
echo $res;
?>
