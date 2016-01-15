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
use GuzzleHttp;
use GuzzleHttp\Client as GuzzleClient;


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

    public    $apiRoot   = '';
    protected $apiClient;
    protected $apiToken;
    protected $apiServer = 'https://api.nylas.com';

    // ------------------------------------------------------------------------------

    /**
     * @param $appID
     * @param $appSecret
     * @param null $token
     * @param null $apiServer
     */
    public function __construct(
        $appID, $appSecret, $token = NULL, $apiServer = NULL
    )
    {
        $this->appID     = $appID;
        $this->apiToken  = $token;
        $this->appSecret = $appSecret;
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
            'debug'   => FALSE,
            'expect'  => FALSE,
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
            "scope"         => "email",
            "state"         => $userid ? (string)$userid : $this->generateId(),
            "client_id"     => $this->appID,
            "login_hint"    => $login_hint,
            "redirect_uri"  => $redirect_uri,
            "response_type" => "code"
        ];

        return $this->apiServer . '/oauth/authorize?' . http_build_query($args);
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
            "code"          => $code,
            "client_id"     => $this->appID,
            "grant_type"    => "authorization_code",
            "client_secret" => $this->appSecret
        ];

        $url = $this->apiServer . '/oauth/token';

        try
        {
            $response =
            $this->apiClient->post($url, ['form_params' => $args])
            ->getBody()->getContents();
        }

        catch (GuzzleHttp\Exception\RequestException $e)
        {
            return $e->getMessage();
        }

        // get json decode
        $data = $this->json($response);

        if (array_key_exists('access_token', $data))
        {
            $this->apiToken = $data['access_token'];
        }

        return $this->apiToken;
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
        $url = $this->apiServer . '/account';
        $this->apiToken = $this->apiToken ? $this->apiToken : $token;

        try
        {
            $response =
            $this->apiClient->get($url, $this->createHeaders())
            ->getBody()->getContents();
        }

        catch (GuzzleHttp\Exception\RequestException $e)
        {
            return $e->getMessage();
        }

        return $this->json($response);
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
        $url = $this->apiServer . '/labels';
        $this->apiToken = $this->apiToken ? $this->apiToken : $token;

        try
        {
            $response =
            $this->apiClient->get($url, $this->createHeaders())
            ->getBody()->getContents();

        }

        catch (GuzzleHttp\Exception\RequestException $e)
        {
            return $e->getMessage();
        }

        return $this->json($response);
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
        $url = $this->apiServer . '/folders';
        $this->apiToken = $this->apiToken ? $this->apiToken : $token;

        try
        {
            $response =
            $this->apiClient->get($url, $this->createHeaders())
            ->getBody()->getContents();
        }

        catch (GuzzleHttp\Exception\RequestException $e)
        {
            return $e->getMessage();
        }

        return $this->json($response);
    }

    // ------------------------------------------------------------------------------

    /**
     * get latest cursor for current account
     *
     * @return mixed
     */
    public function getLatestCursor()
    {
        $url = $this->apiServer . '/delta/latest_cursor';

        try
        {
            $response =
            $this->apiClient->post($url, $this->createHeaders())
            ->getBody()->getContents();
        }

        catch (GuzzleHttp\Exception\RequestException $e)
        {
            return $e->getMessage();
        }

        // get json decode
        return $this->json($response);
    }

    // ------------------------------------------------------------------------------

    /**
     * get deltas
     *
     * @param  $cursor
     * @return mixed
     */
    public function getDeltas($cursor)
    {
        $url = $this->apiServer . '/delta';
        $url = $url . '?' . http_build_query(['cursor' => $cursor]);

        try
        {
            $response =
            $this->apiClient->get($url, $this->createHeaders())
            ->getBody()->getContents();
        }

        catch (GuzzleHttp\Exception\RequestException $e)
        {
            return $e->getMessage();
        }

        // get json decode
        return $this->json($response);
    }

    // ------------------------------------------------------------------------------

    /**
     * revoke a account
     *
     * @return mixed
     */
    public function revoke()
    {
        $url = $this->apiServer . '/oauth/revoke';

        try
        {
            $response =
            $this->apiClient->post($url, $this->createHeaders())
            ->getBody()->getContents();
        }

        catch (GuzzleHttp\Exception\RequestException $e)
        {
            return $e->getMessage();
        }

        // get json decode
        return $this->json($response);
    }

    // ------------------------------------------------------------------------------

    /**
     * @return mixed
     */
    public function account()
    {
        $apiObj = new NylasAPIObject();
        $nsObj  = new Models\Account();

        $accountData = $this->getResource($nsObj, '', array());
        return $apiObj->_createObject($accountData->klass, $accountData->data);

    }

    // ------------------------------------------------------------------------------

    /**
     * @return NylasModelCollection
     */
    public function threads()
    {
        $msgObj = new Models\Thread($this);
        return new NylasModelCollection($msgObj, $this);
    }

    // ------------------------------------------------------------------------------

    /**
     * @return NylasModelCollection
     */
    public function messages()
    {
        $msgObj = new Models\Message($this);
        return new NylasModelCollection($msgObj, $this);
    }

    // ------------------------------------------------------------------------------

    /**
     * @return NylasModelCollection
     */
    public function drafts()
    {
        $msgObj = new Models\Draft($this);
        return new NylasModelCollection($msgObj, $this);
    }

    // ------------------------------------------------------------------------------

    public function labels()
    {
        $msgObj = new Models\Label($this);
        return new NylasModelCollection($msgObj, $this);
    }

    // ------------------------------------------------------------------------------

    /**
     * @return NylasModelCollection
     */
    public function files()
    {
        $msgObj = new Models\File($this);
        return new NylasModelCollection($msgObj, $this);
    }

    // ------------------------------------------------------------------------------

    /**
     * @return NylasModelCollection
     */
    public function contacts()
    {
        $msgObj = new Models\Contact($this);
        return new NylasModelCollection($msgObj, $this);
    }

    // ------------------------------------------------------------------------------

    /**
     * @return NylasModelCollection
     */
    public function calendars()
    {
        $msgObj = new Models\Calendar($this);
        return new NylasModelCollection($msgObj, $this);
    }

    // ------------------------------------------------------------------------------

    /**
     * @return NylasModelCollection
     */
    public function events()
    {
        $msgObj = new Models\Event($this);
        return new NylasModelCollection($msgObj, $this);
    }

    // ------------------------------------------------------------------------------

    /**
     * @param $klass
     * @param $filter
     * @return array
     */
    public function getResources($klass, $filter)
    {
        $url = $this->apiServer . '/' . $klass->collectionName;
        $url = $url . '?' . http_build_query($filter);

        try
        {
            $data =
            $this->apiClient->get($url, $this->createHeaders())
            ->getBody()->getContents();
        }

        catch (GuzzleHttp\Exception\RequestException $e)
        {
            return $e->getMessage();
        }

        $mapped = array();
        $data   = $this->json($data);

        foreach ($data as $i)
        {
            $mapped[] = clone $klass->_createObject($this, $i);
        }

        return $mapped;
    }

    // ------------------------------------------------------------------------------

    /**
     * @param $klass
     * @param $id
     * @param $filters
     * @return mixed
     */
    public function getResource($klass, $id, $filters)
    {
        if (array_key_exists('extra', $filters))
        {
            unset($filters['extra']);
        }

        $response = $this->getResourceRaw($klass, $id, $filters);

        return $klass->_createObject($this, $response);
    }

    // ------------------------------------------------------------------------------

    /**
     * @param $klass
     * @param $id
     * @param $filters
     * @return mixed|string
     */
    public function getResourceRaw($klass, $id, $filters)
    {
        $extra = '';

        if (array_key_exists('extra', $filters))
        {
            $extra = $filters['extra'];
            unset($filters['extra']);
        }

        $postfix = ($extra) ? '/' . $extra : '';

        $url = $this->apiServer . '/' . $klass->collectionName . '/' . $id . $postfix;
        $url = $url . '?' . http_build_query($filters);

        try
        {
            $data =
            $this->apiClient->get($url, $this->createHeaders())
            ->getBody()->getContents();
        }

        catch (GuzzleHttp\Exception\RequestException $e)
        {
            return $e->getMessage();
        }

        return $this->json($data);
    }

    // ------------------------------------------------------------------------------

    /**
     * @param $klass
     * @param $id
     * @param $filters
     * @return mixed|string
     */
    public function getResourceData($klass, $id, $filters)
    {
        $extra = '';

        if (array_key_exists('extra', $filters))
        {
            $extra = $filters['extra'];
            unset($filters['extra']);
        }

        $postfix = ($extra) ? '/' . $extra : '';

        $url = $this->apiServer . '/' . $klass->collectionName . '/' . $id . $postfix;
        $url = $url . '?' . http_build_query($filters);

        try
        {
            $data =
            $this->apiClient->get($url, $this->createHeaders())
            ->getBody()->getContents();
        }

        catch (GuzzleHttp\Exception\RequestException $e)
        {
            return $e->getMessage();
        }

        return $this->json($data);
    }

    // ------------------------------------------------------------------------------

    /**
     * @param $klass
     * @param $data
     * @return mixed
     */
    public function _createResource($klass, $data)
    {
        $url = $this->apiServer . '/' . $klass->collectionName;

        $payload = $this->createHeaders();

        $klass->collectionName == 'files' ?
        $payload['multipart'] = $data :
        $payload['json'] = $data;

        try
        {
            $response = $this->apiClient->post($url, $payload)
            ->getBody()->getContents();
        }

        catch (GuzzleHttp\Exception\RequestException $e)
        {
            return $e->getMessage();
        }

        $response = $this->json($response);

        return $klass->_createObject($this, $response);
    }

    // ------------------------------------------------------------------------------

    /**
     * @param $klass
     * @param $id
     * @param $data
     * @return mixed
     */
    public function _updateResource($klass, $id, $data)
    {
        $url = $this->apiServer . '/' . $klass->collectionName . '/' . $id;

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

        catch (GuzzleHttp\Exception\RequestException $e)
        {
            return $e->getMessage();
        }

        $response = $this->json($response);

        return $klass->_createObject($this, $response);
    }

    // ------------------------------------------------------------------------------

    /**
     * @param $klass
     * @param $id
     * @return mixed|string
     */
    public function _deleteResource($klass, $id)
    {
        $url = $this->apiServer . '/' . $klass->collectionName . '/' . $id;

        $payload = $this->createHeaders();

        try
        {
            $response =
            $this->apiClient->delete($url, $payload)
            ->getBody()->getContents();
        }

        catch (GuzzleHttp\Exception\RequestException $e)
        {
            return $e->getMessage();
        }

        return $this->json($response);
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

    /**
     * Parse the JSON response body and return an array
     *
     * @param  $content
     * @return array|string|int|bool|float
     * @throws \Exception if the response body is not in JSON format
     */
    private function json($content)
    {
        $data = json_decode($content, TRUE);

        if (JSON_ERROR_NONE !== json_last_error())
        {
            $msg = 'Unable to parse response body into JSON: ';

            error_log($msg . json_last_error());
        }

        return $data === NULL ? [] : $data;
    }

    // ------------------------------------------------------------------------------

}
