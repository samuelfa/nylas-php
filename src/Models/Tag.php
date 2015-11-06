<?php

namespace Nylas\Models;

use Nylas\NylasAPIObject;

/**
 * ----------------------------------------------------------------------------------
 * Tag
 * ----------------------------------------------------------------------------------
 *
 * @package Nylas\Models
 * @author lanlin
 * @change 2015-11-06
 */
class Tag extends NylasAPIObject
{

    // ------------------------------------------------------------------------------

    public $collectionName = 'tags';

    // ------------------------------------------------------------------------------

    public function __construct($api, $namespace)
    {
        parent::__construct();

        $this->api = $api->api;
        $this->namespace = $api->namespace;
    }

    // ------------------------------------------------------------------------------

}