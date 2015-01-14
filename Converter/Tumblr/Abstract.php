<?php
abstract class SNF_SNFeed_Converter_Tumblr_Abstract implements SNF_SNFeed_Converter_Interface {

	protected function _initElement(array $post) {
		return new SNF_SNFeed_Element(array(
			'sn_type'   => SNF_SNFeed_Type::TUMBLR,
			'sn_id'     => $post['id'],
			'posted_at' => $post['timestamp']
		));
	}
}