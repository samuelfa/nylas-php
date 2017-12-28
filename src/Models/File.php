<?php namespace Nylas\Models;

use Nylas\Shims\Model;

/**
 * ----------------------------------------------------------------------------------
 * File
 * ----------------------------------------------------------------------------------
 *
 * @package Nylas\Models
 * @author lanlin
 * @change 2017-12-28
 */
class File extends Model
{

    // ------------------------------------------------------------------------------

    /**
     * @var string
     */
    public $collectionName = 'files';

    // ------------------------------------------------------------------------------

    /**
     * @param $fileName
     * @return \Nylas\Models\File
     * @throws \Exception
     */
    public function create($fileName)
    {
        $payload[] =
        [
            'name'     => 'file',
            'filename' => basename($fileName),
            'contents' => fopen($fileName, 'r')
        ];

        $upload = $this->createResource($payload);

        $this->data = $upload[0];

        return $this;
    }

    // ------------------------------------------------------------------------------

    /**
     * @link https://docs.nylas.com/v1.0/reference#filesiddownload
     * @param string $fileId
     * @return string
     * @throws \Exception
     */
    public function download(string $fileId = null)
    {
        $id = $fileId ?? $this->data['id'];

        if (!$id)
        {
            throw new \Exception('File id is required!');
        }

        $data     = '';
        $resource = $this->getResourceData($id, ['extra' => 'download']);

        while (!$resource->eof())
        {
            $data .= $resource->read(1024);
        }

        return $data;
    }

    // ------------------------------------------------------------------------------

}