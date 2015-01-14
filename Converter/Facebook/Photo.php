<?php
class SNF_SNFeed_Converter_Facebook_Photo extends SNF_SNFeed_Converter_Facebook_Abstract {

    const FETCH_PICTURE_URL = "https://graph.facebook.com/";
	public function toElement($post) {
		$element = $this->_initElement($post);

		$description = empty($post['caption']) ? (empty($post['message']) ? '' : $post['message']) : $post['caption'];
		
		$element->set('description', nl2br($description, true));

        if (isset($post['picture'])) {
            $element->set('thumbnail', str_replace('_s.', '_n.', $post['picture']));
            $postImages = $this->_fetch(self::FETCH_PICTURE_URL . $post['object_id']);
            if (is_object($postImages) && isset($postImages->images[0])) {
                $element->set('thumbnail', $postImages->images[0]->source);
            }
        }

        $element->set('feed_type', 'photo');

		return $element;
	}

    protected function _fetch($url) {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_FAILONERROR    => true,
            CURLOPT_HEADER         => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_URL            => $url . '/?fields=images',
            CURLOPT_SSL_VERIFYPEER => false
        ));

        $result = array();

        $result['response'] = curl_exec($curl);

        if(curl_error($curl)) {
            throw new Exception('cURL error: ' . curl_error($curl) . '; URL: ' . $url . '; Response: ' . $result['response']);
        }

        $result['code'] = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        if ($result['code'] != 200) {
            return false;
        }

        return json_decode($result['response']);
    }
}