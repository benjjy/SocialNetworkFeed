<?php
class SNF_SNFeed_Converter_Facebook_Link extends SNF_SNFeed_Converter_Facebook_Abstract {
	
	public function toElement($post) {
		$element = $this->_initElement($post);
		
		if(array_key_exists('message', $post)) {
			$element->set('description', nl2br($post['message'], true));
		}
		
		if(!empty($post['link']) && !empty($post['name'])) {
			$description = empty($post['description']) ? (empty($post['caption']) ? '' : $post['caption']) : $post['description'];
			$element->addLink($post['link'], $post['name'], $description);
		}
        $element->set('feed_type', 'link');
		return $element;
	}
}