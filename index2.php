<?php

require __DIR__ . '/vendor/autoload.php';

use Caronae\CaronaeUFRJAgent;

$agent = new CaronaeUFRJAgent();
$agent->authenticateWithIntranet();
$agent->fetchUserProfileFromSIGA();
$agent->sendUserToCaronae();
$agent->redirectToCaronae();