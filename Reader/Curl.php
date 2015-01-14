<?php
abstract class SNF_SNFeed_Reader_Curl extends SNF_SNFeed_Reader_Abstract {
	
	const HTTP_OK = 200;
	
	protected $_baseUrl    = '';
	protected $_json       = true;
	protected $_baseParams = array();
	
	public function readPage() {
		$params = $this->_prepareParams($this->_baseParams);
		
		$url = $this->_getUrl($this->_baseUrl, $params);
		
		$result = $this->_fetch($url);
		
		if($result['code'] != self::HTTP_OK) {
			throw new Exception('HTTP response code: ' . $result['code'] . '; response: ' . $result['response']);
		}
		
		if(!$result['response']) {
			throw new Exception('HTTP empty response');
		}
		
		if($this->_json) {
			$result['response'] = preg_replace('#("\\s*\\:\\s*)(\\d{8,})(\\s*,)#', '$1"$2"$3', $result['response']); // convert long int to string
			$result['response'] = json_decode($result['response'], true);
			
			if(is_null($result['response'])) {
				throw new Exception('Response json_decode error: ' . json_last_error());
			}
		}
		
		return $this->_prepareResponse($result['response']);
	}
	
	protected function _init($baseUrl, array $baseParams, $json = true) {
		$this->_baseUrl    = $baseUrl;
		$this->_baseParams = $baseParams;
		$this->_json       = !!$json;
		
		return $this;
	}
	
	protected function _getUrl($url, array $params = array()) {
		if($params) {
			$url .= '?' . http_build_query($params);
		}
		
		return $url;
	}
	
	protected function _fetch($url) {
		$curl = curl_init();
		
		curl_setopt_array($curl, array(
			CURLOPT_FAILONERROR    => true,
			CURLOPT_HEADER         => false,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_CONNECTTIMEOUT => 5,
			CURLOPT_URL            => $url,
			CURLOPT_SSL_VERIFYPEER => false
		));
		
		$result = array();
		
		$result['response'] = curl_exec($curl);
		
		if(curl_error($curl)) {
			throw new Exception('cURL error: ' . curl_error($curl) . '; URL: ' . $url . '; Response: ' . $result['response']);
		}
			
		$result['code'] = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		
		curl_close($curl);
		
		return $result;
	}
	
	abstract protected function _prepareParams(array $params);
	abstract protected function _prepareResponse(array $response);
}