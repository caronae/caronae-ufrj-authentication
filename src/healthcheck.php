<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Caronae\HealthCheckService;

$healthCheck = new HealthCheckService();

$cas = $healthCheck->checkCASConnection();

$errors = [];

if (!$cas) {
    http_response_code(503);
    $errors[] = 'Connection with CAS failed';
}

header('Content-type: application/json; charset=utf-8');
echo json_encode([
    'errors' => $errors
]);
