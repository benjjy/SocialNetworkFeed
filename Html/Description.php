<?php
class SNF_SNFeed_Html_Description {

	protected $_description = array();

	public function __construct() {
		$args = func_get_args();

		if(!empty($args)) {
			if(is_array($args[0])) {
				$args = $args[0];
			}

			$this->_description = $args;
		}
	}

	public function __toString() {
		return implode('<br />', array_filter($this->_description));
	}
}