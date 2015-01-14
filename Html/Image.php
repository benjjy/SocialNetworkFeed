<?php
class SNF_SNFeed_Html_Image {
	
	protected $_src = '';
	
	public function __construct($src) {
		$this->_src = $src;
	}
	
	public function __toString() {
        if (strpos($this->_src, 'http') === false) {
            return "";
        }
		return sprintf('<img src="%s" title="%s" />', $this->_src, $this->_src);
	}
}