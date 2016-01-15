<?php

namespace Nylas\Models;

use Nylas\NylasAPIObject;

/**
 * ----------------------------------------------------------------------------------
 * File
 * ----------------------------------------------------------------------------------
 *
 * @package Nylas\Models
 * @author lanlin
 * @change 2015-11-06
 */
class File extends NylasAPIObject
{

    // ------------------------------------------------------------------------------

    public $collectionName = 'files';

    // ------------------------------------------------------------------------------

    /**
     * File constructor.
     *
     * @param $api
     */
    public function __construct($api)
    {
        parent::__construct();

        $this->api = $api;
    }

    // ------------------------------------------------------------------------------

    /**
     * @param $file_name
     * @return $this
     */
    public function create($file_name)
    {
        $payload = array(
            "name"     => "file",
            "filename" => basename($file_name),
            "contents" => fopen($file_name, 'r')
        );

        $upload = $this->api->_createResource($this, $payload);
        $data   = $upload->data[0];

        $this->data = $data;
        return $this;
    }

    // ------------------------------------------------------------------------------

    /**
     * @return string
     */
    public function download()
    {
        $resource =
            $this->klass->getResourceData(
                $this,
                $this->data['id'],
                array('extra' => 'download')
            );

        $data = '';

        while (!$resource->eof())
        {
            $data .= $resource->read(1024);
        }

        return $data;
    }

    // ------------------------------------------------------------------------------

}