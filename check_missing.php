<?php
$data = json_decode(file_get_contents('c:/laragon/www/screners/market_data.json'), true);
$missing = [];
foreach ($data as $ticker => $info) {
    if (!isset($info['close']) || empty($info['close']) || end($info['close']) == 0) {
        $missing[] = $ticker;
    }
}
echo "Missing prices for: " . implode(', ', $missing) . "\n";
echo "Total missing: " . count($missing) . "\n";
?>
