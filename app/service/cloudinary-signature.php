<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$rootPath = dirname(__DIR__, 2);

// Load .env file manually
$cloudName = '';
$apiKey = '';
$apiSecret = '';

if (file_exists($rootPath . '/.env')) {
    $envLines = file($rootPath . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($envLines as $line) {
        $line = trim($line);
        if (empty($line) || strpos($line, '#') === 0) {
            continue;
        }
        $parts = explode('=', $line, 2);
        if (count($parts) === 2) {
            $key = trim($parts[0]);
            $value = trim($parts[1]);
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
}

// Get Cloudinary credentials
$cloudName = trim($_ENV['CLOUDINARY_CLOUD_NAME'] ?? getenv('CLOUDINARY_CLOUD_NAME') ?? '');
$apiKey    = trim($_ENV['CLOUDINARY_API_KEY'] ?? getenv('CLOUDINARY_API_KEY') ?? '');
$apiSecret = trim($_ENV['CLOUDINARY_API_SECRET'] ?? getenv('CLOUDINARY_API_SECRET') ?? '');

if (empty($cloudName) || empty($apiKey) || empty($apiSecret)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Cloudinary credentials tidak lengkap di file .env',
        'debug' => [
            'cloudName' => $cloudName ? '****' : '',
            'apiKey' => $apiKey ? '****' : ''
        ]
    ]);
    exit();
}

// Generate signature parameters
$timestamp = time();
$folder = 'jadwal_ujian';

// Parameters to sign (sorted alphabetically)
$paramsToSign = [
    'folder' => $folder,
    'timestamp' => $timestamp
];
ksort($paramsToSign);

// Build the parameter string
$paramStr = '';
foreach ($paramsToSign as $key => $value) {
    $paramStr .= ($paramStr ? '&' : '') . $key . '=' . $value;
}

// Generate the signature using SHA1
$signature = sha1($paramStr . $apiSecret);

// Send response
echo json_encode([
    'success' => true,
    'cloudName' => $cloudName,
    'apiKey' => $apiKey,
    'timestamp' => $timestamp,
    'signature' => $signature,
    'folder' => $folder
]);
