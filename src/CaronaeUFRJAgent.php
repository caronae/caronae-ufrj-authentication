<?php

namespace Caronae;

use phpCAS;

class CaronaeUFRJAgent
{
    private $siga;
    private $caronae;
    private $adaptor;
    private $id_ufrj;
    private $siga_user;

    public function __construct()
    {
        $this->siga = new SigaService;

        $this->caronae = new CaronaeService(getenv('CARONAE_API_URL'));
        $this->caronae->setInstitution(getenv('CARONAE_INSTITUTION_ID'), getenv('CARONAE_INSTITUTION_PASSWORD'));

        $this->adaptor = new CaronaeSigaAdaptor;

        $this->setupCAS();
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
        $this->caronae->authorize($user);
    }

    public function redirectToCaronae($errorReason = null)
    {
        $redirect_url = empty($errorReason) ? $this->caronae->redirectUrlForSuccess() : $this->caronae->redirectUrlForError($errorReason);
        header('Location: ' . $redirect_url, true, 302);
        die;
    }

    private function setupCAS()
    {
        phpCAS::client(CAS_VERSION_2_0, 'cas.ufrj.br', 443, '');
        phpCAS::setNoCasServerValidation();
        phpCAS::setFixedServiceURL($_SERVER['REQUEST_URI']);
    }
}