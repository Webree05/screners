<?php
file_put_contents('test_dump.json', file_get_contents('http://localhost/screners/proxy.php?url=' . urlencode('https://query1.finance.yahoo.com/v8/finance/spark?symbols=GOTO.JK,BBCA.JK&interval=1d&range=1d')));
?>
