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
     * Test that the command execution output is what we expect.
     * @covers Announce::execute()
     */
    public function testCanExecute()
    {
        $mockSite = [
            'name' => 'PHPUnit Mock',
            'url' => 'http://phpunitmock.com',
            'version' => '4.4.2',
        ];

        $mockPlugins = [
            'akismet/akismet.php' => [
                'Name' => 'Akismet',
                'PluginURI' => 'http://akismet.com/',
                'Version' => '3.1.6',
                'Description' => 'Used by millions, Akismet is quite possibly the best way in the world to ' .
                                 '<strong>protect your blog from spam</strong>. It keeps your site protected even ' .
                                 'while you sleep. To get started: 1) Click the "Activate" link to the left of this ' .
                                 'description, 2) <a href="http://akismet.com/get/">Sign up for an Akismet plan</a> ' .
                                 'to get an API key, and 3) Go to your Akismet configuration page, and save your ' .
                                 'API key.',
                'Author' => 'Automattic',
                'AuthorURI' => 'http://automattic.com/wordpress-plugins/',
                'TextDomain' => 'akismet',
                'DomainPath' => '',
                'Network' => false,
                'Title' => 'Akismet',
                'AuthorName' => 'Automattic',
            ],
            'wp-bcrypt/wp-bcrypt.php' => [
                'Name' => 'wp-bcrypt',
                'PluginURI' => 'http://wordpress.org/plugins/wp-bcrypt/',
                'Version' => '1.0.1',
                'Description' => 'wp-bcrypt switches WordPress\'s password hashes from MD5 to bcrypt, making it ' .
                                 'harder for them to be brute-forced if they are leaked.',
                'Author' => 'dxw',
                'AuthorURI' => 'http://dxw.com',
                'TextDomain' => '',
                'DomainPath' => '',
                'Network' => false,
                'Title' => 'wp-bcrypt',
                'AuthorName' => 'dxw',
            ],
        ];

        $mockMuPlugins = [
            'disallow-indexing.php' => [
                'Name' => 'Disallow Indexing',
                'PluginURI' => 'https://roots.io/bedrock/',
                'Version' => '1.0.0',
                'Description' => 'Disallow indexing of your site on non-production environments.',
                'Author' => 'Roots',
                'AuthorURI' => 'https://roots.io/',
                'TextDomain' => '',
                'DomainPath' => '',
                'Network' => false,
                'Title' => 'Disallow Indexing',
                'AuthorName' => 'Roots',
            ],
            'register-theme-directory.php' => [
                'Name' => 'Register Theme Directory',
                'PluginURI' => 'https://roots.io/bedrock/',
                'Version' => '1.0.0',
                'Description' => 'Register default theme directory',
                'Author' => 'Roots',
                'AuthorURI' => 'https://roots.io/',
                'TextDomain' => '',
                'DomainPath' => '',
                'Network' => false,
                'Title' => 'Register Theme Directory',
                'AuthorName' => 'Roots',
            ],
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
            ->will($this->returnValue($mockPlugins));

        $wp->method('get_mu_plugins')
            ->will($this->returnValue($mockMuPlugins));

        $wp->method('is_plugin_active')
            ->will($this->returnValueMap([
                ['akismet/akismet.php', true],
                ['wp-bcrypt/wp-bcrypt.php', false],
                ['disallow-indexing.php', true],
                ['register-theme-directory.php', true],
            ]));

        $this->object->setWordPressGlobalFunctionsInvoker($wp);
        $announce = $this->object->execute();

        // Assert that the expected keys are returned
        $keys = array_keys($announce);
        $expectedKeys = [
            'site_name',
            'site_id',
            'url',
            'wordpress_version',
            'plugins',
        ];
        sort($keys);
        sort($expectedKeys);
        $this->assertEquals($expectedKeys, $keys);

        // Assert that the expected values are returned
        $this->assertEquals($mockSite['name'], $announce['site_name']);
        $this->assertEquals($this->siteId, $announce['site_id']);
        $this->assertEquals($mockSite['url'], $announce['url']);
        $this->assertEquals($mockSite['version'], $announce['wordpress_version']);

        // Assert that plugins are correctly formatted
        $this->assertCount(4, $announce['plugins'], 'Incorrect number of plugins in output.');
        foreach ($announce['plugins'] as $plugin) {
            // Assert that the expected keys are returned for this plugin
            $keys = array_keys($plugin);
            $expectedKeys = [
                'name',
                'slug',
                'version',
                'mu',
                'active',
            ];
            sort($keys);
            sort($expectedKeys);
            $this->assertEquals($expectedKeys, $keys, "{$plugin['name']}: unexpected array keys");

            // Assert that values are what we expect
            switch ($plugin['name']) {
                case 'Akismet':
                    $mock = $mockPlugins['akismet/akismet.php'];
                    $this->assertFalse($plugin['mu'], "{$plugin['name']}: expected mu to be false");
                    $this->assertTrue($plugin['active'], "{$plugin['name']}: expected active to be true");
                    break;
                case 'wp-bcrypt':
                    $mock = $mockPlugins['wp-bcrypt/wp-bcrypt.php'];
                    $this->assertFalse($plugin['mu'], "{$plugin['name']}: expected mu to be false");
                    $this->assertFalse($plugin['active'], "{$plugin['name']}: expected active to be false");
                    break;
                case 'Disallow Indexing':
                    $mock = $mockMuPlugins['disallow-indexing.php'];
                    $this->assertTrue($plugin['mu'], "{$plugin['name']}: expected mu to be true");
                    $this->assertTrue($plugin['active'], "{$plugin['name']}: expected active to be true");
                    break;
                case 'Register Theme Directory':
                    $mock = $mockMuPlugins['register-theme-directory.php'];
                    $this->assertTrue($plugin['mu'], "{$plugin['name']}: expected mu to be true");
                    $this->assertTrue($plugin['active'], "{$plugin['name']}: expected active to be true");
                    break;
            }
            $this->assertEquals($mock['Version'], $plugin['version'], "{$plugin['name']}: unexpected version");
        }
    }
}
