<?php
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__DIR__) . '/lib/');
require_once 'Zend/Loader.php';

class SNF_SNFeed_Reader_Youtube extends SNF_SNFeed_Reader_Abstract {
	
	public function readPage() {
		$loader = new Zend_Loader;
		$loader->loadClass('Zend_Gdata_YouTube');
		
		$youtube = new Zend_Gdata_YouTube;
		$youtube->setMajorProtocolVersion(2);
		
		$this->nextPage(false);
		
		$entry = $youtube->getUserUploads('#username')->getEntry();
		
		$this->_allIds = array_map(function(Zend_Gdata_YouTube_VideoEntry $videoEntry) {
			return $videoEntry->getVideoId();
		}, $entry);
		
		return $entry;
	}
	
	public function getNamespace() {
		return SNF_SNFeed_Type::YOUTUBE;
	}
	
	protected function _prepareAllIds() {}
}