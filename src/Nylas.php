<?php

/**
 * INFO: I made some change, so the nylas package can
 * suport guzzle >6.0
 *
 * @modify lanlin1987@gmail.com
 * @github https://github.com/lanlin
 */
namespace Nylas;
use Nylas\Models;
use GuzzleHttp\Client as GC;


/**
 * ----------------------------------------------------------------------------------
 * Nylas
 * ----------------------------------------------------------------------------------
 *
 * @package Nylas
 */
class Nylas
{

    // ------------------------------------------------------------------------------

    protected $apiServer = 'https://api.nylas.com';
    protected $apiClient;
    protected $apiToken;
    public $apiRoot = 'n';

    // ------------------------------------------------------------------------------

    /**
     * @param $appID
     * @param $appSecret
     * @param null $token
     * @param null $apiServer
     */
    public function __construct($appID, $appSecret, $token = NULL, $apiServer = NULL)
    {
        $this->appID = $appID;
        $this->appSecret = $appSecret;
        $this->apiToken = $token;
        $this->apiClient = $this->createApiClient();

        if ($apiServer)
        {
            $this->apiServer = $apiServer;
        }
    }

    // ------------------------------------------------------------------------------

    /**
     * @return array
     */
    protected function createHeaders()
    {
        $token = 'Basic ' . base64_encode($this->apiToken . ':');
        $headers = array(
            'headers' => [
                'Authorization' => $token,
                'X-Nylas-API-Wrapper' => 'php'
            ]
        );
        return $headers;
    }

    // ------------------------------------------------------------------------------

    /**
     * @return GC
     */
    private function createApiClient()
    {
        return new GC(['base_uri' => $this->apiServer]);
    }

    // ------------------------------------------------------------------------------

    /**
     * create auth url
     *
     * @param $redirect_uri
     * @param null $login_hint
     * @param null $userid
     * @return string
     *
     * @author lanlin
     * @TODO   changed, if upgrade, should modify this.
     */
    public function createAuthURL($redirect_uri, $login_hint = NULL, $userid = NULL)
    {
        $args =
            [
                "client_id" => $this->appID,
                "redirect_uri" => $redirect_uri,
                "response_type" => "code",
                "scope" => "email",
                "login_hint" => $login_hint,
                "state" => $userid ? $userid : $this->generateId()
            ];

        return $this->apiServer . '/oauth/authorize?' . http_build_query($args);
    }

    // ------------------------------------------------------------------------------

    /**
     * get account base info
     *
     * @param  $token
     * @return array
     * @author lanlin
     * @TODO   changed, if upgrade, should modify this.
     */
    public function getAccount($token=NULL)
    {
        $this->apiToken = $this->apiToken ? $this->apiToken : $token;

        $url = $this->apiServer . '/account';
        $response = $this->apiClient->get($url)->getBody()->getContents();

        return json_decode($response);
    }

    // ------------------------------------------------------------------------------

    /**
     * get all labels of the account
     *
     * @param  $token
     * @return mixed
     * @author lanlin
     * @TODO   changed, if upgrade, should modify this.
     */
    public function getLabels($token=NULL)
    {
        $this->apiToken = $this->apiToken ? $this->apiToken : $token;

        $url = $this->apiServer . '/labels';
        $response = $this->apiClient->get($url)->getBody()->getContents();

        return json_decode($response);
    }

    // ------------------------------------------------------------------------------

    /**
     * get all folders of the account
     *
     * @param  $token
     * @return mixed
     * @author lanlin
     * @TODO   changed, if upgrade, should modify this.
     */
    public function getFolders($token=NULL)
    {
        $this->apiToken = $this->apiToken ? $this->apiToken : $token;

        $url = $this->apiServer . '/folders';
        $response = $this->apiClient->get($url)->getBody()->getContents();

        return json_decode($response);
    }

    // ------------------------------------------------------------------------------

