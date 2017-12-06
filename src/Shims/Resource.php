<?php namespace Nylas\Shims;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Tools
 * ----------------------------------------------------------------------------------
 *
 * INFO: I made some change, so the nylas package can
 * suport guzzle >6.0
 *
 * @package Nylas
 * @author lanlin
 * @change 2017-11-12
 */
class Resource
{

    // ------------------------------------------------------------------------------

    /**
     * @var string
     */
    public $collectionName;

    // ------------------------------------------------------------------------------

    protected $appID;
    protected $apiToken;
    protected $apiClient;
    protected $appSecret;

    // ------------------------------------------------------------------------------

    protected $apiDebug  = false;
    protected $apiServer = 'https://api.nylas.com';

    // ------------------------------------------------------------------------------

    /**
     * Nylas constructor.
     *
     * @param array $options
     * [
     *     'app_id'     => 'must',
     *     'app_secret' => 'must',
     *     'app_server' => 'option',
     *
     *     'token'      => 'must',
     *     'debug'      => 'option',
     * ]
     */
    public function __construct(array $options = null)
    {
        $this->appID     = $options['app_id'] ?? '';
        $this->appSecret = $options['app_secret'] ?? '';

        $this->apiToken  = $options['token'] ?? '';
        $this->apiDebug  = $options['debug'] ?? false;
        $this->apiServer = $options['api_server'] ?? $this->apiServer;
    }

    // ------------------------------------------------------------------------------

    /**
     * new guzzle client
     *
     * @param bool $reset
     * @return \GuzzleHttp\Client
     */
    public function client(bool $reset = false)
    {
        if (!$this->apiClient || $reset)
        {
            $this->apiClient = new GuzzleClient(['base_uri' => $this->apiServer]);
        }

        return $this->apiClient;
    }

    // ------------------------------------------------------------------------------

    /**
     * get resource by id (single)
     *
     * @param string $id
     * @param array $filters
     * @return mixed
     * @throws \Exception
     */
    public function getResource(string $id, array $filters)
    {
        if (array_key_exists('extra', $filters))
        {
            // remove extra:  exp file download
            unset($filters['extra']);
        }

        return $this->getResourceRaw($id, $filters);
    }

    // ------------------------------------------------------------------------------

    /**
     * get resources (multiple)
     *
     * @param array $filter
     * @return array
     * @throws \Exception
     */
    public function getResources(array $filter)
    {
        $url = $this->apiServer . '/' . $this->collectionName;
        $url = $url . '?' . http_build_query($filter);

        try
        {
            $data = $this->client()
            ->get($url, $this->createHeaders())
            ->getBody()
            ->getContents();
        }

        catch (RequestException $e)
        {
            $msg = "Nylas::getResources: {$e->getMessage()}";

            throw new \Exception($msg);
        }

        return $this->json($data);
    }

    // ------------------------------------------------------------------------------

    /**
     * get resource raw
     *
     * @param string $id
     * @param array $filters
     * @return mixed
     * @throws \Exception
     */
    public function getResourceRaw($id, $filters)
    {
        $extra = '';

        // concat extra of filters
        // exp: file download
        if (array_key_exists('extra', $filters))
        {
            $extra = $filters['extra'];
            unset($filters['extra']);
        }

        $postfix = ($extra) ? '/' . $extra : '';

        $url = $this->apiServer . '/' . $this->collectionName . '/' . $id . $postfix;
        $url = $url . '?' . http_build_query($filters);

        try
        {
            $data = $this->client()
            ->get($url, $this->createHeaders())
            ->getBody()
            ->getContents();
        }

        catch (RequestException $e)
        {
            $msg = "Nylas::getResourceRaw: {$e->getMessage()}";

            throw new \Exception($msg);
        }

        return $this->json($data);
    }

    // ------------------------------------------------------------------------------

