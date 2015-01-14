<?php
class SNF_SNFeed_Reader_Tumblr extends SNF_SNFeed_Reader_Curl {
	
	const MSG_OK          = 'ok';
	const STATE_PUBLISHED = 'published';
	
	protected $_apiKey = '#';
	protected $_offset = 0;
	protected $_seekTo = PHP_INT_MAX;
	
	public function __construct() {
		$this->_init('http://api.tumblr.com/v2/blog/#username.tumblr.com/posts', array(
			'limit'   => 20,
			'api_key' => $this->_apiKey
		));
	}
	
	protected function _prepareParams(array $params) {
		$params['offset'] = $this->_offset;
		
		return $params;
	}
	
	protected function _isFirstPage() {
		return $this->_offset === 0;
	}
	
	protected function _prepareResponse(array $response) {
		if(strcasecmp($response['meta']['msg'], self::MSG_OK)) {
			throw new Exception('API error: ' . $response['meta']['msg']);
		}
		
		$posts  = $response['response']['posts'];
		$actual = array_filter($posts, array($this, '_filterPost'));
		
		if($posts) {
			if($this->_isFirstPage()) {
				$this->_saveSeekFrom($posts[0]['timestamp']);
			}
			
			$last = end($posts);
			
			$this->_seekTo = $last['timestamp'];
		}
		
		$this->_offset += count($posts);
		
		if(!$actual) {
			$this->nextPage(false);
		}
		
		return $actual;
	}
	
	public function getNamespace() {
		return SNF_SNFeed_Type::TUMBLR;
	}
	
	protected function _filterPost(array $post) {
		return !strcasecmp($post['state'], self::STATE_PUBLISHED) &&
			$post['timestamp'] > $this->_getSeekFrom() &&
			$post['timestamp'] < $this->_seekTo;
	}
	
	protected function _prepareAllIds() {
		$this->_offset   = false;
		$this->_seekFrom = 0;
		
		do {
			$this->_allIds = array_merge($this->_allIds, array_map(function($post) {
				return $post['id'];
			}, $this->readPage()));
		} while($this->nextPage());
	}
}