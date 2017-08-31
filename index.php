<?php

require __DIR__ . '/vendor/autoload.php';

use Caronae\CaronaeUFRJAgent;

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