    /**
     * @param $code
     * @return null
     */
    public function getAuthToken($code)
    {
        $args = array(
            "client_id" => $this->appID,
            "client_secret" => $this->appSecret,
            "grant_type" => "authorization_code",
            "code" => $code
        );

        $url = $this->apiServer . '/oauth/token';
        $payload = array();
        $payload['headers']['Content-Type'] = 'application/x-www-form-urlencoded';
        $payload['headers']['Accept'] = 'text/plain';
        $payload['form_params'] = $args;

        $response = $this->apiClient->post($url, $payload)->getBody()->getContents();
        $response = json_decode($response);

        if (array_key_exists('access_token', $response))
        {
            $this->apiToken = $response['access_token'];
        }

        return $this->apiToken;
    }

    // ------------------------------------------------------------------------------

    /**
     * @return NylasModelCollection
     */
    public function namespaces()
    {
        $nsObj = new Models\Namespaces($this, NULL);
        return new NylasModelCollection($nsObj, $this, NULL);
    }

    // ------------------------------------------------------------------------------

    /**
     * @param $namespace
     * @param $klass
     * @param $filter
     * @return array
     */
    public function getResources($namespace, $klass, $filter)
    {
        $suffix = ($namespace) ? '/' . $klass->apiRoot . '/' . $namespace : '';
        $url = $this->apiServer . $suffix . '/' . $klass->collectionName;
        $url = $url . '?' . http_build_query($filter);

        $data = $this->apiClient->get($url, $this->createHeaders())->getBody()->getContents();
        $data = json_decode($data);

        $mapped = array();
        foreach ($data as $i)
        {
            $mapped[] = clone $klass->_createObject($this, $namespace, $i);
        }
        return $mapped;
    }

    // ------------------------------------------------------------------------------

    /**
     * @param $namespace
     * @param $klass
     * @param $id
     * @param $filters
     * @return mixed
     */
    public function getResource($namespace, $klass, $id, $filters)
    {
        $extra = '';
        if (array_key_exists('extra', $filters))
        {
            $extra = $filters['extra'];
            unset($filters['extra']);
        }
        $response = $this->getResourceRaw($namespace, $klass, $id, $filters);
        return $klass->_createObject($this, $namespace, $response);
    }

    // ------------------------------------------------------------------------------

    /**
     * @param $namespace
     * @param $klass
     * @param $id
     * @param $filters
     * @return mixed|string
     */
    public function getResourceRaw($namespace, $klass, $id, $filters)
    {
        $extra = '';
        if (array_key_exists('extra', $filters))
        {
            $extra = $filters['extra'];
            unset($filters['extra']);
        }
        $prefix = ($namespace) ? '/' . $klass->apiRoot . '/' . $namespace : '';
        $postfix = ($extra) ? '/' . $extra : '';
        $url = $this->apiServer . $prefix . '/' . $klass->collectionName . '/' . $id . $postfix;
        $url = $url . '?' . http_build_query($filters);

        $data = $this->apiClient->get($url, $this->createHeaders())->getBody()->getContents();
        $data = json_decode($data);

        return $data;
    }

    // ------------------------------------------------------------------------------

    /**
     * @param $namespace
     * @param $klass
     * @param $id
     * @param $filters
     * @return mixed|string
     */
    public function getResourceData($namespace, $klass, $id, $filters)
    {
        $extra = '';
        if (array_key_exists('extra', $filters))
        {
            $extra = $filters['extra'];
            unset($filters['extra']);
        }
        $prefix = ($namespace) ? '/' . $klass->apiRoot . '/' . $namespace : '';
        $postfix = ($extra) ? '/' . $extra : '';
        $url = $this->apiServer . $prefix . '/' . $klass->collectionName . '/' . $id . $postfix;
        $url = $url . '?' . http_build_query($filters);

        $data = $this->apiClient->get($url, $this->createHeaders())->getBody()->getContents();
        $data = json_decode($data);

        return $data;
    }

    // ------------------------------------------------------------------------------

    /**
     * @param $namespace
     * @param $klass
     * @param $data
     * @return mixed
     */
    public function _createResource($namespace, $klass, $data)
    {
        $prefix = ($namespace) ? '/' . $klass->apiRoot . '/' . $namespace : '';
        $url = $this->apiServer . $prefix . '/' . $klass->collectionName;

        $payload = $this->createHeaders();
        if ($klass->collectionName == 'files')
        {
            $payload['headers']['Content-Type'] = 'multipart/form-data';
            $payload['multipart'] = $data;
        }
        else
        {
            $payload['headers']['Content-Type'] = 'application/json';
            $payload['json'] = $data;
        }

        $response = $this->apiClient->post($url, $payload)->getBody()->getContents();
        $response = json_decode($response);

        return $klass->_createObject($this, $namespace, $response);
    }

