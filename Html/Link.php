<?php
class SNF_SNFeed_Html_Link {
	
	protected $_href        = '';
	protected $_text        = '';
	protected $_description = '';

	public function __construct($href, $text, $description) {
		$this->_href        = $href;
		$this->_text        = $text;
		$this->_description = $description;
	}

	public function __toString() {
		return sprintf(
			'<a href="%s" title="%s">%s</a><br />%s',
			$this->_href,
			$this->_text,
			$this->_text,
			$this->_description
		);
	}
}