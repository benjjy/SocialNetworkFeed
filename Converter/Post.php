<?php
class SNF_SNFeed_Converter_Post {

	protected static $_postTypes = array(
		SNF_SNFeed_Type::FACEBOOK   => 'SNF_snfeed',
		SNF_SNFeed_Type::TWITTER    => 'SNF_snfeed',
		SNF_SNFeed_Type::TUMBLR     => 'SNF_snfeed',
		SNF_SNFeed_Type::GOOGLEPLUS => 'SNF_snfeed',
		SNF_SNFeed_Type::SOUNDCLOUD => 'SNF_music',
		SNF_SNFeed_Type::YOUTUBE    => 'SNF_video',
	);

	public function fromElement(SNF_SNFeed_Element $element) {
		$description = $this->_prepareDescription($element);
		
		if(!$description) {
			return false;
		}
		
		$title = $element->get('title');
		
		if(!$title) {
			$title = $this->_titleFromDescription($description);
		}
		
		if(!$title) {
			$title = $this->_defaultTitle($element);
		}
		
		$urlKey = $this->_urlKeyFromTitle($title);

		$info = array(
			'post' => array(
				'comment_status' => 'open',
				'post_status'    => 'publish',
				'post_content'   => $description,
				'post_excerpt'   => $description,
				'post_date_gmt'  => gmdate('Y-m-d H:i:s', $element->get('posted_at')),
				'post_date'      => date('Y-m-d H:i:s', $element->get('posted_at')),
				'post_name'      => $urlKey,
				'post_title'     => $title,
				'post_type'      => self::$_postTypes[$element->get('sn_type')]
			),
			'sn_id'    => $this->getSnId($element->get('sn_type'), $element->get('sn_id')),
			'sn_type'  => $element->get('sn_type'),
			'sn_group' => $element->get('sn_group'),
		);
		
		if($element->get('thumbnail')) {
			$info['thumbnail'] = $element->get('thumbnail');
		}

        if ($element->get('th_type')) {
            $info['th_type'] = $element->get('th_type');
        }

        if ($element->get('feed_type')) {
            $info['feed_type'] = $element->get('feed_type');
        }

        if($element->get('urls')) {
            $info['urls'] = $element->get('urls');
        }

        if ($element->get('post_url')) {
            $info['post_url'] = $element->get('post_url');
        }

		return $info;
	}
	
	public function getSnId($snType, $snId) {
		return $snType . '#' . $snId;
	}
	
	protected function _prepareDescription(SNF_SNFeed_Element $element) {
		$descriptions = array();
		$src = array();

		foreach($element->get('images') as $src) {
			$descriptions[] = new SNF_SNFeed_Html_Image($src);
		}
		
		foreach($element->get('videos') as $video) {
            if (!is_array($src) || in_array($video['href'], $src))
                continue;

            $src[] = $video['href'];
			$descriptions[] = new SNF_SNFeed_Html_Video($video['source'], $video['href'], $video['text'], $video['description']);
		}
		
		foreach($element->get('links') as $link) {
            if (!is_array($src) || in_array($link['href'], $src))
                continue;

            $src[] = $link['href'];
			$descriptions[] = new SNF_SNFeed_Html_Link($link['href'], $link['text'], $link['description']);
		}
		
		$descriptions[] = $this->_linksToHtml($element->get('description'));
		
		$description = new SNF_SNFeed_Html_Description($descriptions);
		
		return $description->__toString();
	}
	
	protected function _titleFromDescription($description) {
		$description = strip_tags($description);
		
		return substr($description, 0, 80);
	}
	
	protected function _defaultTitle(SNF_SNFeed_Element $element) {
		return sprintf('%s post %s', ucfirst($element->get('sn_type')), date('Y-m-d H:i', $element->get('posted_at')));
	}
	
	protected function _urlKeyFromTitle($title) {
		$urlKey = strtolower(trim($title));
		$urlKey = preg_replace('#[\\W\\s]+#', '-', $urlKey);
		$urlKey = trim(preg_replace('#\\-{2,}#', '-', $urlKey), '-');
		
		return $urlKey;
	}
	
	protected function _linksToHtml($description) {
		return preg_replace_callback('#(\\s|^|>)(https?\\://(?:\\w+\\.){1,2}[\\w]+/?[^<\\s]+)#', function($matched) {
			$html = new SNF_SNFeed_Html_Link($matched[2], $matched[2], '');
			
			return $matched[1] . $html->__toString();
		}, (string)$description);
	}
}