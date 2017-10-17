<?php namespace Nylas\Models;

use Nylas\Shims\Model;

/**
 * ----------------------------------------------------------------------------------
 * Send
 * ----------------------------------------------------------------------------------
 *
 * @package Nylas\Models
 * @author lanlin
 * @change 2017-10-12
 */
class Send extends Model
{

    // ------------------------------------------------------------------------------

    public $collectionName = 'send';

    // ------------------------------------------------------------------------------

    /**
     * @param array $data
     * @return mixed
     */
    public function send(array $data)
    {
        if (array_key_exists('id', $data))
        {
            $payload =
            [
                'draft_id' => $data['id'],
                'version'  => $data['version']
            ];
        }

        else { $payload = $data; }

        return $this->createResource($payload);
    }

    // ------------------------------------------------------------------------------

}