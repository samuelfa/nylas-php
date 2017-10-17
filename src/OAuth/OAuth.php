<?php namespace Nylas\OAuth;

use Nylas\Shims\Model;
use GuzzleHttp\Exception\RequestException;

/**
 * ----------------------------------------------------------------------------------
 * OAuth
 * ----------------------------------------------------------------------------------
 *
 * @package Nylas
 * @author lanlin
 * @change 2017-11-12
 */
class OAuth extends Model
{

    // ------------------------------------------------------------------------------

    /**
     * create auth url
     *
     * @param string $redirectUri
     * @param string $loginHint
     * @param string $state (custom params would be sent back)
     * @return string
     */
    public function createAuthURL(string $redirectUri, string $loginHint = null, string $state = null)
    {
        $args =
        [
            'scope'         => 'email',
            'state'         => $state ?? $this->generateId(),
            'client_id'     => $this->appID,
            'login_hint'    => $loginHint,
            'redirect_uri'  => $redirectUri,
            'response_type' => 'code'
        ];

        return $this->apiServer . '/oauth/authorize?' . http_build_query($args);
    }

    // ------------------------------------------------------------------------------

    /**
     * get auth token
     *
     * @param string $code
     * @return string
     * @throws \Exception
     */
    public function getAuthToken(string $code)
    {
        $args =
        [
            'code'          => $code,
            'client_id'     => $this->appID,
            'grant_type'    => 'authorization_code',
            'client_secret' => $this->appSecret
        ];

        $url = $this->apiServer . '/oauth/token';

        try
        {
            $response = $this->client()
            ->post($url, ['form_params' => $args])
            ->getBody()
            ->getContents();
        }

        catch (RequestException $e)
        {
            $msg = "Nylas::getAuthToken: {$e->getMessage()}";

            throw new \Exception($msg);
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
     * revoke a account
     *
     * @return mixed
     * @throws \Exception
     */
    public function revoke()
    {
        $url = $this->apiServer . '/oauth/revoke';

        try
        {
            $response = $this->client()
            ->post($url, $this->createHeaders())
            ->getBody()
            ->getContents();
        }

        catch (RequestException $e)
        {
            $msg = "Nylas::revoke: {$e->getMessage()}";

            throw new \Exception($msg);
        }

        // get json decode
        return $this->json($response);
    }

    // ------------------------------------------------------------------------------

}
