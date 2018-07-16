<?php namespace Nylas;

use Nylas\OAuth;
use Nylas\Models;

/**
 * ----------------------------------------------------------------------------------
 * Nylas
 * ----------------------------------------------------------------------------------
 *
 * @package Nylas
 * @author lanlin
 * @change 2017-11-16
 */
class Nylas
{

    // ------------------------------------------------------------------------------

    /**
     * @var array
     */
    private $options = [];

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
        $this->options = $options ? $options : [];
    }

    // ------------------------------------------------------------------------------

    /**
     * get account collection handle
     *
     * @param array $options
     * @return \Nylas\OAuth\OAuth
     */
    public function oauth(array $options = null)
    {
        $options = array_merge($this->options, $options ? $options : []);

        return new OAuth\OAuth($options);
    }

    // ------------------------------------------------------------------------------

    /**
     * get account collection handle
     *
     * @param array $options
     * @return \Nylas\Models\Account
     */
    public function account(array $options = null)
    {
        $options = array_merge($this->options, $options ? $options : []);

        return new Models\Account($options);
    }

    // ------------------------------------------------------------------------------

    /**
     * get threads collection handle
     *
     * @param array $options
     * @return \Nylas\Models\Thread
     */
    public function threads(array $options = null)
    {
        $options = array_merge($this->options, $options ? $options : []);

        return new Models\Thread($options);
    }

    // ------------------------------------------------------------------------------

    /**
     * get messages collection handle
     *
     * @param array $options
     * @return \Nylas\Models\Message
     */
    public function messages(array $options = null)
    {
        $options = array_merge($this->options, $options ? $options : []);

        return new Models\Message($options);
    }

    // ------------------------------------------------------------------------------

    /**
     * get drafts collection handle
     *
     * @param array $options
     * @return \Nylas\Models\Draft
     */
    public function drafts(array $options = null)
    {
        $options = array_merge($this->options, $options ? $options : []);

        return new Models\Draft($options);
    }

    // ------------------------------------------------------------------------------

    /**
     * get labels collection handle
     *
     * @param array $options
     * @return \Nylas\Models\Label
     */
    public function labels(array $options = null)
    {
        $options = array_merge($this->options, $options ? $options : []);

        return new Models\Label($options);
    }

    // ------------------------------------------------------------------------------

    /**
     * get folders collection handle
     *
     * @param array $options
     * @return \Nylas\Models\Folder
     */
    public function folders(array $options = null)
    {
        $options = array_merge($this->options, $options ? $options : []);

        return new Models\Folder($options);
    }

    // ------------------------------------------------------------------------------

    /**
     * get files collection handle
     *
     * @param array $options
     * @return \Nylas\Models\File
     */
    public function files(array $options = null)
    {
        $options = array_merge($this->options, $options ? $options : []);

        return new Models\File($options);
    }

    // ------------------------------------------------------------------------------

    /**
     * get contacts collection handle
     *
     * @param array $options
     * @return \Nylas\Models\Contact
     */
    public function contacts(array $options = null)
    {
        $options = array_merge($this->options, $options ? $options : []);

        return new Models\Contact($options);
    }

    // ------------------------------------------------------------------------------

    /**
     * get calendars collection handle
     *
     * @param array $options
     * @return \Nylas\Models\Calendar
     */
    public function calendars(array $options = null)
    {
        $options = array_merge($this->options, $options ? $options : []);

        return new Models\Calendar($options);
    }

    // ------------------------------------------------------------------------------

    /**
     * get events collection handle
     *
     * @param array $options
     * @return \Nylas\Models\Event
     */
    public function events(array $options = null)
    {
        $options = array_merge($this->options, $options ? $options : []);

        return new Models\Event($options);
    }

    // ------------------------------------------------------------------------------

    /**
     * get deltas collection handle
     *
     * @param array $options
     * @return \Nylas\Models\Delta
     */
    public function deltas(array $options = null)
    {
        $options = array_merge($this->options, $options ? $options : []);

        return new Models\Delta($options);
    }

    // ------------------------------------------------------------------------------

    /**
     * webhook X-Nylas-Signature header verification
     *
     * @link https://docs.nylas.com/reference#receiving-notifications
     *
     * @param string      $code
     * @param string      $data
     * @param string|NULL $secret
     * @return bool
     */
    public function xSignatureVerification($code, $data, $secret = null)
    {
        $key  = $secret ? $secret : $this->options['app_secret'];

        $hash = hash_hmac('sha256', $data, $key);

        return $code === $hash;
    }

    // ------------------------------------------------------------------------------

}
