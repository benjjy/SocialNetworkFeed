<?php
class SNF_SNFeed_Converter_Soundcloud_Track implements SNF_SNFeed_Converter_Interface {
	
	protected $_playlist = '';
	
	public function setPlaylist($name) {
		$this->_playlist = $name;
	}
	
	public function toElement($post) {
		$thumbnail = empty($post['artwork_url']) ? $post['user']['avatar_url'] : $post['artwork_url'];
		
		return new SNF_SNFeed_Element(array(
			'sn_type'     => SNF_SNFeed_Type::SOUNDCLOUD,
			'sn_id'       => $post['id'],
			'sn_group'    => $this->_playlist,
			'posted_at'   => strtotime($post['created_at']),
			'title'       => $post['title'],
			'description' => new SNF_SNFeed_Html_Description(new SNF_SNFeed_Html_Soundcloud_Iframe($post['uri']), $post['description']),
			'thumbnail'   => $thumbnail
		));
	}
}