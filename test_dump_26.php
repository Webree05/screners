<?php
$d = json_decode(file_get_contents('market_data.json'), true);
print_r($d['RMKE.JK']);
echo date('Y-m-d H:i:s', end($d['RMKE.JK']['timestamp'])) . "\n";
