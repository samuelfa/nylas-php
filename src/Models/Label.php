<?php namespace Nylas\Models;

use Nylas\Shims\Model;

/**
 * ----------------------------------------------------------------------------------
 * Label
 * ----------------------------------------------------------------------------------
 *
 * @package Nylas\Models
 * @author lanlin
 * @change 2017-11-16
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
     * @return \Nylas\Models\Label
     * @throws \Exception
     */
    public function create($labelName)
    {
        $payload =
        [
            'display_name' => $labelName,
        ];

        $this->data = $this->createResource($payload);

        return $this;
    }

    // ------------------------------------------------------------------------------

    /**
     * @param string $id
     * @param string $newName
     * @return \Nylas\Models\Label
     * @throws \Exception
     */
    public function update($id, $newName)
    {
        $payload =
        [
            'display_name' => $newName,
        ];

        $this->data = $this->updateResource($id, $payload);

        return $this;
    }

    // ------------------------------------------------------------------------------

    /**
     * @param string $id
     * @return \Nylas\Models\Label
     * @throws \Exception
     */
    public function delete($id)
    {
        $this->data = $this->deleteResource($id);

        return $this;
    }

    // ------------------------------------------------------------------------------

}
