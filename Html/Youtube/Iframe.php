<?php
class SNF_SNFeed_Html_Youtube_Iframe {

	protected $_videoId = '';
	protected $_width   = 500;
	protected $_height  = 310;

	public function __construct($videoId) {
		$this->_videoId = $videoId;
	}
	
	public function setDimensions($width, $height) {
		$this->_width  = $width;
		$this->_height = $height;
		
		return $this;
	}

	public function __toString() {
		return sprintf('<iframe width="%s" height="%s" src="http://www.youtube.com/embed/%s?autoplay=0" frameborder="0"></iframe>',
			$this->_width, $this->_height, $this->_videoId);
	}
}