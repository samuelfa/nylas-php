<?php

/**
 * INFO: I made some change, so the nylas package can
 * suport guzzle >6.0
 *
 * @modify lanlin1987@gmail.com
 * @github https://github.com/lanlin
 */
namespace Nylas;

use GuzzleHttp;
use GuzzleHttp\Exception;
use GuzzleHttp\Client as GuzzleClient;

use Nylas\Models\Account;
use Nylas\Models\Calendar;
use Nylas\Models\Contact;
use Nylas\Models\Draft;
use Nylas\Models\Event;
use Nylas\Models\File;
use Nylas\Models\Message;
use Nylas\Models\Tag;
use Nylas\Models\Thread;


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

    public    $apiRoot   = 'n';
    protected $apiServer = 'https://api.nylas.com';
    protected $apiClient;
    protected $apiToken;

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

        $headers =
        [
            'headers' => [ 'Authorization' => $token, 'X-Nylas-API-Wrapper' => 'php' ]
        ];

        return $headers;
    }

    // ------------------------------------------------------------------------------

    /**
     * @return GuzzleClient
     */
    private function createApiClient()
    {
        return new GuzzleClient(['base_uri' => $this->apiServer]);
    }

    // ------------------------------------------------------------------------------

    /**
     * create auth url
     *
     * @param $redirect_uri
     * @param null $login_hint
     * @param null $userid (custom params would be sent back)
     * @return string
     *
     * @author lanlin
     * @TODO   changed, if upgrade, should modify this.
     */
    public function createAuthURL($redirect_uri, $login_hint = NULL, $userid = NULL)
    {
        $args =
        [
            "scope" => "email",
            "client_id" => $this->appID,
            "redirect_uri" => $redirect_uri,
            "response_type" => "code",
            "login_hint" => $login_hint,
            "state" => $userid ? $userid : $this->generateId()
        ];

        return $this->apiServer . '/oauth/authorize?' . http_build_query($args);
    }

    // ------------------------------------------------------------------------------

    /**
     * get account base info
     *
     * @return array
     * @author lanlin
     * @TODO   changed, if upgrade, should modify this.
     */
    public function getAccount()
    {
        $url = $this->apiServer . '/account';

        try
        {
            $response =
                $this->apiClient->get($url, $this->createHeaders())
                    ->getBody()->getContents();
        }

        catch (GuzzleHttp\Exception\ClientException $e)
        {
            return $e->getMessage();
        }

        return json_decode($response, TRUE);
    }

    // ------------------------------------------------------------------------------

    /**
     * get all labels of the account
     *
     * @return mixed
     * @author lanlin
     * @TODO   changed, if upgrade, should modify this.
     */
    public function getLabels()
    {
        $url = $this->apiServer . '/labels';

        try
        {
            $response =
                $this->apiClient->get($url, $this->createHeaders())
                    ->getBody()->getContents();
        }

        catch (GuzzleHttp\Exception\ClientException $e)
        {
            return $e->getMessage();
        }

        return json_decode($response, TRUE);
    }

    // ------------------------------------------------------------------------------

    /**
     * get all folders of the account
     *
     * @return mixed
     * @author lanlin
     * @TODO   changed, if upgrade, should modify this.
     */
    public function getFolders()
    {
        $url = $this->apiServer . '/folders';

        try
        {
            $response =
                $this->apiClient->get($url, $this->createHeaders())
                    ->getBody()->getContents();
        }

        catch (GuzzleHttp\Exception\ClientException $e)
        {
            return $e->getMessage();
        }

        return json_decode($response, TRUE);
    }

    // ------------------------------------------------------------------------------

    /**
     * @param $code
     * @return null
     */
    public function getAuthToken($code)
    {
        $args =
            [
                "code" => $code,
                "client_id" => $this->appID,
                "client_secret" => $this->appSecret,
                "grant_type" => "authorization_code"
            ];

        $url = $this->apiServer . '/oauth/token';

        try
        {
            $response =
                $this->apiClient->post($url, ['form_params' => $args])
                    ->getBody()->getContents();
        }

        catch (GuzzleHttp\Exception\ClientException $e)
        {
            return $e->getResponse();
        }

        $response = json_decode($response, TRUE);

        if (array_key_exists('access_token', $response))
        {
            $this->apiToken = $response['access_token'];
        }

        return $this->apiToken;
    }

    // ------------------------------------------------------------------------------

    /**
     * @return mixed
     */
    public function account()
    {
        $account = new Account($this, null);
        return $this->getResource(null, $account, null, array());
    }

    // ------------------------------------------------------------------------------

    /**
     * @return NylasModelCollection
     */
    public function messages()
    {
        $msgObj = new Message($this, null);
        return new NylasModelCollection($msgObj, $this, null, array(), 0, array());
    }

    // ------------------------------------------------------------------------------

    /**
     * @return NylasModelCollection
     */
    public function threads()
    {
        $msgObj = new Thread($this, null);
        return new NylasModelCollection($msgObj, $this, null, array(), 0, array());
    }

    // ------------------------------------------------------------------------------

    /**
     * @return NylasModelCollection
     */
    public function drafts()
    {
        $msgObj = new Draft($this, null);
        return new NylasModelCollection($msgObj, $this, null, array(), 0, array());
    }

    // ------------------------------------------------------------------------------

    /**
     * @return NylasModelCollection
     */
    public function tags()
    {
        $msgObj = new Tag($this, null);
        return new NylasModelCollection($msgObj, $this, null, array(), 0, array());
    }

    // ------------------------------------------------------------------------------

    /**
     * @return NylasModelCollection
     */
    public function files()
    {
        $msgObj = new File($this, null);
        return new NylasModelCollection($msgObj, $this, null, array(), 0, array());
    }

    // ------------------------------------------------------------------------------

    /**
     * @return NylasModelCollection
     */
    public function contacts()
    {
        $msgObj = new Contact($this, null);
        return new NylasModelCollection($msgObj, $this, null, array(), 0, array());
    }

    // ------------------------------------------------------------------------------

    /**
     * @return NylasModelCollection
     */
    public function calendars()
    {
        $msgObj = new Calendar($this, null);
        return new NylasModelCollection($msgObj, $this, null, array(), 0, array());
    }

    // ------------------------------------------------------------------------------

    /**
     * @return NylasModelCollection
     */
    public function events()
    {
        $msgObj = new Event($this, null);
        return new NylasModelCollection($msgObj, $this, null, array(), 0, array());
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

        try
        {
            $data =
            $this->apiClient->get($url, $this->createHeaders())
            ->getBody()->getContents();
        }

        catch (GuzzleHttp\Exception\ClientException $e)
        {
            return $e->getMessage();
        }

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

        try
        {
            $data = $this->apiClient->get($url, $this->createHeaders())
                ->getBody()->getContents();
        }

        catch (GuzzleHttp\Exception\ClientException $e)
        {
            return $e->getMessage();
        }

        return json_decode($data);
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

        try
        {
            $data =
                $this->apiClient->get($url, $this->createHeaders())
                    ->getBody()->getContents();
        }

        catch (GuzzleHttp\Exception\ClientException $e)
        {
            return $e->getMessage();
        }

        return json_decode($data);
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

        $klass->collectionName == 'files' ?
        $payload['multipart'] = $data :
        $payload['json'] = $data;

        try
        {
            $response =
            $this->apiClient->post($url, $payload)
            ->getBody()->getContents();
        }

        catch (GuzzleHttp\Exception\ClientException $e)
        {
            return $e->getMessage();
        }

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

        $payload = $this->createHeaders();

        $klass->collectionName == 'files' ?
        $payload['multipart'] = $data :
        $payload['json'] = $data;

        try
        {
            $response =
                $this->apiClient->put($url, $payload)
                    ->getBody()->getContents();
        }

        catch (GuzzleHttp\Exception\ClientException $e)
        {
            return $e->getMessage();
        }

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

        try
        {
            $response =
                $this->apiClient->delete($url, $payload)
                    ->getBody()->getContents();
        }

        catch (GuzzleHttp\Exception\ClientException $e)
        {
            return $e->getMessage();
        }

        return json_decode($response);
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
