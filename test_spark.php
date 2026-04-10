<?php
$url = 'https://query1.finance.yahoo.com/v8/finance/spark?symbols=GOTO.JK&interval=1d&range=5d';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
$response = curl_exec($ch);


echo json_encode(json_decode($response), JSON_PRETTY_PRINT);
?>
