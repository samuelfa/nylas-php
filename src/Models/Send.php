<?php

namespace Nylas\Models;

use Nylas\NylasAPIObject;

/**
 * ----------------------------------------------------------------------------------
 * Send
 * ----------------------------------------------------------------------------------
 *
 * @package Nylas\Models
 * @author lanlin
 * @change 2015-11-06
 */
class Send extends NylasAPIObject
{

    // ------------------------------------------------------------------------------

    public $collectionName = 'send';

    // ------------------------------------------------------------------------------

    public function __construct($api, $namespace)
    {
        parent::__construct();
        $this->api = $api;
        $this->namespace = $namespace;
    }

    // ------------------------------------------------------------------------------

    /**
     * @param array $data
     * @return mixed
     */
    public function send(array $data)
    {
        if (array_key_exists('id', $data))
        {
            $payload = array(
                "draft_id" => $data['id'],
                "version"  => $data['version']
            );
        }

        else { $payload = $data; }

        return $this->api->_createResource($this->namespace, $this, $payload);
    }

    // ------------------------------------------------------------------------------

}