<?php
class SNF_SNFeed_Converter_Twitter implements SNF_SNFeed_Converter_Interface {
	
	public function toElement($post) {
		$data = new SNF_SNFeed_Element(array(
			'sn_type'     => SNF_SNFeed_Type::TWITTER,
			'sn_id'       => $post['id_str'],
			'description' => $post['text'],
			'posted_at'   => strtotime($post['created_at']),
            'urls'        => $post['entities']
		));
        return $data;
	}
}