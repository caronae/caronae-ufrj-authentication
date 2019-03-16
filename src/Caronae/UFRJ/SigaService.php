<?php

namespace Caronae\UFRJ;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class SigaService
{
    protected $client;
    private $searchURL;

    public function __construct(Client $client = null, $searchURL = null)
    {
        $this->searchURL = is_null($searchURL) ? getenv('SIGA_SEARCH_URL') : $searchURL;
        if ($client == null) {
            $client = new Client([
                'timeout' => 5.0,
            ]);
        }
        $this->client = $client;
    }

    public function getProfileById($id)
    {
        try {
            $response = $this->client->get($this->searchURL, ['query' => ['q' => 'IdentificacaoUFRJ:' . $id]]);
        } catch (RequestException $e) {
            throw new SigaConnectionException();
        }

        // Decode JSON
        $sigaResponse = json_decode($response->getBody());
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new SigaUnexpectedResponseException();
        }

        // Check if we found a hit
        if (empty($sigaResponse->hits->hits)) {
            throw new SigaUserNotFoundException();
        }

        $user = $sigaResponse->hits->hits[0]->_source;

        // Check if the extracted user has all the required fields
        if (!isset($user->nome) || !isset($user->nomeCurso) ||
            !isset($user->situacaoMatricula) || !isset($user->nivel)) {
            throw new SigaUnexpectedResponseException();
        }

        // Check if the student is still enrolled
        $isEmployee = isset($user->alunoServidor) && $user->alunoServidor == '1';
        if (!$isEmployee && !preg_match('/ativ[ao]/i', $user->situacaoMatricula)) {
            throw new SigaUserNotEnrolledException($user->situacaoMatricula);
        }

        return $user;
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

