<?php
class SNF_SNFeed_Converter_Tumblr_Link extends SNF_SNFeed_Converter_Tumblr_Abstract {

	public function toElement($post) {
		$element = $this->_initElement($post);

		$element->set('description', $post['description']);
		$element->addLink($post['url'], $post['title']);

		return $element;
	}
}