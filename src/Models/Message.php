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

    /** @var \Nylas\Nylas */
    private $api;

    // ------------------------------------------------------------------------------

    /** @var \Nylas\Models\Account|null */
    private $namespace;

    // ------------------------------------------------------------------------------

    public $collectionName = 'messages';

    // ------------------------------------------------------------------------------

    public function __construct($api, $namespace)
    {
        parent::__construct();
        $this->api = $api;
        $this->namespace = $namespace;
    }

    // ------------------------------------------------------------------------------

    /**
     * @return null|string
     */
    public function raw()
    {
        $resource =
            $this->klass->getResourceRaw($this->namespace, $this, $this->data['id'], array('extra' => 'rfc2822'));
        if (array_key_exists('rfc2822', $resource))
        {
            return base64_decode($resource['rfc2822']);
        }
        return NULL;
    }

    // ------------------------------------------------------------------------------

}