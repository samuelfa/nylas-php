<?php

namespace Nylas\Models;

use Nylas\NylasAPIObject;

/**
 * ----------------------------------------------------------------------------------
 * Label
 * ----------------------------------------------------------------------------------
 *
 * @package Nylas\Models
 * @author lanlin
 * @change 2015-12-28
 */
class Label extends NylasAPIObject
{

    // ------------------------------------------------------------------------------

    public $collectionName = 'labels';

    // ------------------------------------------------------------------------------

    /**
     * Label constructor.
     *
     * @param $api
     */
    public function __construct($api)
    {
        parent::__construct();
    }

    // ------------------------------------------------------------------------------

}
