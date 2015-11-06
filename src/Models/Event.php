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

    public function __construct($api, $namespace)
    {
        parent::__construct();
        $this->api = $api;
        $this->namespace = $namespace;
    }

    // ------------------------------------------------------------------------------

    /**
     * @param $data
     * @return mixed
     * @throws Exception
     */
    public function create($data)
    {
        $sanitized = array();
        foreach ($this->attrs as $attr)
        {
            if (array_key_exists($attr, $data))
            {
                $sanitized[$attr] = $data[$attr];
            }
        }

        if (!array_key_exists('calendar_id', $sanitized))
        {
            if ($this->api->collectionName == 'calendars')
            {
                $sanitized['calendar_id'] = $this->api->id;
            }
            else
            {
                throw new Exception("Missing calendar_id", 1);
            }
        }

        $this->data = $sanitized;
        return $this->api->_createResource($this->namespace, $this, $this->data);
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

        return $this->api->klass->_updateResource($this->namespace, $this, $this->id, $sanitized);
    }

    // ------------------------------------------------------------------------------

    /**
     * @return mixed
     */
    public function delete()
    {
        return $this->klass->_deleteResource($this->namespace, $this, $this->id);
    }

    // ------------------------------------------------------------------------------

}