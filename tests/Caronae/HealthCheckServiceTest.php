<?php

namespace Caronae;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class HealthCheckServiceTest extends \PHPUnit_Framework_TestCase
{

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
}
