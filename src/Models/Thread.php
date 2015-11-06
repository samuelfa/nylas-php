<?php

namespace Nylas\Models;

use Nylas\Models\Message;
use Nylas\Models\Draft;

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
     * @param $namespace
     */
    public function __construct($api, $namespace)
    {
        parent::__construct();
        $this->api = $api;
        $this->namespace = $namespace;
    }

    // ------------------------------------------------------------------------------

    /**
     * @return NylasModelCollection
     */
    public function messages()
    {
        $thread_id = $this->data['id'];
        $namespace = $this->data['namespace_id'];

        $msgObj = new Message($this, $namespace);

        return new NylasModelCollection(
            $msgObj,
            $this->klass,
            $namespace,
            array("thread_id" => $thread_id),
            0,
            array()
        );
    }

    // ------------------------------------------------------------------------------

    /**
     * @return NylasModelCollection
     */
    public function drafts()
    {
        $thread_id = $this->data['id'];
        $namespace = $this->data['namespace_id'];

        $msgObj = new Draft($this, $namespace);

        return new NylasModelCollection(
            $msgObj,
            $this->klass,
            $namespace,
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
            "add_tags" => $add,
            "remove_tags" => $remove
        );
        return $this->api->klass->_updateResource($this->namespace, $this, $this->data['id'], $payload);
    }

    // ------------------------------------------------------------------------------

}