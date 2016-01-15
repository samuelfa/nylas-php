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

    /**
     * Account constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    // ------------------------------------------------------------------------------

}
