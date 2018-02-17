<?php

namespace Caronae\UFRJ;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class SigaService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 5.0,
        ]);
    }

    public function getProfileById($id)
    {
        try {
            $response = $this->client->get(getenv('SIGA_SEARCH_URL'), ['query' => ['q' => 'IdentificacaoUFRJ:' . $id]]);
        } catch (RequestException $e) {
            throw new SigaConnectionException();
        }

        // Decode JSON
        $intranetResponse = json_decode($response->getBody());
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new SigaUnexpectedResponseException();
        }

        // Check if we found a hit
        if (empty($intranetResponse->hits->hits)) {
            throw new SigaUserNotFoundException();
        }

        $intranetUser = $intranetResponse->hits->hits[0]->_source;

        // Check if the extracted user has all the required fields
        if (!isset($intranetUser->nome) || !isset($intranetUser->nomeCurso) ||
            !isset($intranetUser->situacaoMatricula) || !isset($intranetUser->nivel)) {
            throw new SigaUnexpectedResponseException();
        }

        // Check if the user is still enrolled
        if ($intranetUser->situacaoMatricula != "Ativa") {
            throw new SigaUserNotEnrolledException($intranetUser->situacaoMatricula);
        }

        return $intranetUser;
    }
}

class SigaException extends \Exception {}

class SigaConnectionException extends SigaException {
    protected $message = 'Não foi possível conectar ao SIGA.';
}

class SigaUnexpectedResponseException extends SigaException {
    protected $message = 'O SIGA retornou uma resposta inesperada.';
}

class SigaUnauthorizedException extends SigaException {}

class SigaUserNotFoundException extends SigaUnauthorizedException {
    protected $message = 'Não foi possível localizar o usuário no SIGA.';
}

class SigaUserNotEnrolledException extends SigaUnauthorizedException {
    public function __construct($enrollment_status) {
        $this->message = 'O usuário não possui matrícula ativa no SIGA. Situação da matrícula: ' . $enrollment_status . '.';
    }
}

