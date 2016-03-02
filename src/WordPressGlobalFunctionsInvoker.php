<?php

/**
 * Wrapper class for WordPress global functions.
 * This is required to allow for effective unit testing of
 * plugin functionality.
 */

namespace MOJDigital\WP_Registry\Client;

class WordPressGlobalFunctionsInvoker
{
    public function register_activation_hook()
    {
        return call_user_func_array('register_activation_hook', func_get_args());
    }

    public function register_deactivation_hook()
    {
        return call_user_func_array('register_deactivation_hook', func_get_args());
    }

    public function get_bloginfo()
    {
        return call_user_func_array('get_bloginfo', func_get_args());
    }

    public function get_plugins()
    {
        if (!function_exists('get_plugins')) {
            $this->requireWordPressPluginFunctions();
        }
        return call_user_func_array('get_plugins', func_get_args());
    }

    public function get_mu_plugins()
    {
        if (!function_exists('get_mu_plugins')) {
            $this->requireWordPressPluginFunctions();
        }
        return call_user_func_array('get_mu_plugins', func_get_args());
    }

    public function is_plugin_active()
    {
        if (!function_exists('is_plugin_active')) {
            $this->requireWordPressPluginFunctions();
        }
        return call_user_func_array('is_plugin_active', func_get_args());
    }

    public function add_action()
    {
        return call_user_func_array('add_action', func_get_args());
    }

    public function wp_next_scheduled()
    {
        return call_user_func_array('wp_next_scheduled', func_get_args());
    }

    public function wp_schedule_event()
    {
        return call_user_func_array('wp_schedule_event', func_get_args());
    }

    public function wp_clear_scheduled_hook()
    {
        return call_user_func_array('wp_clear_scheduled_hook', func_get_args());
    }

    public function wp_remote_post()
    {
        return call_user_func_array('wp_remote_post', func_get_args());
    }

    public function is_wp_error()
    {
        return call_user_func_array('is_wp_error', func_get_args());
    }

    private function requireWordPressPluginFunctions()
    {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
}
