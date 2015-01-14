<?php
class SNF_SNFeed_Reader_Facebook extends SNF_SNFeed_Reader_Curl {
	
	protected $_appId     = '';
	protected $_appSecret = '';

	protected $_until     = null;

    const URL_FEED = 'https://graph.facebook.com/#/feed';

	public function __construct() {
		$fields = array(
			'message',
            'object_id',
			'picture',
			'caption',
			'type',
			'link',
			'name',
			'description',
			'source',
			'properties'
		);
		
		$this->_init(self::URL_FEED, array(
			'fields'       => implode(',', $fields),
			'limit'        => 20,
			'access_token' => $this->_getAccessToken(),
			'since'        => $this->_getSeekFrom()
		));
	}
	
	protected function _prepareParams(array $params) {
		if(!$this->_isFirstPage() && $this->_until) {
			$params['until'] = $this->_until;
		}
		
		return $params;
	}
	
	protected function _isFirstPage() {
		return is_null($this->_until);
	}
	
	protected function _prepareResponse(array $response) {
		if(empty($response['paging']['next'])) {
			$this->nextPage(false);
		}
		
		if(!empty($response['data'])) {
			if($this->_isFirstPage()) {
				$this->_saveSeekFrom(strtotime($response['data'][0]['created_time']) + 1);
			}
			
			$last = end($response['data']);
			
			$this->_until = strtotime($last['created_time']) - 1;
		}
		
		return $response['data'];
	}
	
	protected function _getAccessToken() {
		$params = array(
			'client_id'     => $this->_appId,
			'client_secret' => $this->_appSecret,
			'grant_type'    => 'client_credentials'
		);
		
		$url = 'https://graph.facebook.com/oauth/access_token';
		
		$result = $this->_fetch($this->_getUrl($url, $params));
		
		parse_str($result['response'], $pairs);
			
		if(!array_key_exists('access_token', $pairs)) {
			throw new Exception('OAuth token response error : ' . $result['response']);
		}
		
		return $pairs['access_token'];
	}
	
	protected function _prepareAllIds() {
		$this->_init(self::URL_FEED, array(
			'fields'       => 'id',
			'limit'        => 20,
			'access_token' => $this->_getAccessToken(),
		));
		
		$this->_until = false;
		
		do {
			$this->_allIds = array_merge($this->_allIds, array_map(function($post) {
				return $post['id'];
			}, $this->readPage()));
		} while($this->nextPage());
	}
	
	public function getNamespace() {
		return SNF_SNFeed_Type::FACEBOOK;
	}
}