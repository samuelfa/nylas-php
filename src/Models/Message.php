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
 * @change 2015-11-06
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