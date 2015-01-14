<?php
abstract class SNF_SNFeed_Reader_Abstract
{

    protected $_allIds = array();

    protected $_seekFrom = null;
    protected $_nextPage = true;

    public function getAllIds()
    {
        $this->_prepareAllIds();
        return $this->_allIds;
    }

    public function nextPage($flag = null)
    {
        if (is_null($flag)) {
            return $this->_nextPage;
        }

        $this->_nextPage = !!$flag;

        return $this;
    }

    protected function _getSeekFrom()
    {
        if (is_null($this->_seekFrom)) {
            $lastPost = new WP_Query(
                array(
                    'post_type' => SNF_SNFeed::POST_TYPE,
                    'posts_per_page' => 1,
                    'order' => 'DESC',
                    'orderby' => 'date',
                    'tax_query' => array(
                        array(
                            'taxonomy' => SNF_SNFeed::TAXONOMY_NAME,
                            'field' => 'slug',
                            'terms' => $this->getNamespace()
                        )
                    )
                )
            );

            if ($lastPost->have_posts()) {
                $this->_seekFrom = get_the_time('U', $lastPost->post);
            } else {
                $this->_seekFrom = get_option('SNF_snfeed.reader.' . $this->getNamespace() . '.seek_from', 0);
            }
        }

        return $this->_seekFrom;
    }

    protected function _saveSeekFrom($seekFrom)
    {
        update_option('SNF_snfeed.reader.' . $this->getNamespace() . '.seek_from', $seekFrom);
    }

    abstract protected function _prepareAllIds();

    abstract public function readPage();

    abstract public function getNamespace();
}