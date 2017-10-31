<?php

/**
 * Class ServerSetupTest
 */
class ServerSetupTest extends PHPUnit_Framework_TestCase
{

    protected $scheme = 'http';
    protected $path = '/typo3conf/ext/bolius_staticdomain/Resources/Public/Testpages/StripCookieHeader.php?bypassStaticFilter';

    public function setup ()
    {

    }
    
    /**
     * @test
     */
    public function testCookieNotStripped ()
    {

    }

    /**
     * @test
     */
    public function testCookieStripped ()
    {

        // http://boliusstatic.dk/typo3conf/ext/bolius_staticdomain/Resources/Public/Testpages/StripSetCookieHeader.php?bypassStaticFilter



    }

    /**
     * @test
     */
    public function testSetcookieStripped ()
    {

        $domain = \Bolius\BoliusStaticdomain\Service\StaticDomainService::getStaticDomainName();


        $curl = curl_init($this->scheme . '://' . $domain . $this->path);

        curl_setopt_array($curl, [
            CURLOPT_HEADER => TRUE,
            CURLOPT_HTTPHEADER => [
                'Cookie: Test=testvalue',
                "Cache-Control: no-cache",
                "Pragma: no-cache",
            ],
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_TIMEOUT => 60



        ]);

        $data = curl_exec($curl);

        print_r($data);


    }

    public function testSetcookieNotStripped ()
    {

    }

    public function testVarnishPass ()
    {

    }

    public function testVarnishBypassed ()
    {

    }

    public function testVarnishHit ()
    {

    }
}