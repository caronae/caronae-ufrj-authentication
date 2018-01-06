<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Caronae\HealthCheckService;

$healthCheck = new HealthCheckService();
$errors = [];

$cas = $healthCheck->checkCASConnection();
if (!$cas) {
    http_response_code(503);
    $errors[] = 'cas';
}

$siga = $healthCheck->checkSigaConnection();
if (!$siga) {
    http_response_code(503);
    $errors[] = 'siga';
}

header('Content-type: application/json; charset=utf-8');
echo json_encode([
    'errors' => $errors
]);
