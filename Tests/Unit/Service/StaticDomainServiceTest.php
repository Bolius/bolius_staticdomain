<?php
use Bolius\BoliusStaticdomain\Service\StaticDomainService;

/**
 * Class StaticDomainServiceTest
 */
class StaticDomainServiceTest extends PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function testStaticDomainService()
    {

        $domainRecord = StaticDomainService::getStaticDomainRecord();
        $this->assertInternalType('array', $domainRecord);

        $domainName = StaticDomainService::getStaticDomainName();
        $this->assertInternalType('string', $domainName);

        $this->assertNotEmpty($domainName);

        // without host
        $url = StaticDomainService::appendDomainToUrl('foo/bar.css', 'boliusstatic.dk');
        $this->assertEquals('//boliusstatic.dk/foo/bar.css', $url);

        // with host without forcing
        $url = StaticDomainService::appendDomainToUrl('//www.bolius.dk/foo/bar.css', 'boliusstatic.dk');
        $this->assertEquals('//www.bolius.dk/foo/bar.css', $url);

        // with host and forcing
        $url = StaticDomainService::appendDomainToUrl('http://www.bolius.dk/foo/bar.css?t=123#456', 'boliusstatic.dk', TRUE);
        $this->assertEquals('http://boliusstatic.dk/foo/bar.css?t=123#456', $url);

        // with host and forcing and removal of scheme
        $url = StaticDomainService::appendDomainToUrl('http://www.bolius.dk/foo/bar.css?t=123#456', 'boliusstatic.dk', TRUE, '');
        $this->assertEquals('//boliusstatic.dk/foo/bar.css?t=123#456', $url);

        // with host and forcing and force https
        $url = StaticDomainService::appendDomainToUrl('http://www.bolius.dk/foo/bar.css?t=123#456', 'boliusstatic.dk', TRUE, 'https');
        $this->assertEquals('https://boliusstatic.dk/foo/bar.css?t=123#456', $url);
    }
}
