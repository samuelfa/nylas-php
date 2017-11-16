<?php namespace Nylas\Models;

use Nylas\Shims\Model;

/**
 * ----------------------------------------------------------------------------------
 * Label
 * ----------------------------------------------------------------------------------
 *
 * @package Nylas\Models
 * @author lanlin
 * @change 2017-10-12
 */
class Label extends Model
{

    // ------------------------------------------------------------------------------

    /**
     * @var string
     */
    public $collectionName = 'labels';

    // ------------------------------------------------------------------------------

    /**
     * @param string $labelName
     * @param string $collectionName 'labels|folders'
     * @return \Nylas\Models\Label
     */
    public function create(string $labelName, string $collectionName = 'labels')
    {
        $payload =
        [
            'display_name' => $labelName,
        ];

        $this->setCollection($collectionName);

        $labelData  = $this->createResource($payload);
        $this->data = $labelData->data;

        return $this;
    }

    // ------------------------------------------------------------------------------

    /**
     * @param string $id
     * @param string $newName
     * @param string $collectionName 'labels|folders'
     * @return \Nylas\Models\Label
     */
    public function update(string $id, string $newName, string $collectionName = 'labels')
    {
        $payload =
        [
            'display_name' => $newName,
        ];

        $this->setCollection($collectionName);

        $labelData  = $this->updateResource($id, $payload);
        $this->data = $labelData->data;

        return $this;
    }

    // ------------------------------------------------------------------------------

    /**
     * @param string $id
     * @param string $collectionName 'labels|folders'
     * @return \Nylas\Models\Label
     */
    public function delete(string $id, string $collectionName = 'labels')
    {
        $this->setCollection($collectionName);

        $labelData  = $this->deleteResource($id);
        $this->data = $labelData->data;

        return $this;
    }

    // ------------------------------------------------------------------------------

    /**
     * @param string $collectionName
     */
    private function setCollection(string $collectionName)
    {
        if ($collectionName && in_array($collectionName, ['labels', 'folders']))
        {
            $this->collectionName = $collectionName;
        }
    }

    // ------------------------------------------------------------------------------

}
