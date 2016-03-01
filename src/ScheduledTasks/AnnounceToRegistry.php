<?php

namespace MOJDigital\WP_Registry\Client\ScheduledTasks;

use MOJDigital\WP_Registry\Client\Commands\Announce;

class AnnounceToRegistry extends BaseScheduledTask
{
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
     * Holds the Site ID of the current WordPress install.
     * @var string
     */
    public $siteId = null;

    /**
     * AnnounceToRegistry constructor.
     * @param string $registryUrl
     * @param string $siteId
     */
    public function __construct($registryUrl, $siteId)
    {
        parent::__construct();
        $this->registryUrl = $registryUrl;
        $this->siteId = $siteId;
    }

    /**
     * Submit the Announce payload to the WP Registry server.
     */
    public function execute()
    {
        $announce = new Announce($this->siteId);
        $payload = $announce->execute();
        $return = wp_remote_post(WP_REGISTRY_URL, [
            'body' => [
                'payload' => json_encode($payload),
            ],
        ]);

        if (is_wp_error($return)) {
            trigger_error('WP Registry (client): could not announce to registry. WP_Error was returned with message: ' . $return->get_error_message(), E_USER_ERROR);
        } else if ($return['response']['code'] !== 200) {
            trigger_error('WP Registry (client): could not announce to registry. A non-200 status code was returned: ' . $return['response']['code'] . ' ' . $return['response']['message'], E_USER_ERROR);
        }
    }
}
