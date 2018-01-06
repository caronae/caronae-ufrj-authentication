<?php

namespace Caronae\UFRJ;

class CASServiceTest extends \PHPUnit_Framework_TestCase
{
    /** @test® */
    public function shouldSetAuthenticationURL()
    {
        $_SERVER = [
            'HTTP_X_FORWARDED_PROTO' => 'https',
            'HTTP_HOST' => 'example',
            'REQUEST_URI' => '/route',
        ];

        $cas = new CASService();
        $this->assertEquals('https://example/route', $cas->getServiceURL());
    }
}
