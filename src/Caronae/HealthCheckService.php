<?php

namespace Caronae;

use Caronae\UFRJ\CASService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class HealthCheckService
{
    private $client;

    public function __construct(Client $client = null)
    {
        if ($client == null) {
            $this->client = new Client([
                'timeout' => 30.0,
            ]);
        } else {
            $this->client = $client;
        }
    }

    public function checkCASConnection()
    {
        try {
            $cas = new CASService();
            $casURL = 'https://' . CASService::CAS_HOSTNAME . '/login?service=' . $cas->getServiceURL();
            $response = $this->client->get($casURL);
        } catch (RequestException $e) {
            return false;
        }

        return $response->getStatusCode() == 200;
    }

    public function checkSigaConnection()
    {
        try {
            $sigaURL = getenv('SIGA_SEARCH_URL');
            $response = $this->client->get($sigaURL);
        } catch (RequestException $e) {
            return false;
        }

        return $response->getStatusCode() == 200;
    }
}
