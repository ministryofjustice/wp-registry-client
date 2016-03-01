<?php

/**
 * WP Registry plugin class.
 */

namespace MOJDigital\WP_Registry\Client;

use MOJDigital\WP_Registry\Client\ScheduledTasks\AnnounceToRegistry;
use MOJDigital\WP_Registry\Client\ScheduledTasks\BaseScheduledTask;

class Plugin
{
    /**
     * URL for accessing the WP Registry server
     * @var string
     */
    public $registryUrl = null;

    /**
     * Identifier for this WordPress install
     * @var string
     */
    public $siteId = null;

    /**
     * Array of scheduled task objects
     * @var BaseScheduledTask[]
     */
    public $scheduledTasks = [];

    /**
     * Plugin constructor.
     * @param string $pluginFile Path to the WordPress plugin file
     * @param string $registryUrl
     * @param string $siteId
     */
    public function __construct($pluginFile, $registryUrl, $siteId)
    {
        $this->registryUrl = $registryUrl;
        $this->siteId = $siteId;
        $this->registerActivationHooks($pluginFile);
        $this->constructScheduledTasks();
        $this->registerHooksForScheduledTasks();
    }

    /**
     * Register plugin activation and deactivation hooks.
     * @param string $pluginFile
     */
    public function registerActivationHooks($pluginFile)
    {
        register_activation_hook($pluginFile, array($this, 'addScheduledTasksToCron'));
        register_deactivation_hook($pluginFile, array($this, 'removeScheduledTasksFromCron'));
    }

    /**
     * Construct scheduled task objects and store them
     * in array $this->scheduledTasks
     */
    public function constructScheduledTasks() {
        $tasks = [];
        $tasks[] = new AnnounceToRegistry($this->registryUrl, $this->siteId);
        $this->scheduledTasks = $tasks;
    }

    /**
     * Register execution hooks for scheduled tasks.
     */
    public function registerHooksForScheduledTasks() {
        foreach ($this->scheduledTasks as $task) {
            $task->registerHook();
        }
    }

    /**
     * Add scheduled tasks to wp-cron.
     */
    public function addScheduledTasksToCron()
    {
        foreach ($this->scheduledTasks as $task) {
            if (!$task->isScheduled()) {
                $task->scheduleTask();
            }
        }
    }

    /**
     * Remove scheduled tasks from wp-cron.
     */
    public function removeScheduledTasksFromCron()
    {
        foreach ($this->scheduledTasks as $task) {
            if ($task->isScheduled()) {
                $task->removeScheduledTask();
            }
        }
    }
}
