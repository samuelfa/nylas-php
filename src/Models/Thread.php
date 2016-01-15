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
 * @change 2015-11-06
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
     * @param $tags
     * @return mixed
     */
    public function addTags($tags)
    {
        return $this->_updateTags($tags);
    }

    // ------------------------------------------------------------------------------

    /**
     * @param $tags
     * @return mixed
     */
    public function removeTags($tags)
    {
        return $this->_updateTags(array(), $tags);
    }

    // ------------------------------------------------------------------------------

    /**
     * @return mixed
     */
    public function markAsRead()
    {
        return $this->_updateTags(array(), array('unread'));
    }

    // ------------------------------------------------------------------------------

    /**
     * @return mixed
     */
    public function markAsSeen()
    {
        return $this->_updateTags(array(), array('unseen'));
    }

    // ------------------------------------------------------------------------------

    /**
     * @return mixed
     */
    public function archive()
    {
        return $this->_updateTags(array('archive'), array('inbox'));
    }

    // ------------------------------------------------------------------------------

    /**
     * @return mixed
     */
    public function unarchive()
    {
        return $this->_updateTags(array('inbox'), array('archive'));
    }

    // ------------------------------------------------------------------------------

    /**
     * @return mixed
     */
    public function trash()
    {
        return $this->_updateTags(array('trash'), array());
    }

    // ------------------------------------------------------------------------------

    /**
     * @return mixed
     */
    public function star()
    {
        return $this->_updateTags(array('starred'), array());
    }

    // ------------------------------------------------------------------------------

    /**
     * @return mixed
     */
    public function unstar()
    {
        return $this->_updateTags(array(), array('starred'));
    }

    // ------------------------------------------------------------------------------

    /**
     * @param array $add
     * @param array $remove
     * @return mixed
     */
    private function _updateTags($add = array(), $remove = array())
    {
        $payload = array(
            "add_tags"    => $add,
            "remove_tags" => $remove
        );

        return $this->api->klass->_updateResource(
            $this,
            $this->data['id'],
            $payload
        );
    }

    // ------------------------------------------------------------------------------

}