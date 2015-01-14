<?php
abstract class SNF_SNFeed_Factory_Abstract {
	
	public function factory($type) {
		$className = $this->_getNamespace() . '_' . ucfirst(strtolower($type));
		
		if(class_exists($className, true)) {
			return new $className;
		}
		
		throw new Exception('Class not found: ' . $className);
	}
	
	abstract protected function _getNamespace();
}