    /**
     * get data by stream
     *
     * @link https://docs.nylas.com/v1.0/reference#filesiddownload
     *
     * @param string $id
     * @param array $filters
     * @return \Psr\Http\Message\StreamInterface;
     * @throws \Exception
     */
    public function getResourceData($id, $filters)
    {
        $extra = '';

        // extra filters
        // exp: file download
        if (array_key_exists('extra', $filters))
        {
            $extra = $filters['extra'];
            unset($filters['extra']);
        }

        $postfix = ($extra) ? '/' . $extra : '';

        $url = $this->apiServer . '/' . $this->collectionName . '/' . $id . $postfix;
        $url = $url . '?' . http_build_query($filters);

        try
        {
            $stream = $this->client()
            ->get($url, $this->createHeaders())
            ->getBody();
        }

        catch (RequestException $e)
        {
            $msg = "Nylas::getResourceData: {$e->getMessage()}";

            throw new \Exception($msg);
        }

        return $stream;
    }

    // ------------------------------------------------------------------------------

    /**
     * create resource
     *
     * @param  $data
     * @param array $filters
     * @return mixed
     * @throws \Exception
     */
    public function createResource($data, array $filters = [])
    {
        $extra = '';

        if (array_key_exists('extra', $filters))
        {
            $extra = $filters['extra'];
            unset($filters['extra']);
        }

        $postfix = ($extra) ? '/' . $extra : '';

        $url = $this->apiServer . '/' . $this->collectionName . $postfix;

        !empty($filters) and $url = $url . '?' . http_build_query($filters);

        $payload = $this->createHeaders();

        $this->collectionName === 'files' ?
        $payload['multipart'] = $data : $payload['json'] = $data;

        try
        {
            $response = $this->client()
            ->post($url, $payload)
            ->getBody()
            ->getContents();
        }

        catch (RequestException $e)
        {
            $msg = "Nylas::_createResource: {$e->getMessage()}";

            throw new \Exception($msg);
        }

        return $this->json($response);
    }

    // ------------------------------------------------------------------------------

    /**
     * update resource
     *
     * @param string $id
     * @param $data
     * @return mixed
     * @throws \Exception
     */
    public function updateResource($id, $data)
    {
        $url = $this->apiServer . '/' . $this->collectionName . '/' . $id;

        $payload = $this->createHeaders();

        $this->collectionName === 'files' ?
        $payload['multipart'] = $data : $payload['json'] = $data;

        try
        {
            $response = $this->client()
            ->put($url, $payload)
            ->getBody()
            ->getContents();
        }

        catch (RequestException $e)
        {
            $msg = "Nylas::_updateResource: {$e->getMessage()}";

            throw new \Exception($msg);
        }

        return $this->json($response);
    }

    // ------------------------------------------------------------------------------

    /**
     * delete resource
     *
     * @param string $id
     * @return mixed
     * @throws \Exception
     */
    public function deleteResource($id)
    {
        $url = $this->apiServer . '/' . $this->collectionName . '/' . $id;

        $payload = $this->createHeaders();

        try
        {
            $response = $this->client()
            ->delete($url, $payload)
            ->getBody()
            ->getContents();
        }

        catch (RequestException $e)
        {
            $msg = "Nylas::_deleteResource: {$e->getMessage()}";

            throw new \Exception($msg);
        }

        return $this->json($response);
    }

    // ------------------------------------------------------------------------------

    /**
     * create request headers
     *
     * @return array
     */
    protected function createHeaders()
    {
        $token = 'Basic ' . base64_encode($this->apiToken . ':');

        $headers =
        [
            'debug'   => $this->apiDebug,
            'expect'  => false,
            'headers' =>
            [
                'Authorization'       => $token,
                'X-Nylas-API-Wrapper' => 'php'
            ]
        ];

        return $headers;
    }

    // ------------------------------------------------------------------------------

    /**
     * Parse the JSON response body and return an array
     *
     * @param  $content
     * @return array|string|int|bool|float
     * @throws \Exception if the response body is not in JSON format
     */
    protected function json($content)
    {
        $data = json_decode($content, true);

        if (JSON_ERROR_NONE !== json_last_error())
        {
            $msg = 'Unable to parse response body into JSON: ';

            throw new \Exception($msg . json_last_error());
        }

        return $data === null ? [] : $data;
    }

    // ------------------------------------------------------------------------------

    /**
     * generate uuid
     *
     * @return string
     */
    protected function generateId()
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
