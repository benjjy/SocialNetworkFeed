<?php
class SNF_SNFeed_Model_Db
{

    protected $_tries = array();
    private $_thumbnails = array();

    public static function instance()
    {
        static $instance = null;

        if (is_null($instance)) {
            $instance = new self;
        }

        return $instance;
    }

    public function savePosts(array $posts)
    {
        $snIds = array_map(function($post)
        {
            return $post['sn_id'];
        }, $posts);

        $snIds = $this->_filterSnIds($snIds);

        if ($snIds) {
            $posts = array_filter($posts, function($post) use($snIds)
            {
                return !in_array($post['sn_id'], $snIds);
            });

            if (!$posts) {
                return;
            }
        }

        foreach ($posts as $post) {
            if (empty($post['thumbnail']) && strpos($post['sn_type'], SNF_SNFeed_Type::TWITTER) !== 0) {
                continue;
            }

            $postId = wp_insert_post($post['post']);

            update_post_meta($postId, 'SNF_sn_id', $post['sn_id']);

            if ($post['sn_group']) {
                update_post_meta($postId, 'SNF_sn_group', $post['sn_group']);
            }

            if (isset($post['urls'])) {
                update_post_meta($postId, 'urls', $post['urls']);
            }

            if (isset($post['post_url'])) {
                update_post_meta($postId, 'post_url', $post['post_url']);
            }

            if (isset($post['feed_type'])) {
                update_post_meta($postId, 'feed_type', $post['feed_type']);
            }

            update_post_meta($postId, 'unrelated', false);

            wp_set_object_terms($postId, $post['sn_type'], 'SNF_snfeed_type');

            if (!empty($post['thumbnail'])) {
                $thType = isset($post['th_type']) ? $post['th_type'] : false;
                $this->_savePostThumbnail($postId, $post['thumbnail'], $post['sn_type'], $thType);
            }
        }
    }

    public function removeUnrelatedPosts($snType, $snIds)
    {
        global $wpdb;

        // sync only this post_types
        $ids = $wpdb->get_col('
			SELECT
				p.ID
			FROM
				' . $wpdb->posts . ' p,
				' . _get_meta_table('post') . ' pm
			WHERE
				p.ID = pm.post_id
				AND pm.meta_key = "SNF_sn_id"
				AND pm.meta_value LIKE "' . $snType . '#%"
				AND pm.meta_value NOT IN("' . implode('","', $snIds) . '")
				AND p.post_type = "SNF_snfeed"
		');

        foreach ($ids as $id) {
            update_post_meta($id, 'unrelated', true);
        }

    }

    protected function _filterSnIds(array $snIds)
    {
        global $wpdb;

        return $wpdb->get_col('
			SELECT
				pm.meta_value
			FROM
				' . _get_meta_table('post') . ' pm
			WHERE
				pm.meta_key = "SNF_sn_id"
				AND pm.meta_value IN("' . implode('","', $snIds) . '")
		');
    }

    protected function _savePostThumbnail($postId, $thumbnailUrl, $type = 'social_feed', $imageMimeType = false)
    {
        try {
            if (!isset($this->_tries[$postId]))
                $this->_tries[$postId] = 0;

            $this->_tries[$postId]++;
            $uploadDir = wp_upload_dir();
            $imageContent = $this->_getContents($thumbnailUrl);

            if ($imageContent !== false) {
                $filename = $type . $postId . "_" . time() . "." . $imageContent['ext'];
                $path = (wp_mkdir_p($uploadDir['path'])) ? ($uploadDir['path'] . '/' . $filename) : ($path = $uploadDir['basedir'] . '/' . $filename);
                $saveResult = @file_put_contents($path, $imageContent['body']);
            } else {
                update_post_meta($postId, 'external_thumbnail_url', $thumbnailUrl);
                update_post_meta($postId, 'try_fetch_image', $this->_tries[$postId]);
                return $this->_draftedPost($postId);
            }

            $filetype = wp_check_filetype($filename, null);

            $attachment = array(
                'post_mime_type' => $filetype['type'],
                'post_title' => sanitize_file_name($filename),
                'post_content' => '',
                'post_status' => 'inherit'
            );

            $thumbnailId = wp_insert_attachment($attachment, $path, $postId);

            require_once ABSPATH . 'wp-admin/includes/image.php';

            $thumbnailData = wp_generate_attachment_metadata($thumbnailId, $path);
            wp_update_attachment_metadata($thumbnailId, $thumbnailData);
            set_post_thumbnail($postId, $thumbnailId);

            update_post_meta($postId, 'external_thumbnail_url', $thumbnailUrl);
            update_post_meta($postId, 'try_fetch_image', $this->_tries[$postId]);

        } catch (Exception $e) {
            if (file_exists($path))
                unlink($path);

            if (defined('SocialNetworkFeed_IMAGE_RELOADS') && SocialNetworkFeed_IMAGE_RELOADS > $this->_tries[$postId]) {
                $this->_savePostThumbnail($postId, $thumbnailUrl, $type, $imageMimeType);
            } else {
                update_post_meta($postId, 'external_thumbnail_url', $thumbnailUrl);
                update_post_meta($postId, 'try_fetch_image', $this->_tries[$postId]);
                return $this->_draftedPost($postId);
            }
        }

    }

    protected function _getContents($url)
    {
        $obj = wp_remote_get($url, array('sslverify' => false, 'timeout' => 60));
        if (is_wp_error($obj) || $obj['response']['code'] != 200)
            return false;

        $ext = self::mimeTypeToExt($obj['headers']['content-type']);
        if (!$ext)
            return false;

        $obj['ext'] = $ext;
        return $obj;
    }

    protected function _draftedPost($postID, $status = 'draft')
    {
        wp_update_post(array('ID' => $postID, 'post_status' => $status));
    }

    public static function mimeTypeToExt($mime)
    {
        $mimes = array(
            'image/jpeg' => 'jpg',
            'image/gif' => 'gif',
            'image/png' => 'png',
            'image/bmp' => 'bmp',
            'image/tiff' => 'tif',
            'image/x-icon' => 'ico'
        );

        if (isset($mimes[$mime]))
            return $mimes[$mime];

        return false;
    }
}