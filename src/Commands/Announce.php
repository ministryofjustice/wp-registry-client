<?php

namespace MOJDigital\WP_Registry\Client\Commands;

use MOJDigital\WP_Registry\Client\Traits\UsesWordPressGlobalFunctionsInvoker;

class Announce
{
    use UsesWordPressGlobalFunctionsInvoker;

    /**
     * Site identifier string used at WP Registry.
     * @var string
     */
    public $siteId = null;

    public function __construct($siteId)
    {
        $this->siteId = $siteId;
    }

    public function execute()
    {
        $response = [
            'site_name' => $this->wp()->get_bloginfo('name'),
            'site_id' => $this->siteId,
            'url' => $this->wp()->get_bloginfo('url'),
            'wordpress_version' => $this->wp()->get_bloginfo('version'),
            'plugins' => $this->getPlugins(),
        ];
        return $response;
    }

    /**
     * Get info about installed plugins.
     * For each plugin, the following keys are provided:
     *  - name
     *  - slug
     *  - version
     *  - mu: is this a mu-plugin?
     *  - active: is this plugin active?
     *
     * @return array
     */
    private function getPlugins()
    {
        $plugins = array_map(function($plugin) {
            $plugin['mu'] = false;
            return $plugin;
        }, $this->wp()->get_plugins());

        $mu_plugins = array_map(function($plugin) {
            $plugin['mu'] = true;
            return $plugin;
        }, $this->wp()->get_mu_plugins());

        $plugins = array_merge($plugins, $mu_plugins);
        $response = [];

        foreach ($plugins as $file => $plugin) {
            $response[] = [
                'name' => $plugin['Name'],
                'slug' => basename($file, '.php'),
                'version' => $plugin['Version'],
                'mu' => $plugin['mu'],
                'active' => ($plugin['mu']) ? true : $this->wp()->is_plugin_active($file),
            ];
        }

        return $response;
    }
}