    // ------------------------------------------------------------------------------

    /**
     * @param $namespace
     * @param $klass
     * @param $id
     * @param $data
     * @return mixed
     */
    public function _updateResource($namespace, $klass, $id, $data)
    {
        $prefix = ($namespace) ? '/' . $klass->apiRoot . '/' . $namespace : '';
        $url = $this->apiServer . $prefix . '/' . $klass->collectionName . '/' . $id;

        if ($klass->collectionName == 'files')
        {
            $payload['headers']['Content-Type'] = 'multipart/form-data';
            $payload['multipart'] = $data;
        }
        else
        {
            $payload = $this->createHeaders();
            $payload['json'] = $data;
        }

        $response = $this->apiClient->put($url, $payload)->getBody()->getContents();
        $response = json_decode($response);

        return $klass->_createObject($this, $namespace, $response);
    }

    // ------------------------------------------------------------------------------

    /**
     * @param $namespace
     * @param $klass
     * @param $id
     * @return mixed|string
     */
    public function _deleteResource($namespace, $klass, $id)
    {
        $prefix = ($namespace) ? '/' . $klass->apiRoot . '/' . $namespace : '';
        $url = $this->apiServer . $prefix . '/' . $klass->collectionName . '/' . $id;

        $payload = $this->createHeaders();
        $response = $this->apiClient->delete($url, $payload)->getBody()->getContents();
        $response = json_decode($response);

        return $response;
    }

    // ------------------------------------------------------------------------------

    /**
     * @return string
     */
    private function generateId()
    {
        // Generates unique UUID
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    // ------------------------------------------------------------------------------

}


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
     * @param $klass
     * @param $api
     * @param null $namespace
     * @param array $filter
     * @param int $offset
     * @param array $filters
     */
    public function __construct($klass, $api, $namespace = NULL, $filter = array(), $offset = 0, $filters = array())
    {
        $this->klass = $klass;
        $this->api = $api;
        $this->namespace = $namespace;
        $this->filter = $filter;
        $this->filters = $filters;

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
            if (!$items)
            {
                break;
            }
            foreach ($items as $item)
            {
                yield $item;
            }
            if (count($items) < $this->chunkSize)
            {
                break;
            }
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
     * @param array $filters
     * @return NylasModelCollection
     */
    public function where($filter, $filters = array())
    {
        $this->filter = array_merge($this->filter, $filter);
        $this->filter['offset'] = 0;
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
            $data = $this->_getModelCollection($offset + count($result), $to_fetch);
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
        return $this->api->getResource($this->namespace, $this->klass, $id, $this->filter);
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
        $this->filter['limit'] = $limit;
        return $this->api->getResources($this->namespace, $this->klass, $this->filter);
    }

    // ------------------------------------------------------------------------------
}


/**
 * ----------------------------------------------------------------------------------
 * NylasAPIObject
 * ----------------------------------------------------------------------------------
 *
 * @package Nylas
 */
class NylasAPIObject
{

    // ------------------------------------------------------------------------------

    public $apiRoot;

    // ------------------------------------------------------------------------------

    public function __construct()
    {
        $this->apiRoot = 'n';
    }

    // ------------------------------------------------------------------------------

    /**
     * @return null
     */
    public function json()
    {
        return $this->data;
    }

    // ------------------------------------------------------------------------------

    /**
     * @param $klass
     * @param $namespace
     * @param $objects
     * @return $this
     */
    public function _createObject($klass, $namespace, $objects)
    {
        $this->data = $objects;
        $this->klass = $klass;
        return $this;
    }

    // ------------------------------------------------------------------------------

    /**
     * @param $key
     * @return null
     */
    public function __get($key)
    {
        if (array_key_exists($key, $this->data))
        {
            return $this->data[$key];
        }
        return NULL;
    }

    // ------------------------------------------------------------------------------

}

