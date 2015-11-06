<?php

namespace Nylas\Models;

use Nylas\NylasAPIObject;
use Nylas\Models\Send;

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
        'version'
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
        foreach ($this->attrs as $attr)
        {
            if (array_key_exists($attr, $data))
            {
                $sanitized[$attr] = $data[$attr];
            }
        }

        $this->data = array_merge($this->data, $sanitized);

        return $this->api->_updateResource($this->namespace, $this, $data['id'], $this->data);
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
            $this->data['file_ids'] = array_diff($this->data['file_ids'], array($fileObj->id));
        }

        return $this;
    }

    // ------------------------------------------------------------------------------

    /**
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        return $this->klass->_deleteResource($this->namespace, $this, $id);
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

        $send_object = new Send($this->api, $this->namespace);
        return $send_object->send($data);
    }

    // ------------------------------------------------------------------------------

}