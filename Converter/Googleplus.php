<?php
class SNF_SNFeed_Converter_Googleplus implements SNF_SNFeed_Converter_Interface
{

    public function toElement($post)
    {
        $element = new SNF_SNFeed_Element(array(
            'sn_type' => SNF_SNFeed_Type::GOOGLEPLUS,
            'sn_id' => $post['id'],
            'title' => $post['title'],
            'description' => "",
            'posted_at' => strtotime($post['published']),
            'post_url' => @$post['url']
        ));


        if (!empty($post['object']['attachments'])) {
            foreach ($post['object']['attachments'] as $attachment) {
                $content = !empty($post['object']['content']) ? $post['object']['content'] : "";

                if (empty($post['title']) && isset($attachment["displayName"]) && !empty($attachment["displayName"])) {
                    $post['title'] = $attachment["displayName"];
                }

                if (!empty($attachment['embed'])) {
                    $element->addVideo($attachment['embed'], $attachment['url'], $post['title'], $content);
                    $element->set('feed_type', 'video');
                }

                $thumbnail = $element->get('thumbnail');
                if (empty($thumbnail) && !empty($attachment['fullImage'])) {
                    $element->set('thumbnail', $attachment['fullImage']['url']);
                    if (isset($attachment['fullImage']['type'])) {
                        $element->set('th_type', $attachment['fullImage']['type']);
                    }
                } else if (empty($thumbnail) && !empty($attachment['image'])) {
                    $element->set('thumbnail', $attachment['image']['url']);
                    if (isset($attachment['image']['type'])) {
                        $element->set('th_type', $attachment['image']['type']);
                    }
                } elseif (!empty($attachment['image'])) {
                    $element->addImage($attachment['image']['url']);
                }

                $element->addLink($attachment['url'], $post['title']);
                $element->addUrls($attachment['url']);
            }
        }

        return $element;
    }
}