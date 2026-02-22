<?php
/**
 * Health Check Endpoint
 * Lab DIABLE - XPath Injection
 */

header('Content-Type: application/json');

$health = [
    'status' => 'healthy',
    'service' => 'xpath-injection-lab',
    'timestamp' => date('Y-m-d H:i:s'),
    'checks' => []
];

// Vérifier que les fichiers XML existent
$data_path = getenv('DATA_PATH') ?: '/var/www/html/data';
$required_files = ['users.xml', 'secrets.xml', 'products.xml'];

foreach ($required_files as $file) {
    $filepath = "$data_path/$file";
    $exists = file_exists($filepath);
    
    $health['checks'][$file] = [
        'status' => $exists ? 'ok' : 'error',
        'path' => $filepath,
        'readable' => $exists ? is_readable($filepath) : false
    ];
    
    if (!$exists) {
        $health['status'] = 'unhealthy';
    }
}

// Vérifier l'extension DOM PHP
$health['checks']['php_dom'] = [
    'status' => extension_loaded('dom') ? 'ok' : 'error',
    'loaded' => extension_loaded('dom')
];

if (!extension_loaded('dom')) {
    $health['status'] = 'unhealthy';
}

// Code de statut HTTP
http_response_code($health['status'] === 'healthy' ? 200 : 503);

echo json_encode($health, JSON_PRETTY_PRINT);
?>
