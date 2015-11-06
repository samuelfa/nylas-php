<?php

namespace Nylas\Models;

use Nylas\NylasAPIObject;

/**
 * ----------------------------------------------------------------------------------
 * Account
 * ----------------------------------------------------------------------------------
 *
 * @package Nylas\Models
 * @author lanlin
 * @change 2015-11-06
 */
class Account extends NylasAPIObject
{

    // ------------------------------------------------------------------------------

    public $collectionName = 'account';

    // ------------------------------------------------------------------------------

    public function __construct($api, $namespace)
    {
        parent::__construct();

        $this->api = $api->api;
        $this->namespace = $api->namespace;
    }

    // ------------------------------------------------------------------------------

}
