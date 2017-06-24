<?php

namespace Caronae;

class CaronaeUFRJAgent
{
    private $siga;
    private $caronae;
    private $adaptor;
    private $redirect_url;

    public function __construct()
    {
        $this->siga = new SigaService;

        $this->caronae = new CaronaeService(CARONAE_API_URL);
        $this->caronae->setInstitution(CARONAE_INSTITUTION_ID, CARONAE_INSTITUTION_PASSWORD);

        $this->adaptor = new CaronaeSigaAdaptor;
    }

    public function createOrUpdateUserWithUfrjId(string $id_ufrj)
    {
        $siga_user = $this->siga->getProfileById($id_ufrj);
        $user = $this->adaptor->convertToCaronaeUser($siga_user);

        $response = $this->caronae->signUp($user);
        $this->redirect_url = $response->redirect_url;

        $this->redirectToCaronae();
    }

    private function redirectToCaronae()
    {
        header('Location: ' . $this->redirect_url, true, 302);
        die;
    }
}