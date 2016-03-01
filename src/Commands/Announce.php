<?php

namespace MOJDigital\WP_Registry\Client\Commands;

class Announce
{
    public function __construct($siteId)
    {
        $this->siteId = $siteId;
    }

    public function execute()
    {
        $response = [
            'site_name' => get_bloginfo('name'),
            'site_id' => $this->siteId,
            'url' => get_bloginfo('url'),
            'wordpress_version' => get_bloginfo('version'),
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
        if (!function_exists('get_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $plugins = array_map(function($plugin) {
            $plugin['mu'] = false;
            return $plugin;
        }, get_plugins());

        $mu_plugins = array_map(function($plugin) {
            $plugin['mu'] = true;
            return $plugin;
        }, get_mu_plugins());

        $plugins = array_merge($plugins, $mu_plugins);
        $response = [];

        foreach ($plugins as $file => $plugin) {
            $response[] = [
                'name' => $plugin['Name'],
                'slug' => basename($file, '.php'),
                'version' => $plugin['Version'],
                'mu' => $plugin['mu'],
                'active' => ($plugin['mu']) ? true : is_plugin_active($file),
            ];
        }

        return $response;
    }
}
