<?php
class SNF_SNFeed_Converter_Facebook extends SNF_SNFeed_Factory_Abstract implements SNF_SNFeed_Converter_Interface {
	
	public function toElement($post) {
		try {
			return $this->factory($post['type'])->toElement($post);
		} catch(Exception $e) {
			return new SNF_SNFeed_Element;
		}
	}
	
	protected function _getNamespace() {
		return 'SNF_SNFeed_Converter_Facebook';
	}
}