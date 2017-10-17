<?php namespace Nylas\Models;

use Nylas\Models;
use Nylas\Shims\Model;

/**
 * ----------------------------------------------------------------------------------
 * Calendar
 * ----------------------------------------------------------------------------------
 *
 * @package Nylas\Models
 * @author lanlin
 * @change 2017-10-12
 */
class Calendar extends Model
{

    // ------------------------------------------------------------------------------

    /**
     * @var string
     */
    public $collectionName = 'calendars';

    // ------------------------------------------------------------------------------

    /**
     * get events
     *
     * @param string $calendarId
     * @return Models\Event
     * @throws \Exception
     */
    public function events(string $calendarId = null)
    {
        $options =
        [
            'token'      => $this->apiToken,
            'debug'      => $this->apiDebug,
            'app_id'     => $this->appID,
            'app_secret' => $this->appSecret,
            'app_server' => $this->apiServer
        ];

        $event = new Models\Event($options);

        $event->collectionName = $this->collectionName;

        $id = $calendarId ?? $this->data['id'];

        if (!$id)
        {
            throw new \Exception('Calendar id is required!');
        }

        $event->where(['calendar_id' => $this->data['id']]);

        return $event;
    }

    // ------------------------------------------------------------------------------

}