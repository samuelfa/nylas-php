<?php

namespace Nylas;

/**
 * ----------------------------------------------------------------------------------
 * NylasAPIObject
 * ----------------------------------------------------------------------------------
 *
 * @package Nylas
 */
class NylasAPIObject
{

    // ------------------------------------------------------------------------------

    public $api;
    public $data;
    public $klass;
    public $filter;
    public $filters;

    // ------------------------------------------------------------------------------

    /**
     * NylasAPIObject constructor.
     */
    public function __construct() {}

    // ------------------------------------------------------------------------------

    /**
     * @param $klass
     * @param $data
     * @return $this
     */
    public function _createObject($klass, $data)
    {
        $this->data  = $data;
        $this->klass = $klass;

        return $this;
    }

    // ------------------------------------------------------------------------------

    /**
     * @param $key
     * @return null
     */
    public function __get($key)
    {
        if (array_key_exists($key, $this->data))
        {
            return $this->data[$key];
        }

        return NULL;
    }

    // ------------------------------------------------------------------------------

}

