<?php
class SNF_SNFeed_Converter_Facebook_Status extends SNF_SNFeed_Converter_Facebook_Abstract {

	public function toElement($post) {
		$element = $this->_initElement($post);

		$description = empty($post['message']) ? '' : $post['message'];

		$element->set('description', nl2br($description, true));
        $element->set('feed_type', 'status');
		return $element;
	}
}