<?php

namespace Nylas\Models;

use Nylas\NylasAPIObject;

/**
 * ----------------------------------------------------------------------------------
 * Message
 * ----------------------------------------------------------------------------------
 *
 * @package Nylas\Models
 * @author lanlin
 * @change 2016-01-19
 */
class Message extends NylasAPIObject
{

    // ------------------------------------------------------------------------------

    public $collectionName = 'messages';

    // ------------------------------------------------------------------------------

    /**
     * Message constructor.
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
     * mark star to message id
     *
     * @param       $id
     * @param  bool $starred
     * @return mixed
     */
    public function starred($id, $starred = true)
    {
        $data = ['starred' => $starred];

        return $this->api->_updateResource($this, $id, $data);
    }

    // ------------------------------------------------------------------------------

    /**
     * mark unread status to message id
     *
     * @param      $id
     * @param bool $unread
     * @return mixed
     */
    public function unread($id, $unread = false)
    {
        $data = ['unread' => $unread];

        return $this->api->_updateResource($this, $id, $data);
    }

    // ------------------------------------------------------------------------------

    /**
     * move a message to folder
     *
     * @param  $id
     * @param  $target_id
     * @param  $type  'folder|label'
     * @return mixed
     */
    public function move($id, $target_id, $type = 'folder')
    {
        // move message to a folder
        if($type == 'folder')
        {
            $data = ['folder_id' => $target_id];
        }

        // move message to a label
        else
        {
            $data = ['label_ids' => [$target_id]];
        }

        return $this->api->_updateResource($this, $id, $data);
    }

    // ------------------------------------------------------------------------------

    /**
     * @return null|string
     */
    public function raw()
    {
        $data = '';
        $headers = array('Accept' => 'message/rfc822');

        $resource =
        $this->klass->getResourceData(
            $this,
            $this->data['id'],
            array('headers' => $headers)
        );

        while (!$resource->eof())
        {
            $data .= $resource->read(1024);
        }

        return $data;
    }

    // ------------------------------------------------------------------------------

}