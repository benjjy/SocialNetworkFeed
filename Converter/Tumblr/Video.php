<?php
class SNF_SNFeed_Converter_Tumblr_Video extends SNF_SNFeed_Converter_Tumblr_Abstract {

	protected $_maxWidth = 500;

	public function toElement($post) {
		$element = $this->_initElement($post);

		$maxWidth = $this->_maxWidth;
		$videos   = $post['player'];

		usort($videos, function($l, $r) use($maxWidth) {
			return abs($l['width'] - $maxWidth) - abs($r['width'] - $maxWidth);
		});

		$element->set('description', new SNF_SNFeed_Html_Description($post['caption'], $videos[0]['embed_code']));

		return $element;
	}
}