<?php

namespace Nylas\Models;

use Nylas\Models;
use Nylas\NylasAPIObject;
use Nylas\NylasModelCollection;

/**
 * ----------------------------------------------------------------------------------
 * Calendar
 * ----------------------------------------------------------------------------------
 *
 * @package Nylas\Models
 * @author lanlin
 * @change 2015-11-06
 */
class Calendar extends NylasAPIObject
{

    // ------------------------------------------------------------------------------

    public $collectionName = 'calendars';

    // ------------------------------------------------------------------------------

    /**
     * Calendar constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    // ------------------------------------------------------------------------------

    /**
     * @return NylasModelCollection
     */
    public function events()
    {
        $calendar_id = $this->data['id'];
        $msgObj = new Models\Event($this);

        return new NylasModelCollection(
            $msgObj, $this->klass, array("calendar_id" => $calendar_id)
        );
    }

    // ------------------------------------------------------------------------------

}