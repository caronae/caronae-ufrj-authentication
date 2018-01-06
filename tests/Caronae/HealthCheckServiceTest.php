<?php

namespace Caronae;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class HealthCheckServiceTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();
        putenv('SIGA_SEARCH_URL=http://example.com/search');
    }

    /** @test */
    public function shouldBeTrueWhenConnectionWithCASIsOk()
    {
        $mock = new MockHandler([new Response(200)]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $healthCheckService = new HealthCheckService($client);

        $this->assertTrue($healthCheckService->checkCASConnection());
    }

    /** @test */
    public function shouldBeFalseWhenConnectionWithCASFails()
    {
        $mock = new MockHandler([new Response(403)]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $healthCheckService = new HealthCheckService($client);

        $this->assertFalse($healthCheckService->checkCASConnection());
    }

    /** @test */
    public function shouldBeTrueWhenConnectionWithSigaIsOk()
    {
        $mock = new MockHandler([new Response(200)]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $healthCheckService = new HealthCheckService($client);

        $this->assertTrue($healthCheckService->checkSigaConnection());
    }

    /** @test */
    public function shouldBeFalseWhenConnectionWithSigaFails()
    {
        $mock = new MockHandler([new RequestException('Error', new Request('GET', '/'))]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $healthCheckService = new HealthCheckService($client);

        $this->assertFalse($healthCheckService->checkSigaConnection());
    }
}
