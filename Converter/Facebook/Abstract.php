<?php
abstract class SNF_SNFeed_Converter_Facebook_Abstract implements SNF_SNFeed_Converter_Interface {
	
	protected function _initElement(array $post) {
		$element = new SNF_SNFeed_Element(array(
			'sn_type'   => SNF_SNFeed_Type::FACEBOOK,
			'sn_id'     => $post['id'],
			'posted_at' => strtotime($post['created_time']),
			'urls'      => $post['link'],
		));
		
		if(array_key_exists('picture', $post)) {
			$element->addImage($post['picture']);
		}
		
		return $element;
	}
}