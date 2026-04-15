<?php
$str = "GOTO.JK,EMTK.JK,BUKA.JK,WIFI.JK,MLPT.JK,DCII.JK,BELI.JK,GLVA.JK,AWAN.JK,TRON.JK,PGJO.JK,TECH.JK,KREN.JK,MCAS.JK,NFCX.JK,DIVA.JK,LUCK.JK,ZATA.JK,SEGE.JK,IRSX.JK,BREN.JK,BYAN.JK,ADRO.JK,PTBA.JK,PGAS.JK";
$url = "https://query2.finance.yahoo.com/v8/finance/spark?symbols=" . urlencode($str) . "&interval=1d&range=5d";
echo "$url\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
$res = curl_exec($ch);
echo "HTTP: " . curl_getinfo($ch, CURLINFO_HTTP_CODE) . "\n";
echo substr($res, 0, 100);
