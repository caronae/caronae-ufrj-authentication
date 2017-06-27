<?php

namespace Caronae;

use phpCAS;

require '.env';
require __DIR__ . '/vendor/autoload.php';

class CaronaeUFRJAgent
{
    private $siga;
    private $caronae;
    private $adaptor;
    private $id_ufrj;
    private $siga_user;
    private $redirect_url;

    public function __construct()
    {
        $this->siga = new SigaService;

        $this->caronae = new CaronaeService(CARONAE_API_URL);
        $this->caronae->setInstitution(CARONAE_INSTITUTION_ID, CARONAE_INSTITUTION_PASSWORD);

        $this->adaptor = new CaronaeSigaAdaptor;

        phpCAS::client(CAS_VERSION_2_0, 'cas.ufrj.br', 443, '');
        phpCAS::setNoCasServerValidation();
    }

    public function authenticateWithIntranet()
    {
        phpCAS::forceAuthentication();
        $this->id_ufrj = phpCAS::getUser();
    }

    public function fetchUserProfileFromSIGA()
    {
        $this->siga_user = $this->siga->getProfileById($this->id_ufrj);
    }

    public function sendUserToCaronae()
    {
        $user = $this->adaptor->convertToCaronaeUser($this->siga_user);

        $response = $this->caronae->signUp($user);
        $this->redirect_url = $response->redirect_url;
    }

    public function redirectToCaronae()
    {
        header('Location: ' . $this->redirect_url, true, 302);
        die;
    }
}