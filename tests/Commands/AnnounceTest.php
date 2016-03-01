<?php

namespace MOJDigital\WP_Registry\Client\Commands;

class AnnounceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Holds the object being tested
     * @var Announce
     */
    private $object = null;

    private $siteId = 'phpunit';

    /**
     * Setup method run before each test.
     */
    public function setUp()
    {
        $this->object = new Announce($this->siteId);
    }

    /**
     *
     */
    public function testCanExecute()
    {
        $mockSite = [
            'name'    => 'PHPUnit Mock',
            'url'     => 'http://phpunitmock.com',
            'version' => '4.4.2',
        ];

        $wp = $this->getMockBuilder('MOJDigital\WP_Registry\Client\WordPressGlobalFunctionsInvoker')
            ->setMethods([
                'get_bloginfo',
                'get_plugins',
                'get_mu_plugins',
                'is_plugin_active',
            ])
            ->getMock();

        $wp->method('get_bloginfo')
            ->will($this->returnValueMap([
                ['name', $mockSite['name']],
                ['url', $mockSite['url']],
                ['version', $mockSite['version']],
            ]));

        $wp->method('get_plugins')
            ->will($this->returnValue([]));

        $wp->method('get_mu_plugins')
            ->will($this->returnValue([]));

        $wp->method('is_plugin_active')
            ->will($this->returnValue(false));

        $this->object->setWordPressGlobalFunctionsInvoker($wp);
        $announce = $this->object->execute();

        // Assert that the correct values are returned
        $this->assertEquals($mockSite['name'],    $announce['site_name']);
        $this->assertEquals($this->siteId,        $announce['site_id']);
        $this->assertEquals($mockSite['url'],     $announce['url']);
        $this->assertEquals($mockSite['version'], $announce['wordpress_version']);
        $this->assertArrayHasKey('plugins', $announce);

        // @TODO: Add mocks and assertions for plugins response

    }
}
