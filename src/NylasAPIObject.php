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

    public $apiRoot;

    // ------------------------------------------------------------------------------

    public function __construct()
    {
        $this->apiRoot = 'n';
    }

    // ------------------------------------------------------------------------------

    /**
     * @return null
     */
    public function json()
    {
        return $this->data;
    }

    // ------------------------------------------------------------------------------

    /**
     * @param $klass
     * @param $namespace
     * @param $objects
     * @return $this
     */
    public function _createObject($klass, $namespace, $objects)
    {
        $this->data = $objects;
        $this->klass = $klass;
        $this->namespace = $namespace;

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
