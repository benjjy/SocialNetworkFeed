<?php
class SNF_SNFeed_Converter_Youtube implements SNF_SNFeed_Converter_Interface {

	public function toElement($post) {
		/* @var $post Zend_Gdata_YouTube_VideoEntry */
		$description = new SNF_SNFeed_Html_Description(new SNF_SNFeed_Html_Youtube_Iframe($post->getVideoId()), $post->getVideoDescription());
		
		return new SNF_SNFeed_Element(array(
			'sn_type'     => SNF_SNFeed_Type::YOUTUBE,
			'sn_id'       => $post->getVideoId(),
			'title'       => $post->getVideoTitle(),
			'description' => $description,
			'posted_at'   => strtotime($post->getUpdated()),
			'thumbnail'   => $this->_matchThumbnail($post->getVideoThumbnails(), 67, 50)
		));
	}
	
	public function _matchThumbnail(array $thumbnails, $width, $height) {
		$thumbnails = array_filter($thumbnails, function($thumbnail) use($width, $height) {
			return empty($thumbnail['time']) && $thumbnail['height'] >= $height && $thumbnail['width'] >= $width;
		});
		
		$thumbnails = array_map(function($thumbnail) {
			return array(
				'url'   => $thumbnail['url'],
				'scale' => $thumbnail['width'] / $thumbnail['height']
			);
		}, $thumbnails);
		
		$scale = $width / $height;
		
		usort($thumbnails, function($l, $r) use($scale) {
			return abs($l['scale'] - $scale) - abs($r['scale'] - $scale);
		});
		
		return $thumbnails[0]['url'];
	}
}