<?php
class SNF_SNFeed_Reader_Googleplus extends SNF_SNFeed_Reader_Curl
{

    protected $_apiKey = '';

    protected $_pageToken = null;

    const URL_FEED = '';

    public function __construct()
    {
        $this->_init(self::URL_FEED, array(
            'fields' => 'nextPageToken,items(id,title,published,url,object(content,attachments(displayName,content,url,image,embed,fullImage)))',
            'key' => $this->_apiKey,
            'prettyPrint' => false,
            'maxResults' => 20
        ));
    }

    protected function _prepareParams(array $params)
    {
        if (!is_null($this->_pageToken)) {
            $params['pageToken'] = $this->_pageToken;
        }

        return $params;
    }

    protected function _isFirstPage()
    {
        return is_null($this->_pageToken);
    }

    protected function _prepareResponse(array $response)
    {
        $posts = $response['items'];
        $actual = array_filter($posts, array($this, '_filterPost'));
        $actual = array_reverse($actual);
        if ($posts && $this->_isFirstPage()) {
            $this->_saveSeekFrom(strtotime($posts[0]['published']));
        }

        if (empty($response['nextPageToken'])) {
            $this->nextPage(false);
        } else {
            $this->_pageToken = $response['nextPageToken'];
        }

        if (!$actual) {
            $this->nextPage(false);
        }

        return $actual;
    }

    protected function _prepareAllIds()
    {
        $this->_init(self::URL_FEED, array(
            'fields' => 'nextPageToken,items(id,published)',
            'key' => $this->_apiKey,
            'prettyPrint' => false,
            'maxResults' => 20
        ));

        $this->_pageToken = false;

        do {
            $this->_allIds = array_merge($this->_allIds, array_map(function($post)
            {
                return $post['id'];
            }, $this->readPage()));
        } while ($this->nextPage());
    }

    public function getNamespace()
    {
        return SNF_SNFeed_Type::GOOGLEPLUS;
    }

    protected function _filterPost(array $post)
    {
        return strtotime($post['published']) > $this->_getSeekFrom();
    }
}