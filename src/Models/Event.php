<?php namespace Nylas\Models;

use Nylas\Shims\Model;

/**
 * ----------------------------------------------------------------------------------
 * Event
 * ----------------------------------------------------------------------------------
 *
 * @package Nylas\Models
 * @author lanlin
 * @change 2017-10-12
 */
class Event extends Model
{

    // ------------------------------------------------------------------------------

    /**
     * @var string
     */
    public $collectionName = 'events';

    // ------------------------------------------------------------------------------

    /**
     * @var array
     */
    public $attrs =
    [
        'id',
        'namespace_id',
        'title',
        'description',
        'location',
        'read_only',
        'when',
        'busy',
        'participants',
        'calendar_id',
        'recurrence',
        'status',
        'master_event_id',
        'original_start_time'
    ];

    // ------------------------------------------------------------------------------

    /**
     * @param $data
     * @return mixed
     * @throws \Exception
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

        if (!array_key_exists('calendar_id', $sanitized))
        {
            if ($this->collectionName === 'calendars')
            {
                $sanitized['calendar_id'] = $this->data['id'];
            }
            else
            {
                throw new \Exception('Missing calendar_id', 1);
            }
        }

        $this->data = $sanitized;

        return $this->createResource($this->data);
    }

    // ------------------------------------------------------------------------------

    /**
     * @param $data
     * @param string $eventId
     * @return mixed
     * @throws \Exception
     */
    public function update($data, string $eventId = null)
    {
        $sanitized = [];

        foreach ($this->attrs as $attr)
        {
            if (array_key_exists($attr, $data))
            {
                $sanitized[$attr] = $data[$attr];
            }
        }

        $id = $eventId ?? $this->data['id'];

        if (!$id)
        {
            throw new \Exception('Event id is required!');
        }

        return $this->updateResource($id, $sanitized);
    }

    // ------------------------------------------------------------------------------

    /**
     * @param string $eventId
     * @return mixed
     * @throws \Exception
     */
    public function delete(string $eventId = null)
    {
        $id = $eventId ?? $this->data['id'];

        if (!$id)
        {
            throw new \Exception('Event id is required!');
        }

        return $this->deleteResource($this->data['id']);
    }

    // ------------------------------------------------------------------------------

}