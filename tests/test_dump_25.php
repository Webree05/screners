<?php
file_put_contents('test_dump_25.json', file_get_contents('http://localhost/screners/proxy.php?url=' . urlencode('https://query1.finance.yahoo.com/v8/finance/spark?symbols=GOTO.JK,BBCA.JK,BBNI.JK,BMRI.JK,BREN.JK,TLKM.JK,ASII.JK,AMMN.JK,CUAN.JK,WIFI.JK,BUKA.JK,EMTK.JK,MDKA.JK,BRPT.JK,ADRO.JK,PGAS.JK,UNTR.JK,INDF.JK,ICBP.JK,KLBF.JK,AKRA.JK,CPIN.JK,INKP.JK,TKIM.JK,INTP.JK&interval=1d&range=1d')));
?>
