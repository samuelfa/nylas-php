<?php namespace Nylas\Models;

use Nylas\Shims\Model;
use GuzzleHttp\Exception\RequestException;

/**
 * ----------------------------------------------------------------------------------
 * Account
 * ----------------------------------------------------------------------------------
 *
 * @package Nylas\Models
 * @author lanlin
 * @change 2017-10-12
 */
class Account extends Model
{

    // ------------------------------------------------------------------------------

    public $collectionName = 'account';

    // ------------------------------------------------------------------------------

    /**
     * get account base info
     *
     * @param string $token
     * @return array
     * @throws \Exception
     */
    public function getAccount(string $token = null)
    {
        $url = $this->apiServer . '/account';
        $this->apiToken = $this->apiToken ? $this->apiToken : $token;

        try
        {
            $response = $this->client()
            ->get($url, $this->createHeaders())
            ->getBody()
            ->getContents();
        }

        catch (RequestException $e)
        {
            $msg = "Nylas::getAccount: {$e->getMessage()}";

            throw new \Exception($msg);
        }

        return $this->json($response);
    }

    // ------------------------------------------------------------------------------

    /**
     * get all labels of the account
     *
     * @param string $token
     * @return mixed
     * @throws \Exception
     */
    public function getLabels(string $token = null)
    {
        $url = $this->apiServer . '/labels';
        $this->apiToken = $this->apiToken ? $this->apiToken : $token;

        try
        {
            $response = $this->client()
            ->get($url, $this->createHeaders())
            ->getBody()
            ->getContents();
        }

        catch (RequestException $e)
        {
            $msg = "Nylas::getLabels: {$e->getMessage()}";

            throw new \Exception($msg);
        }

        return $this->json($response);
    }

    // ------------------------------------------------------------------------------

    /**
     * get all folders of the account
     *
     * @param  $token
     * @return mixed
     * @throws \Exception
     */
    public function getFolders(string $token = null)
    {
        $url = $this->apiServer . '/folders';
        $this->apiToken = $this->apiToken ? $this->apiToken : $token;

        try
        {
            $response = $this->client()
            ->get($url, $this->createHeaders())
            ->getBody()
            ->getContents();
        }

        catch (RequestException $e)
        {
            $msg = "Nylas::getFolders: {$e->getMessage()}";

            throw new \Exception($msg);
        }

        return $this->json($response);
    }

    // ------------------------------------------------------------------------------

    /**
     * get latest cursor for current account
     *
     * @return mixed
     * @throws \Exception
     */
    public function getLatestCursor()
    {
        $url = $this->apiServer . '/delta/latest_cursor';

        try
        {
            $response = $this->client()
            ->post($url, $this->createHeaders())
            ->getBody()
            ->getContents();
        }

        catch (RequestException $e)
        {
            $msg = "Nylas::getLatestCursor: {$e->getMessage()}";

            throw new \Exception($msg);
        }

        // get json decode
        return $this->json($response);
    }

    // ------------------------------------------------------------------------------

    /**
     * get deltas
     *
     * @param string $cursor
     * @return mixed
     * @throws \Exception
     */
    public function getDeltas($cursor)
    {
        $url = $this->apiServer . '/delta';
        $url = $url . '?' . http_build_query(['cursor' => $cursor]);

        try
        {
            $response = $this->client()
            ->get($url, $this->createHeaders())
            ->getBody()
            ->getContents();
        }

        catch (RequestException $e)
        {
            $msg = "Nylas::getDeltas: {$e->getMessage()}";

            throw new \Exception($msg);
        }

        // get json decode
        return $this->json($response);
    }

    // ------------------------------------------------------------------------------

}
