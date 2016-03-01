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
    $tasks = [
        new ScheduledTasks\AnnounceToRegistry(WP_REGISTRY_URL, WP_REGISTRY_SITE_ID),
    ];

    $wpFunctions = new WordPressGlobalFunctionsInvoker();

    $WP_Registry_Client = new Plugin($tasks);
    $WP_Registry_Client->registerActivationHooks(__FILE__);
    $WP_Registry_Client->registerHooksForScheduledTasks();

}
