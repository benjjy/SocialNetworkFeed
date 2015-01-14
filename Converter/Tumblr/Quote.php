<?php
class SNF_SNFeed_Converter_Tumblr_Quote extends SNF_SNFeed_Converter_Tumblr_Abstract {

	public function toElement($post) {
		$element = $this->_initElement($post);

		$element->set('description', new SNF_SNFeed_Html_Description($post['text'], $post['source']));

		return $element;
	}
}