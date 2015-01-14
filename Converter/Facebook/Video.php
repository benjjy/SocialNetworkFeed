<?php
class SNF_SNFeed_Converter_Facebook_Video extends SNF_SNFeed_Converter_Facebook_Status {
	
	public function toElement($post) {
		$element = parent::toElement($post);
		
		if(!empty($post['link'])) {
			$params = array(
				$post['link'],
				empty($post['name']) ? '' : $post['name'],
				empty($post['description']) ? '' : $post['description'],
			);
			
			if(!empty($post['source'])) {
				$post['source'] = str_replace('autoplay=1', 'autoplay=0', $post['source']);
				array_unshift($params, $post['source']);
				
				$element->removeImages();
				call_user_func_array(array($element, 'addVideo'), $params);
			} else {
				call_user_func_array(array($element, 'addLink'), $params);
			}
		}

        $element->set('feed_type', 'video');
		return $element;
	}
}