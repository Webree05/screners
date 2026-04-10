<?php
/**
 * ULTRA PROXY - Yahoo Finance CORS Bypass
 * Berbasis Model Statistik & Real-Time Data Pipeline
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Cache-Control: no-cache, must-revalidate');

if (!isset($_GET['url'])) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Missing URL parameter. Harap sertakan URL tujuan.',
        'code' => 400
    ]);
    exit;
}

$url = $_GET['url'];

// Enhanced security validation (Jangan hapus rute ini bro)
$allowed_domains = ['finance.yahoo.com', 'query1.finance.yahoo.com', 'query2.finance.yahoo.com'];
$is_valid = false;
foreach ($allowed_domains as $domain) {
    if (strpos($url, $domain) !== false) {
        $is_valid = true;
        break;
    }
}

if (!$is_valid) {
    http_response_code(403);
    echo json_encode([
        'status' => 'forbidden',
        'message' => 'Invalid URL domain. Akses hanya diijinkan untuk Yahoo Finance.',
        'code' => 403
    ]);
    exit;
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 20); // Sedikit lebih longgar agar tidak gampang timeout

// Professional Browsing Headers (Mimicking real user)
$headers = [
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36',
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
    'Accept-Language: en-US,en;q=0.9,id;q=0.8',
    'Connection: keep-alive',
    'Cache-Control: max-age=0',
    'Upgrade-Insecure-Requests: 1'
];
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

// Execute request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'CURL Failure: ' . $error,
        'code' => 500
    ]);
} else {
    http_response_code($httpCode);
    
    // Check if response is actually JSON to prevent browser parse errors
    $json_content = json_decode($response);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo $response;
    } else {
        // Jika bukan JSON (mungkin HTML error page dari Yahoo), bungkus kedalam JSON format
        echo json_encode([
            'status' => 'success_payload',
            'http_code' => $httpCode,
            'raw_response' => mb_strimwidth(strip_tags($response), 0, 500, '...'),
            'message' => 'Data diterima namun bukan format JSON murni. Pastikan URL Yahoo benar.'
        ]);
    }
}
?>
