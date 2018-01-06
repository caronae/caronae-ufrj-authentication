<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Caronae\AuthenticationAgent;
use Dotenv\Dotenv;

if (is_file(__DIR__ . '/.env')) {
    $dotenv = new Dotenv(__DIR__);
    $dotenv->load();
}

$agent = new AuthenticationAgent();
$error = null;

try {
    error_log('will call authenticateWithIntranet');
    $agent->authenticateWithIntranet();
    error_log('will call fetchUserProfileFromSIGA');
    $agent->fetchUserProfileFromSIGA();
    error_log('will call sendUserToCaronae');
    $agent->sendUserToCaronae();
} catch (\CAS_Exception $e) {
    error_log('CAS_Exception');
    $error = 'Não foi possível autenticar com a Intranet UFRJ.';
} catch (\Exception $e) {
    error_log('CAS_Exception');
    $error = $e->getMessage();
} finally {
    error_log('will redirect to caronae with error: '.$error);
    $agent->redirectToCaronae($error);
}