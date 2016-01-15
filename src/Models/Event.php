<?php

namespace Nylas\Models;

use Nylas\NylasAPIObject;

/**
 * ----------------------------------------------------------------------------------
 * Event
 * ----------------------------------------------------------------------------------
 *
 * @package Nylas\Models
 * @author lanlin
 * @change 2015-11-06
 */
class Event extends NylasAPIObject
{

    // ------------------------------------------------------------------------------

    public $collectionName = 'events';

    // ------------------------------------------------------------------------------

    public $attrs = array(
        "id",
        "namespace_id",
        "title",
        "description",
        "location",
        "read_only",
        "when",
        "busy",
        "participants",
        "calendar_id",
        "recurrence",
        "status",
        "master_event_id",
        "original_start_time"
    );

    // ------------------------------------------------------------------------------

    /**
     * Event constructor.
     *
     * @param $api
     */
    public function __construct($api)
    {
        parent::__construct();

        $this->api = $api;
    }

    // ------------------------------------------------------------------------------

    /**
     * @param $data
     * @param $api
     * @return mixed
     * @throws \Exception
     */
    public function create($data, $api = NULL)
    {
        $sanitized = array();
        foreach ($this->attrs as $attr)
        {
            if (array_key_exists($attr, $data))
            {
                $sanitized[$attr] = $data[$attr];
            }
        }

        if(!$api) { $api = $this->api->klass; }

        else { $api = $api->api; }


        if (!array_key_exists('calendar_id', $sanitized))
        {
            if ($this->api->collectionName == 'calendars')
            {
                $sanitized['calendar_id'] = $this->api->id;
            }
            else
            {
                throw new \Exception("Missing calendar_id", 1);
            }
        }

        $this->api  = $api;
        $this->data = $sanitized;

        return $this->api->_createResource($this, $this->data);
    }

    // ------------------------------------------------------------------------------

    /**
     * @param $data
     * @return mixed
     */
    public function update($data)
    {
        $sanitized = array();

        foreach ($this->attrs as $attr)
        {
            if (array_key_exists($attr, $data))
            {
                $sanitized[$attr] = $data[$attr];
            }
        }

        return $this->api->_updateResource($this, $this->id, $sanitized);
    }

    // ------------------------------------------------------------------------------

    /**
     * @return mixed
     */
    public function delete()
    {
        return $this->klass->_deleteResource($this, $this->id);
    }

    // ------------------------------------------------------------------------------

}