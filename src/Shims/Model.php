<?php namespace Nylas\Shims;

/**
 * ----------------------------------------------------------------------------------
 * Model
 * ----------------------------------------------------------------------------------
 *
 * @package Nylas
 * @author lanlin
 * @change 2017-10-12
 */
class Model extends Resource
{

    // ------------------------------------------------------------------------------

    /**
     * @var array
     */
    public $data;

    // ------------------------------------------------------------------------------

    protected $filter    = ['offset' => 0, 'limit' => 50];
    protected $chunkSize = 50;

    // ------------------------------------------------------------------------------

    /**
     * get data
     *
     * @param $key
     * @return mixed
     */
    public function __get($key)
    {
        if (array_key_exists($key, $this->data))
        {
            return $this->data[$key];
        }

        return null;
    }

    // ------------------------------------------------------------------------------

    /**
     * get first item
     *
     * @return mixed
     * @throws \Exception
     */
    public function first()
    {
        $results = $this->getModelCollection(0, 1);

        return $results[0] ? $results[0] : null;
    }

    // ------------------------------------------------------------------------------

    /**
     * get part of datas from $offset till $limit
     *
     * @param int $offset
     * @param int $limit
     * @return array
     * @throws \Exception
     */
    public function part($offset = 0, $limit = 50)
    {
        return $this->range($offset, $limit);
    }

    // ------------------------------------------------------------------------------

    /**
     * get all items
     *
     * @param $limit
     * @return array
     * @throws \Exception
     */
    public function all($limit = INF)
    {
        return $this->range($this->filter['offset'], $limit);
    }

    // ------------------------------------------------------------------------------

    /**
     * set filter
     *
     * @param $filter
     * @return Model
     */
    public function where(array $filter)
    {
        $this->filter = array_merge($this->filter, $filter);

        return $this;
    }

    // ------------------------------------------------------------------------------

    /**
     * find by id
     *
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public function find($id)
    {
        return $this->getModel($id);
    }

    // ------------------------------------------------------------------------------

    /**
     * get items
     *
     * @return \Generator
     * @throws \Exception
     */
    public function items()
    {
        $items  = [];
        $offset = 0;

        while (true)
        {
            $items = $this->getModelCollection($offset, $this->chunkSize);

            if (!$items) { break; }

            foreach ($items as $item) { yield $item; }

            if (count($items) < $this->chunkSize) { break; }

            $offset += count($items);
        }

        return $items;
    }

    // ------------------------------------------------------------------------------

    /**
     * get data by range
     *
     * @param int $offset
     * @param int $limit
     * @return array
     * @throws \Exception
     */
    protected function range($offset, $limit)
    {
        $result = [];

        while (count($result) < $limit)
        {
            $toFetch = min($limit - count($result), $this->chunkSize);

            $data   = $this->getModelCollection($offset + count($result), $toFetch);
            $result = array_merge($result, $data);

            if (!$data || count($data) < $toFetch)
            {
                break;
            }
        }

        return $result;
    }

    // ------------------------------------------------------------------------------

    /**
     * get model object
     *
     * @param $id
     * @return \Nylas\Shims\Model
     * @throws \Exception
     */
    protected function getModel($id)
    {
        return $this->getResource($id, $this->filter);
    }

    // ------------------------------------------------------------------------------

    /**
     * get model collection
     *
     * @param $offset
     * @param $limit
     * @return array
     * @throws \Exception
     */
    protected function getModelCollection($offset, $limit)
    {
        $this->filter['offset'] = $offset;
        $this->filter['limit']  = $limit;

        return $this->getResources($this->filter);
    }

    // ------------------------------------------------------------------------------

}
