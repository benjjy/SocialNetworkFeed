<?php
class SNF_SNFeed_Converter_Tumblr_Audio extends SNF_SNFeed_Converter_Tumblr_Abstract {

	public function toElement($post) {
		$element = $this->_initElement($post);

		$element->set('title', $post['track_name']);
		$element->set('description', new SNF_SNFeed_Html_Description($post['caption'], $post['player']));

		$element->addLink($post['source_url'], $post['source_title']);

		return $element;
	}
}