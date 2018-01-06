<?php

namespace Caronae;

use Caronae\UFRJ\CASService;
use Caronae\UFRJ\SigaService;

class AuthenticationAgent
{
    private $cas;
    private $siga;
    private $caronae;
    private $adaptor;
    private $id_ufrj;
    private $siga_user;

    public function __construct()
    {
        $this->siga = new SigaService();

        $this->caronae = new CaronaeService(getenv('CARONAE_API_URL'));
        $this->caronae->setInstitution(getenv('CARONAE_INSTITUTION_ID'), getenv('CARONAE_INSTITUTION_PASSWORD'));

        $this->adaptor = new SigaToCaronaeAdaptor();

        $this->cas = new CASService();
        $this->cas->setup();
    }

    public function authenticateWithIntranet()
    {
        $this->id_ufrj = $this->cas->authenticate();
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
        if (empty($errorReason)) {
            $redirect_url = $this->caronae->redirectUrlForSuccess();
        } else {
            $redirect_url = $this->caronae->redirectUrlForError($errorReason);
        }

        header('Location: ' . $redirect_url, true, 302);
        die;
    }
}