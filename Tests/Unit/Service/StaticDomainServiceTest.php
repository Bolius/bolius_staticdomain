<?php
use Bolius\BoliusStaticdomain\Service\StaticDomainService;

/**
 * Class StaticDomainServiceTest
 */
class StaticDomainServiceTest extends PHPUnit_Framework_TestCase
{

    public function setup ()
    {
        $GLOBALS['TSFE']->sys_page = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Frontend\Page\PageRepository::class);
    }


    /**
     * @test
     */
    public function testStaticDomainService()
    {

//        $domainRecord = StaticDomainService::getStaticDomainRecord(1);
//        $this->assertInternalType('array', $domainRecord);

        $domainRecord = StaticDomainService::getStaticDomainRecord(35);
        $this->assertInternalType('array', $domainRecord);

        $domainName = StaticDomainService::getStaticDomainName(1);
        $this->assertInternalType('string', $domainName);

        $domainName = StaticDomainService::getStaticDomainName(35);

        $this->assertInternalType('string', $domainName);
        $this->assertNotEmpty($domainName);

        // without host, relative
        $url = StaticDomainService::appendDomainToUrl('foo/bar.css', 'boliusstatic.dk');
        $this->assertContains('//boliusstatic.dk/foo/bar.css', $url);

        // without host, absolute
        $url = StaticDomainService::appendDomainToUrl('/foo/bar.css', 'boliusstatic.dk');
        $this->assertEquals('//boliusstatic.dk/foo/bar.css', $url);

        // with host without forcing
        $url = StaticDomainService::appendDomainToUrl('//www.bolius.dk/foo/bar.css', 'boliusstatic.dk');
        $this->assertEquals('//www.bolius.dk/foo/bar.css', $url);

        // with host and forcing
        $url = StaticDomainService::appendDomainToUrl('http://www.bolius.dk/foo/bar.css?t=123#456', 'boliusstatic.dk', ['www.bolius.dk']);
        $this->assertEquals('http://boliusstatic.dk/foo/bar.css?t=123#456', $url);

        // with host and forcing and removal of scheme
        $url = StaticDomainService::appendDomainToUrl('http://www.bolius.dk/foo/bar.css?t=123#456', 'boliusstatic.dk', ['www.bolius.dk'], '');
        $this->assertEquals('//boliusstatic.dk/foo/bar.css?t=123#456', $url);

        // with host and forcing and force https
        $url = StaticDomainService::appendDomainToUrl('http://www.bolius.dk/foo/bar.css?t=123#456', 'boliusstatic.dk', ['www.bolius.dk'], 'https');
        $this->assertEquals('https://boliusstatic.dk/foo/bar.css?t=123#456', $url);
    }
}
