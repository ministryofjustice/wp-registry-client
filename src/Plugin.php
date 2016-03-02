<?php

/**
 * WP Registry plugin class.
 */

namespace MOJDigital\WP_Registry\Client;

use MOJDigital\WP_Registry\Client\ScheduledTasks\BaseScheduledTask;
use MOJDigital\WP_Registry\Client\Traits\UsesWordPressGlobalFunctionsInvoker;

class Plugin
{
    use UsesWordPressGlobalFunctionsInvoker;

    /**
     * Array of scheduled task objects
     * @var BaseScheduledTask[]
     */
    public $scheduledTasks = [];

    /**
     * Plugin constructor.
     * @param BaseScheduledTask[] $tasks Scheduled task objects
     */
    public function __construct($tasks)
    {
        $this->scheduledTasks = $tasks;
    }

    /**
     * Register plugin activation and deactivation hooks.
     * @param string $pluginFile
     */
    public function registerActivationHooks($pluginFile)
    {
        $this->wp()->register_activation_hook($pluginFile, array($this, 'addScheduledTasksToCron'));
        $this->wp()->register_deactivation_hook($pluginFile, array($this, 'removeScheduledTasksFromCron'));
    }

    /**
     * Register execution hooks for scheduled tasks.
     */
    public function registerHooksForScheduledTasks()
    {
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
