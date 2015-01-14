<?php
class SNF_SNFeed_Converter_Tumblr_Photo extends SNF_SNFeed_Converter_Tumblr_Abstract {

	protected $_maxWidth = 500;

	public function toElement($post) {
		$element = $this->_initElement($post);

		$element->set('description', $post['caption']);

		$maxWidth = $this->_maxWidth;
		$photos   = $post['photos'][0]['alt_sizes'];

		usort($photos, function($l, $r) use($maxWidth) {
			return abs($l['width'] - $maxWidth) - abs($r['width'] - $maxWidth);
		});

		$element->addImage($photos[0]['url']);

		return $element;
	}
}