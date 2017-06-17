<?php

namespace Caronae;

class CaronaeUFRJAgent
{
    private $siga;
    private $caronae;
    private $adaptor;
    private $caronae_user;

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

        $this->caronae_user = $this->caronae->signUp($user);
    }

    public function getCaronaeToken()
    {
        return $this->caronae_user->token;
    }
}