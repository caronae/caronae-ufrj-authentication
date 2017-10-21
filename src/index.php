<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Caronae\CaronaeUFRJAgent;
use Dotenv\Dotenv;

if (is_file(__DIR__ . '/.env')) {
    $dotenv = new Dotenv(__DIR__);
    $dotenv->load();
}

$agent = new CaronaeUFRJAgent();
$error = null;

try {
    $agent->authenticateWithIntranet();
    $agent->fetchUserProfileFromSIGA();
    $agent->sendUserToCaronae();
} catch (\CAS_Exception $e) {
    $error = 'Não foi possível autenticar com a Intranet UFRJ.';
} catch (\Exception $e) {
    $error = $e->getMessage();
} finally {
    $agent->redirectToCaronae($error);
}