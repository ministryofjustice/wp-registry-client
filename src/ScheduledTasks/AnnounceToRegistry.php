<?php

namespace MOJDigital\WP_Registry\Client\ScheduledTasks;

use MOJDigital\WP_Registry\Client\Commands\Announce;
use MOJDigital\WP_Registry\Client\Traits\UsesWordPressGlobalFunctionsInvoker;

class AnnounceToRegistry extends BaseScheduledTask
{
    use UsesWordPressGlobalFunctionsInvoker;

    /**
     * How often the event should reoccur.
     * Valid values: hourly, twicedaily, daily
     * @var string
     */
    public $recurrence = 'minutely';

    /**
     * Holds the URL of the WP Registry server.
     * @var string
     */
    public $registryUrl = null;

    /**
     * Holds the Announce object used to generate the payload for submitting to the registry.
     * @var Announce
     */
    public $announceObject = null;

    /**
     * AnnounceToRegistry constructor.
     * @param string $registryUrl
     * @param Announce $announceObject
     */
    public function __construct($registryUrl, Announce $announceObject)
    {
        parent::__construct();
        $this->registryUrl = $registryUrl;
        $this->announceObject = $announceObject;
    }

    /**
     * Submit the Announce payload to the WP Registry server.
     */
    public function execute()
    {
        $announce = $this->announceObject->execute();
        $return = $this->wp()->wp_remote_post($this->registryUrl, [
            'body' => [
                'payload' => json_encode($announce),
            ],
        ]);

        if ($this->wp()->is_wp_error($return)) {
            trigger_error(
                'WP Registry (client): could not announce to registry. WP_Error was returned with message: ' .
                $return->get_error_message(),
                E_USER_ERROR
            );
        }
    }
}
