<?php namespace Nylas\Models;

use Nylas\Models;
use Nylas\Shims\Model;

/**
 * ----------------------------------------------------------------------------------
 * Thread
 * ----------------------------------------------------------------------------------
 *
 * @package Nylas\Models
 * @author lanlin
 * @change 2017-10-12
 */
class Thread extends Model
{

    // ------------------------------------------------------------------------------

    public $collectionName = 'threads';

    // ------------------------------------------------------------------------------

    /**
     * @param string $threadId
     * @return Model
     * @throws \Exception
     */
    public function messages(string $threadId = null)
    {
        $options =
        [
            'token'      => $this->apiToken,
            'debug'      => $this->apiDebug,
            'app_id'     => $this->appID,
            'app_secret' => $this->appSecret,
            'app_server' => $this->apiServer
        ];

        $msg = new Models\Message($options);

        $msg->collectionName = $this->collectionName;

        $id = $threadId ?? $this->data['id'];

        if (!$id)
        {
            throw new \Exception('Thread id is required!');
        }

        $msg->where(['thread_id' => $id]);

        return $msg;
    }

    // ------------------------------------------------------------------------------

    /**
     * @param string $threadId
     * @return Models\Draft
     * @throws \Exception
     */
    public function drafts(string $threadId = null)
    {
        $options =
        [
            'token'      => $this->apiToken,
            'debug'      => $this->apiDebug,
            'app_id'     => $this->appID,
            'app_secret' => $this->appSecret,
            'app_server' => $this->apiServer
        ];

        $draft = new Models\Draft($options);

        $draft->collectionName = $this->collectionName;

        $id = $threadId ?? $this->data['id'];

        if (!$id)
        {
            throw new \Exception('Thread id is required!');
        }

        $draft->where(['thread_id' => $this->data['id']]);

        return $draft;
    }

    // ------------------------------------------------------------------------------

    /**
     * @param string $threadId
     * @return mixed
     * @throws \Exception
     */
    public function createReply(string $threadId = null)
    {
        $id = $threadId ?? $this->data['id'];

        if (!$id)
        {
            throw new \Exception('Thread id is required!');
        }

        return $this->drafts($threadId)->create(
        [
            'subject'   => $this->data['subject'],
            'thread_id' => $id
        ]);
    }

    // ------------------------------------------------------------------------------

    /**
     * mark star to thread id
     *
     * @param       $id
     * @param  bool $starred
     * @return mixed
     */
    public function starred($id, $starred = true)
    {
        $data = ['starred' => $starred];

        return $this->updateResource($id, $data);
    }

    // ------------------------------------------------------------------------------

    /**
     * mark unread status to thread id
     *
     * @param      $id
     * @param bool $unread
     * @return mixed
     */
    public function unread($id, $unread = false)
    {
        $data = ['unread' => $unread];

        return $this->updateResource($id, $data);
    }

    // ------------------------------------------------------------------------------

    /**
     * move a thread to trash
     *
     * @param  $id
     * @param  $target_id
     * @param  $type  'folder|label'
     * @return mixed
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

}