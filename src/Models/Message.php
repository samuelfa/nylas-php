<?php namespace Nylas\Models;

use Nylas\Shims\Model;

/**
 * ----------------------------------------------------------------------------------
 * Message
 * ----------------------------------------------------------------------------------
 *
 * @package Nylas\Models
 * @author lanlin
 * @change 2017-10-12
 */
class Message extends Model
{

    // ------------------------------------------------------------------------------

    /**
     * @var string
     */
    public $collectionName = 'messages';

    // ------------------------------------------------------------------------------

    /**
     * mark star to message id
     *
     * @param       $id
     * @param  bool $starred
     * @return mixed
     * @throws \Exception
     */
    public function starred($id, $starred = true)
    {
        $data = ['starred' => $starred];

        return $this->updateResource($id, $data);
    }

    // ------------------------------------------------------------------------------

    /**
     * mark unread status to message id
     *
     * @param      $id
     * @param bool $unread
     * @return mixed
     * @throws \Exception
     */
    public function unread($id, $unread = false)
    {
        $data = ['unread' => $unread];

        return $this->updateResource($id, $data);
    }

    // ------------------------------------------------------------------------------

    /**
     * move a message to folder
     *
     * @param  $id
     * @param  $target_id
     * @param  $type  'folder|label'
     * @return mixed
     * @throws \Exception
     */
    public function move($id, $target_id, $type = 'folder')
    {
        // move message to a folder
        if($type == 'folder')
        {
            $data = ['folder_id' => $target_id];
        }

        // move message to a label
        else
        {
            $data = ['label_ids' => [$target_id]];
        }

        return $this->updateResource($id, $data);
    }

    // ------------------------------------------------------------------------------

    /**
     * @param string $msgId
     * @return null|string
     * @throws \Exception
     */
    public function raw(string $msgId = null)
    {
        $data    = '';
        $headers = ['Accept' => 'message/rfc822'];

        $id = $msgId ?? $this->data['id'];

        if (!$id)
        {
            throw new \Exception('Event id is required!');
        }

        $resource = $this->getResourceData($id, ['headers' => $headers]);

        while (!$resource->eof())
        {
            $data .= $resource->read(1024);
        }

        return $data;
    }

    // ------------------------------------------------------------------------------

}