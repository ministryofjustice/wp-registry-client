<?php

namespace MOJDigital\WP_Registry\Client\ScheduledTasks;

class BaseScheduledTask
{
    /**
     * The first time that you want the event to occur. This must be in a UNIX timestamp format.
     * @var int
     */
    public $timestamp = null;

    /**
     * How often the event should reoccur.
     * Valid values: hourly, twicedaily, daily
     * @var string
     */
    public $recurrence = 'daily';

    /**
     * The name of an action hook to execute.
     * Assigned dynamically in class constructor.
     * @var string
     */
    public $hook = null;

    /**
     * Arguments to pass to the hook function(s). Optional.
     * @var array
     */
    public $args = [];

    /**
     * BaseScheduledTask constructor.
     */
    public function __construct()
    {
        $this->hook = get_class($this);
        $this->timestamp = time();
    }

    /**
     * Register WordPress action to handle execution of the task.
     */
    public function registerHook()
    {
        add_action($this->hook, array($this, 'execute'), 10, count($this->args));
    }

    /**
     * Check if the task already exists in wp-cron.
     * @return bool
     */
    public function isScheduled()
    {
        $nextScheduled = wp_next_scheduled($this->hook, $this->args);
        return $nextScheduled !== false;
    }

    /**
     * Add the task to wp-cron.
     */
    public function scheduleTask()
    {
        wp_schedule_event($this->timestamp, $this->recurrence, $this->hook);

    }

    /**
     * Remove the task from wp-cron.
     */
    public function removeScheduledTask()
    {
        wp_clear_scheduled_hook($this->hook, $this->args);
    }

    /**
     * Executed when the cron task runs.
     * Stub to be extended by child classes.
     */
    public function execute()
    {
        // Called when the scheduled task runs.
    }
}
