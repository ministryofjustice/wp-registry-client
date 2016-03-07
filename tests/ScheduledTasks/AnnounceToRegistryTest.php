<?php

namespace MOJDigital\WP_Registry\Client\ScheduledTasks;

class AnnounceToRegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    private $registryUrl = null;

    /**
     * @var string
     */
    private $siteId = null;

    /**
     * @var array
     */
    private $mockAnnounce = null;

    /**
     * The object we're testing.
     * @var AnnounceToRegistry
     */
    private $object = null;

    public function setUp()
    {
        $this->registryUrl = 'https://example.com';
        $this->siteId = 'phpunit';

        $this->mockAnnounce = [
            'site_name' => 'PHPUnit Mock',
            'site_id' => $this->siteId,
            'url' => 'http://phpunitmock.com',
            'wordpress_version' => 'http://phpunitmock.com',
            'plugins' => [
                [
                    'name' => 'Akismet',
                    'slug' => 'akismet',
                    'version' => '3.1.6',
                    'mu' => false,
                    'active' => true,
                ],
            ],
        ];

        $announce = $this->getMockBuilder('\MOJDigital\WP_Registry\Client\Commands\Announce')
            ->disableOriginalConstructor()
            ->setMethods(['execute'])
            ->getMock();

        $announce->method('execute')
            ->will($this->returnValue($this->mockAnnounce));

        $this->object = new AnnounceToRegistry($this->registryUrl, $announce);
    }

    private function getMockWordPressGlobalFunctionsInvoker()
    {
        return $this->getMockBuilder('MOJDigital\WP_Registry\Client\WordPressGlobalFunctionsInvoker')
            ->setMethods([
                'wp_remote_post',
                'is_wp_error',
            ])
            ->getMock();
    }

    public function testCanExecute()
    {
        $wp = $this->getMockWordPressGlobalFunctionsInvoker();

        $mockResponse = [
            'headers' => [
                'connection' => 'close',
                'content-type' => 'application/json',
            ],
            'body' => 'SOMETHING', // @TODO
            'response' => [
                'code' => 200,
                'message' => 'OK',
            ],
            'cookies' => [],
            'filename' => null,
        ];

        $expectedUrl = $this->registryUrl . '/api/installs/announce';
        $expectedRequestArgs = [
            'body' => $this->mockAnnounce,
        ];

        $wp->method('wp_remote_post')
            ->with($this->equalTo($expectedUrl), $this->identicalTo($expectedRequestArgs))
            ->will($this->returnValue($mockResponse));

        $wp->method('is_wp_error')
            ->will($this->returnValue(false));

        $this->object->setWordPressGlobalFunctionsInvoker($wp);
        $this->object->execute();
    }
}
