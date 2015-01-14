<?php
class SNF_SNFeed_Converter_Tumblr_Text extends SNF_SNFeed_Converter_Tumblr_Abstract {

	public function toElement($post) {
		$element = $this->_initElement($post);

		$element->set('title', $post['title']);
		$element->set('description', $post['body']);

		return $element;
	}
}