<?php
class SNF_SNFeed_Converter_Soundcloud implements SNF_SNFeed_Converter_Interface {
	
	public function toElement($post) {
		$converter = new SNF_SNFeed_Converter_Soundcloud_Track;
		
		$converter->setPlaylist($post['title']);
		
		$tracks = array_map(array($converter, 'toElement'), $post['tracks']);
		
		return $tracks;
	}
	
	public function postConvert($posts) {
		return call_user_func_array('array_merge', $posts);
	}
}