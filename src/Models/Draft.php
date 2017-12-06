<?php namespace Nylas\Models;

use Nylas\Models;
use Nylas\Shims\Model;

/**
 * ----------------------------------------------------------------------------------
 * Draft
 * ----------------------------------------------------------------------------------
 *
 * @package Nylas\Models
 * @author lanlin
 * @change 2017-11-12
 */
class Draft extends Model
{

    // ------------------------------------------------------------------------------

    /**
     * @var string
     */
    public $collectionName = 'drafts';

    // ------------------------------------------------------------------------------

    public $attrs =
    [
        'id',
        'subject',
        'to',
        'cc',
        'bcc',
        'from',
        'reply_to',
        'thread_id',
        'body',
        'file_ids',
        'version',
        'reply_to_message_id'
    ];

    // ------------------------------------------------------------------------------

    /**
     * create draft
     *
     * @param $data
     * @return Models\Draft
     */
    public function create($data)
    {
        $sanitized = [];

        foreach ($this->attrs as $attr)
        {
            if (array_key_exists($attr, $data))
            {
                $sanitized[$attr] = $data[$attr];
            }
        }

        $this->data = $sanitized;

        return $this;
    }

    // ------------------------------------------------------------------------------

    /**
     * update draft
     *
     * @param $data
     * @return Models\Draft
     */
    public function update($data)
    {
        $sanitized = [];

        foreach ($this->attrs as $attr)
        {
            if (array_key_exists($attr, $data))
            {
                $sanitized[$attr] = $data[$attr];
            }
        }

        $this->data = array_merge($this->data, $sanitized);

        return $this;
    }

    // ------------------------------------------------------------------------------

    /**
     * attach file
     *
     * @param $fileObj
     * @return Models\Draft
     */
    public function attach($fileObj)
    {
        if (array_key_exists('file_ids', $this->data))
        {
            $this->data['file_ids'][] = $fileObj->id;
        }
        else
        {
            $this->data['file_ids'] = [$fileObj->id];
        }

        return $this;
    }

    // ------------------------------------------------------------------------------

    /**
     * detach file
     *
     * @param $fileObj
     * @return Models\Draft
     */
    public function detach($fileObj)
    {
        if (in_array($fileObj->id, $this->data['file_ids']))
        {
            $this->data['file_ids'] =
            array_diff($this->data['file_ids'], [$fileObj->id]);
        }

        return $this;
    }

    // ------------------------------------------------------------------------------

    /**
     * create or update draft only, not send directly
     *
     * $client->create($data)->save();
     *
     * @param null $data
     * @return mixed
     * @throws \Exception
     */
    public function save($data = null)
    {
        $data = ($data) ? $data : $this->data;

        if(array_key_exists('id', $data))
        {
            return $this->updateResource($data['id'], $data);
        }

        return $this->createResource($data);
    }

    // ------------------------------------------------------------------------------

    /**
     * this could send directly or send an already created draft
     *
     * @param null $data
     * @return mixed
     * @throws \Exception
     */
    public function send($data = null)
    {
        $data = ($data) ? $data : $this->data;

        if(array_key_exists('id', $data))
        {
            $resource = $this->updateResource($data['id'], $data);
        }

        else
        {
            $resource = $this->createResource($data);
        }

        if(!$resource || is_string($resource))
        {
            return $resource;
        }

        $options =
        [
            'token'      => $this->apiToken,
            'debug'      => $this->apiDebug,
            'app_id'     => $this->appID,
            'app_secret' => $this->appSecret,
            'app_server' => $this->apiServer
        ];

        $send = new Models\Send($options);

        $send->collectionName = $this->collectionName;

        return $send->send($resource->data);
    }

    // ------------------------------------------------------------------------------

}