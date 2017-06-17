<?php

namespace Caronae;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

define('SIGA_SEARCH_URL', 'http://146.164.2.117:9200/_search');

class SigaService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'timeout'  => 15.0,
        ]);
    }

    public function getProfileById($id)
    {
        try {
            $response = $this->client->get(SIGA_SEARCH_URL, ['query' => ['q' => 'IdentificacaoUFRJ:' . $id]]);
        } catch (RequestException $e) {
            throw new SigaException('Failed to connect to SIGA.');
        }

        // Decode JSON
        $intranetResponse = json_decode($response->getBody());
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new SigaException('Error decoding response from SIGA.');
        }

        // Check if we found a hit
        if (empty($intranetResponse->hits->hits)) {
            throw new SigaException('No UFRJ profile found with this identification.');
        }

        $intranetUser = $intranetResponse->hits->hits[0]->_source;

        // Check if the extracted user has all the required fields
        if (!isset($intranetUser->nome) || !isset($intranetUser->nomeCurso) ||
            !isset($intranetUser->situacaoMatricula) || !isset($intranetUser->nivel)) {
            throw new SigaException('Unexpected response from SIGA.');
        }

        // Check if the user is still enrolled
        if ($intranetUser->situacaoMatricula != "Ativa") {
            throw new SigaException('User does not have an active profile from SIGA.');
        }

        return $intranetUser;
    }
}

class SigaException extends \Exception {}
