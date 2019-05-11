<?php

namespace Caronae\UFRJ;

class CASServiceTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function shouldSetAuthenticationURLWithForwardedProtoHeader()
    {
        $_SERVER = [
            'HTTP_X_FORWARDED_PROTO' => 'https',
            'HTTP_HOST' => 'example',
            'REQUEST_URI' => '/route',
        ];

        $cas = new CASService();
        $this->assertEquals('https://example/route', $cas->getServiceURL());
    }

    /** @test */
    public function shouldSetAuthenticationURLWithCloudFrontProtoHeader()
    {
        $_SERVER = [
            'HTTP_CLOUDFRONT_FORWARDED_PROTO' => 'https',
            'HTTP_HOST' => 'example',
            'REQUEST_URI' => '/route',
        ];

        $cas = new CASService();
        $this->assertEquals('https://example/route', $cas->getServiceURL());
    }
}
