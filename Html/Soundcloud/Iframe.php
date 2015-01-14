<?php
class SNF_SNFeed_Html_Soundcloud_Iframe {

	protected $_uri = '';

	public function __construct($uri) {
		$this->_uri = $uri;
	}

	public function __toString() {
		return sprintf('<iframe src="http://w.soundcloud.com/player/?url=%s&show_artwork=true" frameborder="no" scrolling="no" height="166" width="542"></iframe>', rawurlencode($this->_uri));
	}
}