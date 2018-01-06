<?php

namespace Caronae\UFRJ;

use phpCAS;

class CASService
{
    const CAS_HOSTNAME = 'cas.ufrj.br';

    public function setup()
    {
        phpCAS::setDebug('php://stdout');
        phpCAS::client(CAS_VERSION_2_0, self::CAS_HOSTNAME, 443, '');
        phpCAS::setNoCasServerValidation();

        $serviceURL = $this->getServiceURL();
        if (!is_null($serviceURL)) {
            phpCAS::setFixedServiceURL($serviceURL);
        }
    }

    public function getServiceURL()
    {
        if (!isset($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
            return null;
        }

        return $_SERVER['HTTP_X_FORWARDED_PROTO'] . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    public function authenticate()
    {
        ob_start();
        phpCAS::forceAuthentication();
        ob_end_clean();

        return phpCAS::getUser();
    }
}