<?php
class SNF_SNFeed_Reader_Soundcloud extends SNF_SNFeed_Reader_Curl {

	protected $_clientId = '';

	public function __construct() {
		$this->_init('http://api.soundcloud.com/users/#id/playlists.json', array(
			'client_id' => $this->_clientId
		));
	}

	protected function _prepareParams(array $params) {
		return $params;
	}

	protected function _prepareResponse(array $response) {
		$this->nextPage(false);
		
		$tracks = array_map(function($row) {
			return $row['tracks'];
		}, $response);
		
		$tracks = call_user_func_array('array_merge', $tracks);
		
		$this->_allIds = array_map(function($row) {
			return $row['id'];
		}, $tracks);
		
		return $response;
	}

	protected function _isFirstPage() {
		return is_null($this->_seekTo);
	}

	public function getNamespace() {
		return SNF_SNFeed_Type::SOUNDCLOUD;
	}
	
	protected function _formatTimestamp($timestamp) {
		return date('Y-m-d h:i:s', $timestamp);
	}
	
	protected function _prepareAllIds() {}
}