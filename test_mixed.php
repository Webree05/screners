<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://query1.finance.yahoo.com/v8/finance/spark?symbols=BVA.JK,GOTO.JK&interval=1d&range=5d");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
$res = curl_exec($ch);
$http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
echo "HTTP: $http\n";
echo "RES: $res\n";
