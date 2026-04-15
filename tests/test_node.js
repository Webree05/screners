const https = require('https');
https.get('https://query1.finance.yahoo.com/v8/finance/spark?symbols=GOTO.JK&interval=1d&range=5d', (resp) => {
  let data = '';
  resp.on('data', (chunk) => { data += chunk; });
  resp.on('end', () => {
    console.log(JSON.stringify(JSON.parse(data), null, 2));
  });
}).on("error", (err) => {
  console.log("Error: " + err.message);
});
