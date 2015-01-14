<?php
class SNF_SNFeed_Reader_Twitter extends SNF_SNFeed_Reader_Abstract
{

    protected $_consumerKey = '';
    protected $_consumerSecret = '';

    protected $_accessToken = '';
    protected $_accessTokenSecret = '';

    protected $_twitter = null;
    protected $_maxId = null;

    public function readPage()
    {
        $twitter = $this->_getTwitter();

        $params = array(
            'count' => 20,
            'trim_user' => true,
            'contributor_details' => false,
            'include_rts' => true,
        );

        if ($this->_getSeekFrom()) {
            $params['since_id'] = $this->_getSeekFrom();
        }

        if (!is_null($this->_maxId)) {
            $params['max_id'] = $this->_maxId;
        }

        $response = $twitter->get('statuses/user_timeline', $params);

        if (!empty($response['error'])) {
            throw new Exception('API error: ' . $response['error']);
        }

        if (!$response) {
            $this->nextPage(false);
            return array();
        }

        if ($this->_isFirstPage()) {
            $this->_saveSeekFrom($response[0]['id_str']);
        }

        $last = end($response);
        $this->_maxId = bcsub($last['id_str'], 1);

        return $response;
    }

    protected function _isFirstPage()
    {
        return is_null($this->_maxId);
    }

    public function getNamespace()
    {
        return SNF_SNFeed_Type::TWITTER;
    }

    protected function _getTwitter()
    {
        if (is_null($this->_twitter)) {
            require_once dirname(__DIR__) . '/lib/twitteroauth/twitteroauth.php'; // crashed recursion?

            $this->_twitter = new TwitterOAuth($this->_consumerKey, $this->_consumerSecret, $this->_accessToken, $this->_accessTokenSecret);
        }

        return $this->_twitter;
    }

    protected function _getSeekFrom()
    {
        if (is_null($this->_seekFrom)) {
            $this->_seekFrom = get_option('SNF_snfeed.reader.' . $this->getNamespace() . '.seek_from', 0);
        }
        return $this->_seekFrom;
    }

    protected function _prepareAllIds()
    {
        $params = array(
            'count' => 20,
            'trim_user' => true,
            'contributor_details' => false,
            'include_rts' => true,
        );

        $twitter = $this->_getTwitter();

        do {
            $response = $twitter->get('statuses/user_timeline', $params);

            $this->_allIds = array_merge($this->_allIds, array_map(function($row)
            {
                return $row['id_str'];
            }, $response));

            $last = end($response);
            $params['max_id'] = bcsub($last['id_str'], 1);
        } while ($response);
    }
}