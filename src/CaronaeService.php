<?php

namespace Caronae;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

class CaronaeService
{
    const PRODUCTION_API_URL = "https://api.caronae.com.br";
    const DEVELOPMENT_API_URL = "https://dev.caronae.com.br";

    private $client;
    private $institutionID;
    private $institutionPassword;

    public function __construct(string $apiURL = PRODUCTION_API_URL, Client $client = null)
    {
        if ($client == null) {
            $client = new Client([
                'base_uri' => $apiURL,
                'timeout' => 15.0,
            ]);
        }

        $this->client = $client;
    }

    public function setInstitution(string $institutionID, string $institutionPassword)
    {
        $this->institutionID = $institutionID;
        $this->institutionPassword = $institutionPassword;
    }

    public function signUp($user)
    {
        $this->verifyInstitutionWasSet();

        try {
            $response = $this->client->post('/users', ['json' => $user, 'auth' => $this->authorization()]);
        } catch (RequestException $e) {
            throw new CaronaeException($e->getMessage());
        }

        if (!$this->isResponseValid($response)) {
            throw new CaronaeException("Invalid response from Caronae API (status code: " . $response->getStatusCode() . ")");
        }

        return json_decode($response->getBody());
    }

    private function authorization()
    {
        return [ $this->institutionID, $this->institutionPassword ];
    }

    private function verifyInstitutionWasSet()
    {
        if (empty($this->institutionID) || empty($this->institutionPassword)) {
            throw new CaronaeException("You need to set the Caronae institution before making calls.");
        }
    }

    private function isResponseValid(ResponseInterface $response)
    {
        $statusCode = $response->getStatusCode();
        return $statusCode >= 200 && $statusCode < 300;
    }
}

class CaronaeException extends \Exception {}
