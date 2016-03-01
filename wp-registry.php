<?php

/**
 * Plugin name: WP Registry (client)
 * Description: Client plugin for WP Registry
 * Version: develop
 * Author: Ollie Treend <ollie.treend@digital.justice.gov.uk>
 */

namespace MOJDigital\WP_Registry\Client;

require 'autoload.php';

// Instantiate the class and register hooks
if (defined('WP_REGISTRY_ENABLED') && WP_REGISTRY_ENABLED) {
    $WP_Registry_Client = new Plugin(__FILE__, WP_REGISTRY_URL, WP_REGISTRY_SITE_ID);
}
