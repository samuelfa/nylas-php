<?php

namespace Nylas\Models;

use Nylas\Models;
use Nylas\NylasAPIObject;
use Nylas\NylasModelCollection;

/**
 * ----------------------------------------------------------------------------------
 * Thread
 * ----------------------------------------------------------------------------------
 *
 * @package Nylas\Models
 * @author lanlin
 * @change 2016-01-19
 */
class Thread extends NylasAPIObject
{

    // ------------------------------------------------------------------------------

    public $collectionName = 'threads';

    // ------------------------------------------------------------------------------

    /**
     * Thread constructor.
     * @param $api
     */
    public function __construct($api)
    {
        parent::__construct();

        $this->api = $api;
    }

    // ------------------------------------------------------------------------------

    /**
     * @return NylasModelCollection
     */
    public function messages()
    {
        $thread_id = $this->data['id'];
        $msgObj = new Models\Message($this);

        return new NylasModelCollection(
            $msgObj, $this->klass, array("thread_id" => $thread_id)
        );
    }

    // ------------------------------------------------------------------------------

    /**
     * @return NylasModelCollection
     */
    public function drafts()
    {
        $thread_id = $this->data['id'];
        $msgObj = new Models\Draft($this);

        return new NylasModelCollection(
            $msgObj,
            $this->klass,
            NULL,
            array("thread_id" => $thread_id),
            0,
            array()
        );
    }

    // ------------------------------------------------------------------------------

    /**
     * @return mixed
     */
    public function createReply()
    {
        return $this->drafts()->create(array(
            "subject" => $this->data['subject'],
            "thread_id" => $this->data['id']
        ));
    }

    // ------------------------------------------------------------------------------

    /**
     * mark star to thread id
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
     * mark unread status to thread id
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
     * move a thread to trash
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

}