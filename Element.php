<?php
class SNF_SNFeed_Element {

	protected $_data = array(
		'sn_type'     => null,
		'sn_id'       => null,
		'sn_group'    => null,
		'title'       => '',
		'description' => '',
		'posted_at'   => null,
		'images'      => array(),
		'links'       => array(),
		'videos'      => array(),
		'thumbnail'   => '',
        'urls'        => array(),
        'feed_type'   => 'simple',
        'th_type'     => false,
        'post_url'    => '',
	);

	public function __construct(array $data = array()) {
		if($data) {
			$this->_data = array_replace($this->_data, array_intersect_key($data, $this->_data));
		}
	}

	public function get($param) {
		return array_key_exists($param, $this->_data) ? $this->_data[$param] : null;
	}

	public function set($param, $value) {
		if(array_key_exists($param, $this->_data)) {
			$this->_data[$param] = $value;
		}

		return $this;
	}
	
	public function addImage($src) {
		$this->_addArray('images', $src);
		
		return $this;
	}
	
	public function removeImages() {
		$this->set('images', array());
		
		return $this;
	}
	
	public function addLink($href, $text, $description = '') {
		$this->_addArray('links', compact('href', 'text', 'description'));
		
		return $this;
	}

    public function addUrls($urls = array()) {
        $this->_addArray('urls', $urls);
        return $this;
    }

	public function addVideo($source, $href, $text, $description = '') {
		$this->_addArray('videos', compact('source', 'href', 'text', 'description'));
		
		return $this;
	}
	
	protected function _addArray($type, $value) {
		$values = $this->get($type);
		$values[] = $value;
		$this->set($type, $values);
	}
}