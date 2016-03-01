<?php

namespace MOJDigital\WP_Registry\Client;

class PluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Generate an array of 2 mock scheduled tasks.
     * @return array
     */
    private function getArrayOfMockScheduledTasks()
    {
        $tasks = [];
        for ($i = 0; $i < 2; $i++) {
            $tasks[] = $this->getMockBuilder('\MOJDigital\WP_Registry\Client\ScheduledTasks\BaseScheduledTask')
                ->disableOriginalConstructor()
                ->getMock();
        }
        return $tasks;
    }

    /**
     * @covers \MOJDigital\WP_Registry\Client\Plugin::__construct
     * @uses   \MOJDigital\WP_Registry\Client\Plugin
     */
    public function testCanBeConstructed()
    {
        $tasks = $this->getArrayOfMockScheduledTasks();
        $p = new Plugin($tasks);
        $this->assertInstanceOf(Plugin::class, $p);
        return $p;
    }

    public function testRegistersActivationHooks()
    {
        $wp = $this->getMockBuilder('MOJDigital\WP_Registry\Client\WordPressGlobalFunctionsInvoker')
            ->setMethods(['register_activation_hook', 'register_deactivation_hook'])
            ->getMock();

        $wp->expects($this->atLeastOnce())
            ->method('register_activation_hook');

        $wp->expects($this->atLeastOnce())
            ->method('register_deactivation_hook');

        $tasks = $this->getArrayOfMockScheduledTasks();
        $p = new Plugin($tasks);
        $p->setWordPressGlobalFunctionsInvoker($wp);
        $p->registerActivationHooks(__FILE__);
    }

    /**
     * @covers \MOJDigital\WP_Registry\Client\Plugin::registerHooksForScheduledTasks
     * @uses   \MOJDigital\WP_Registry\Client\Plugin
     */
    public function testRegistersHooksForScheduledTasks()
    {
        $tasks = $this->getArrayOfMockScheduledTasks();

        // Add expectations to mock objects
        foreach ($tasks as $task) {
            $task->expects($this->once())
                ->method('registerHook');
        }

        $p = new Plugin($tasks);
        $p->registerHooksForScheduledTasks();
    }

    /**
     * @covers \MOJDigital\WP_Registry\Client\Plugin::addScheduledTasksToCron
     * @uses   \MOJDigital\WP_Registry\Client\Plugin
     */
    public function testAddsScheduledTasksToCron()
    {
        $tasks = $this->getArrayOfMockScheduledTasks();

        // $tasks[0] mocks task which is already scheduled in wp-cron
        $tasks[0]->method('isScheduled')
            ->willReturn(true);
        $tasks[0]->expects($this->never())
            ->method('scheduleTask');

        // $tasks[1] mocks a task which is not yet scheduled in wp-cron
        $tasks[1]->method('isScheduled')
            ->willReturn(false);
        $tasks[1]->expects($this->once())
            ->method('scheduleTask');

        $p = new Plugin($tasks);
        $p->addScheduledTasksToCron();
    }

    /**
     * @covers \MOJDigital\WP_Registry\Client\Plugin::removeScheduledTasksFromCron
     * @uses   \MOJDigital\WP_Registry\Client\Plugin
     */
    public function testRemovesScheduledTasksFromCron()
    {
        $tasks = $this->getArrayOfMockScheduledTasks();

        // $tasks[0] mocks task which is already scheduled in wp-cron
        $tasks[0]->method('isScheduled')
            ->willReturn(true);
        $tasks[0]->expects($this->once())
            ->method('removeScheduledTask');

        // $tasks[1] mocks a task which is not yet scheduled in wp-cron
        $tasks[1]->method('isScheduled')
            ->willReturn(false);
        $tasks[1]->expects($this->never())
            ->method('removeScheduledTask');

        $p = new Plugin($tasks);
        $p->removeScheduledTasksFromCron();
    }
}
