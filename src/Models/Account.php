<?php

namespace Nylas\Models;

use Nylas\NylasAPIObject;

class Account extends NylasAPIObject
{
    public $collectionName = 'account';

    public function __construct($api, $namespace)
    {
        parent::__construct();
    }
}
