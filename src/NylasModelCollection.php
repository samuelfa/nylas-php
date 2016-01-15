<?php

namespace Nylas;

/**
 * ----------------------------------------------------------------------------------
 * NylasModelCollection
 * ----------------------------------------------------------------------------------
 *
 * @package Nylas
 */
class NylasModelCollection
{

    // ------------------------------------------------------------------------------

    private $chunkSize = 50;

    // ------------------------------------------------------------------------------

    /**
     * @param NylasAPIObject $klass
     * @param $api
     * @param array $filter
     * @param int $offset
     * @param array $filters
     */
    public function __construct(
        $klass,
        $api,
        $filter    = array(),
        $offset    = 0,
        $filters   = array()
    )
    {
        $this->api       = $api;
        $this->klass     = $klass;
        $this->filter    = $filter;
        $this->filters   = $filters;

        if (!array_key_exists('offset', $filter))
        {
            $this->filter['offset'] = 0;
        }
    }

    // ------------------------------------------------------------------------------

    /**
     * @return \Generator
     */
    public function items()
    {
        $offset = 0;
        while (True)
        {
            $items = $this->_getModelCollection($offset, $this->chunkSize);

            if (!$items) { break; }

            foreach ($items as $item) { yield $item; }

            if (count($items) < $this->chunkSize) { break; }

            $offset += count($items);
        }
    }

    // ------------------------------------------------------------------------------

    /**
     * @return null
     */
    public function first()
    {
        $results = $this->_getModelCollection(0, 1);

        if ($results)
        {
            return $results[0];
        }

        return NULL;
    }

    // ------------------------------------------------------------------------------

    /**
     * get part of datas from $offset till $limit
     *
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function part($offset=0, $limit=50)
    {
        return $this->_range($offset, $limit);
    }

    // ------------------------------------------------------------------------------

    /**
     * @param $limit
     * @return array
     */
    public function all($limit = INF)
    {
        return $this->_range($this->filter['offset'], $limit);
    }

    // ------------------------------------------------------------------------------

    /**
     * @param $filter
     * @return NylasModelCollection
     */
    public function where($filter)
    {
        $this->filter = array_merge($this->filter, $filter);

        $collection = clone $this;
        $collection->filter = $this->filter;

        return $collection;
    }

    // ------------------------------------------------------------------------------

    /**
     * @param $id
     * @return mixed
     */
    public function find($id)
    {
        return $this->_getModel($id);
    }

    // ------------------------------------------------------------------------------

    /**
     * @param $data
     * @return mixed
     */
    public function create($data)
    {
        return $this->klass->create($data, $this);
    }

    // ------------------------------------------------------------------------------

    /**
     * @param $data
     * @return mixed
     */
    public function send($data)
    {
        return $this->klass->send($data);
    }

    // ------------------------------------------------------------------------------

    /**
     * @param $offset
     * @param $limit
     * @return array
     */
    private function _range($offset, $limit)
    {
        $result = array();

        while (count($result) < $limit)
        {
            $to_fetch = min($limit - count($result), $this->chunkSize);

            $data   = $this->_getModelCollection($offset + count($result), $to_fetch);
            $result = array_merge($result, $data);

            if (!$data || count($data) < $to_fetch)
            {
                break;
            }
        }

        return $result;
    }

    // ------------------------------------------------------------------------------

    /**
     * @param $id
     * @return mixed
     */
    private function _getModel($id)
    {
        // make filter a kwarg filters
        return $this->api->getResource($this->klass, $id, $this->filter);
    }

    // ------------------------------------------------------------------------------

    /**
     * @param $offset
     * @param $limit
     * @return mixed
     */
    private function _getModelCollection($offset, $limit)
    {
        $this->filter['offset'] = $offset;
        $this->filter['limit']  = $limit;

        return $this->api->getResources($this->klass, $this->filter);
    }

    // ------------------------------------------------------------------------------

}
