<?php namespace Nylas\Models;

use Nylas\Shims\Model;

/**
 * ----------------------------------------------------------------------------------
 * Delta
 * ----------------------------------------------------------------------------------
 *
 * @package Nylas\Models
 * @author lanlin
 * @change 2017-12-06
 */
class Delta extends Model
{

    // ------------------------------------------------------------------------------

    /**
     * @var string
     */
    public $collectionName = 'delta';

    // ------------------------------------------------------------------------------

    /**
     * obtaining a Delta Cursor
     *
     * @return array
     * @throws \Exception
     */
    public function latestCursor()
    {
        $filter =
        [
            'extra' => 'latest_cursor',
        ];

        return $this->createResource(null, $filter);
    }

    // ------------------------------------------------------------------------------

    /**
     * requesting a set of deltas
     *
     * @param string $cursor
     * @return array
     * @throws \Exception
     */
    public function requesting($cursor)
    {
        $filter =
        [
            'cursor' => $cursor
        ];

        return $this->getResources($filter);
    }

    // ------------------------------------------------------------------------------

    /**
     * streaming delta updates
     *
     * @param string $cursor
     * @return array
     * @throws \Exception
     */
    public function streaming($cursor)
    {
        $filter =
        [
            'extra'  => 'streaming',
            'cursor' => $cursor
        ];

        return $this->getResourceRaw(null, $filter);
    }

    // ------------------------------------------------------------------------------

    /**
     * long polling delta updates
     *
     * @param string $cursor
     * @return array
     * @throws \Exception
     */
    public function longPolling($cursor)
    {
        $filter =
        [
            'extra'  => 'longpoll',
            'cursor' => $cursor
        ];

        return $this->getResourceRaw(null, $filter);
    }

    // ------------------------------------------------------------------------------

}