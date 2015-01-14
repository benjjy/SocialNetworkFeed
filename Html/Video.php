<?php
class SNF_SNFeed_Html_Video extends SNF_SNFeed_Html_Link {
	
	protected $_source = '';
	
	public function __construct($source, $href, $text, $description) {

        if (is_array($source) && isset($source['url'])) {
            $this->_source = $source['url'];
        } else {
            $this->_source = $source;
        }

		parent::__construct($href, $text, $description);
	}
	
	public function __toString() {
		return sprintf('<embed src="%s" wmode="opaque" allowfullscreen="true" type="application/x-shockwave-flash"></embed><br />', $this->_source) .
			parent::__toString();
	}
}