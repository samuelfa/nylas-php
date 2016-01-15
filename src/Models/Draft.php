<?php

namespace Nylas\Models;

use Nylas\Models;
use Nylas\NylasAPIObject;

/**
 * ----------------------------------------------------------------------------------
 * Draft
 * ----------------------------------------------------------------------------------
 *
 * @package Nylas\Models
 * @author lanlin
 * @change 2015-11-06
 */
class Draft extends NylasAPIObject
{

    // ------------------------------------------------------------------------------

    public $collectionName = 'drafts';

    // ------------------------------------------------------------------------------

    public $attrs = array(
        'id',
        'subject',
        'to',
        'cc',
        'bcc',
        'from',
        'reply_to',
        'thread_id',
        'body',
        'file_ids',
        'version',
        'reply_to_message_id'
    );

    // ------------------------------------------------------------------------------

    /**
     * Draft constructor.
     *
     * @param $api
     */
    public function __construct($api)
    {
        parent::__construct();
    }

    // ------------------------------------------------------------------------------

    /**
     * @param $data
     * @param $api
     * @return mixed
     */
    public function create($data, $api)
    {
        $sanitized = array();
        foreach ($this->attrs as $attr)
        {
            if (array_key_exists($attr, $data))
            {
                $sanitized[$attr] = $data[$attr];
            }
        }

        $this->api  = $api->api;
        $this->data = $sanitized;

        return $this;
    }

    // ------------------------------------------------------------------------------

    /**
     * @param $data
     * @return mixed
     */
    public function update($data)
    {
        $sanitized = [];

        foreach ($this->attrs as $attr)
        {
            if (array_key_exists($attr, $data))
            {
                $sanitized[$attr] = $data[$attr];
            }
        }

        $this->data = array_merge($this->data, $sanitized);

        return $this;
    }

    // ------------------------------------------------------------------------------

    /**
     * @param $fileObj
     * @return $this
     */
    public function attach($fileObj)
    {
        if (array_key_exists('file_ids', $this->data))
        {
            $this->data['file_ids'][] = $fileObj->id;
        }
        else
        {
            $this->data['file_ids'] = array($fileObj->id);
        }

        return $this;
    }

    // ------------------------------------------------------------------------------

    /**
     * @param $fileObj
     * @return $this
     */
    public function detach($fileObj)
    {
        if (in_array($fileObj->id, $this->data['file_ids']))
        {
            $this->data['file_ids'] =
            array_diff($this->data['file_ids'], array($fileObj->id));
        }

        return $this;
    }

    // ------------------------------------------------------------------------------

    /**
     * this could send directly or send an already created draft
     *
     * @param null $data
     * @return mixed
     */
    public function send($data = NULL)
    {
        $data = ($data) ? $data : $this->data;

        if(array_key_exists('id', $data))
        {
            $resource = $this->api->_updateResource($this, $data['id'], $data);
        }

        else
        {
            $resource = $this->api->_createResource($this, $data);
        }

        if(!$resource || is_string($resource))
        {
            return $resource;
        }

        $send_object = new Models\Send($this->api);
        return $send_object->send($resource->data);
    }

    // ------------------------------------------------------------------------------

}