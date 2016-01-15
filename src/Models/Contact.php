<?php

namespace Nylas\Models;

use Nylas\NylasAPIObject;

/**
 * ----------------------------------------------------------------------------------
 * Contact
 * ----------------------------------------------------------------------------------
 *
 * @package Nylas\Models
 * @author lanlin
 * @change 2015-11-06
 */
class Contact extends NylasAPIObject
{

    // ------------------------------------------------------------------------------

    public $collectionName = 'contacts';

    // ------------------------------------------------------------------------------

    /**
     * Contact constructor.
     *
     * @param $api
     */
    public function __construct($api)
    {
        parent::__construct();
    }

    // ------------------------------------------------------------------------------
}