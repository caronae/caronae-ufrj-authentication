<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Caronae\HealthCheckService;

$healthCheck = new HealthCheckService();
$errors = [];

$cas = $healthCheck->checkCASConnection();
if (!$cas) {
    $errors[] = 'cas';
}

$siga = $healthCheck->checkSigaConnection();
if (!$siga) {
    $errors[] = 'siga';
}

if (!empty($errors)) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 503 Service Unavailable ' . json_encode($errors));
}

header('Content-type: application/json; charset=utf-8');
echo json_encode([
    'errors' => $errors
]);
