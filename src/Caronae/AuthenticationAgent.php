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
        Log::info('Checando autenticação com CAS...');
        $this->id_ufrj = $this->cas->authenticate();
        Log::info('Autenticação com CAS concluída.');
    }

    public function fetchUserProfileFromSIGA()
    {
        Log::info('Buscando perfil no SIGA...');
        $this->siga_user = $this->siga->getProfileById($this->id_ufrj);
        Log::info('Carregou perfil do SIGA.');
    }

    public function sendUserToCaronae()
    {
        Log::info('Enviando perfil para o Caronaê...');
        $user = $this->adaptor->convertToCaronaeUser($this->siga_user);
        $this->caronae->authorize($user);
        Log::info('Perfil enviado para o Caronaê.');
    }

    public function redirectToCaronae($error_reason = null)
    {
        if (empty($error_reason)) {
            $redirect_url = $this->caronae->redirectUrlForSuccess();
        } else {
            $redirect_url = $this->caronae->redirectUrlForError($error_reason);
        }

        Log::info('Redirecionando para o Caronaê.', [
            'error_reason' => $error_reason,
            'redirect_url' => $redirect_url,
        ]);

        header('Location: ' . $redirect_url, true, 302);
        die;
    }
}