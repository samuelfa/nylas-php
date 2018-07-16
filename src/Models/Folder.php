<?php namespace Nylas\Models;

use Nylas\Shims\Model;

/**
 * ----------------------------------------------------------------------------------
 * Folder
 * ----------------------------------------------------------------------------------
 *
 * @package Nylas\Models
 * @author lanlin
 * @change 2017-11-16
 */
class Folder extends Model
{

    // ------------------------------------------------------------------------------

    /**
     * @var string
     */
    public $collectionName = 'folders';

    // ------------------------------------------------------------------------------

    /**
     * @param string $folderName
     * @return \Nylas\Models\Folder
     * @throws \Exception
     */
    public function create($folderName)
    {
        $payload =
        [
            'display_name' => $folderName,
        ];

        $this->data = $this->createResource($payload);

        return $this;
    }

    // ------------------------------------------------------------------------------

    /**
     * @param string $id
     * @param string $newName
     * @return \Nylas\Models\Folder
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
     * @return \Nylas\Models\Folder
     * @throws \Exception
     */
    public function delete($id)
    {
        $this->data = $this->deleteResource($id);

        return $this;
    }

    // ------------------------------------------------------------------------------

}
