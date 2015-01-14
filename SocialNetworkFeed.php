<?php
/*
Plugin Name: Social Network Feed
Plugin URI:
Version: 1.0.0
Author: Serikov Valentine
Author URI:
*/

if (!defined('SocialNetworkFeed_IMAGE_RELOADS'))
    define('SocialNetworkFeed_IMAGE_RELOADS', 3);

add_action('wp', 'SocialNetworkFeed::onWp');
add_action('refresh_snfeeds', 'SocialNetworkFeed::onCron');
add_action('init', 'SocialNetworkFeed::onWpInit');

register_activation_hook(__FILE__, 'SocialNetworkFeed::onPluginActivate');
register_deactivation_hook(__FILE__, 'SocialNetworkFeed::onPluginDeactivate');

require_once 'Autoload.php';
SNF_SNFeed_Autoload::register();

class SocialNetworkFeed
{

    const POST_TYPE = 'SNF_snfeed';
    const TAXONOMY_NAME = 'SNF_snfeed_type';

    public static function onWp()
    {
        if (!wp_next_scheduled('refresh_snfeeds')) {
            wp_schedule_event(time(), 'hourly', 'refresh_snfeeds');
        }
    }

    public static function onWpInit()
    {
        $smwPage = new WP_Query(
            array(
                'post_type' => 'page',
                'meta_key' => '_wp_page_template',
                'meta_value' => 'template-social-media.php'
            )
        );

        $smwSlug = 'snfeed';
        if ($smwPage->have_posts())
            $smwSlug = $smwPage->post->post_name;

        self::initPostTypes($smwSlug);
        self::initTaxonomies($smwSlug);
    }

    public static function onPluginActivate()
    {
        self::initPostTypes();
        flush_rewrite_rules();
    }

    public static function onPluginDeactivate()
    {
        flush_rewrite_rules();
    }

    public static function onCron()
    {
        $feedsToConsume = array(
            SNF_SNFeed_Type::GOOGLEPLUS,
            SNF_SNFeed_Type::FACEBOOK,
            SNF_SNFeed_Type::TWITTER
        );

        array_walk($feedsToConsume, array(SNF_SNFeed_Consumer::instance(), 'consume'));
    }

    public static function initPostTypes($slug = 'snfeed')
    {
        register_post_type(self::POST_TYPE, array(
            'labels' => array(
                'name' => 'Social Feeds',
            ),
            'public' => true,
            'exclude_from_search' => true,
            'rewrite' => array(
                'slug' => $slug,
                'with_front' => true,
            ),
            'has_archive' => false,
            'hierarchical' => false,
            'supports' => array(
                'title',
                'editor',
                'comments',
                'thumbnail'
            )
        ));
    }

    public static function initTaxonomies($slug = 'snfeed')
    {
        register_taxonomy(
            self::TAXONOMY_NAME,
            self::POST_TYPE,
            array(
                'hierarchical' => false,
                'labels' => array(
                    'name' => 'Social Networks',
                    'singluar_name' => 'Social Network',
                ),
                'query_var' => true,
                'rewrite' => array(
                    'slug' => $slug . '/category',
                    'with_front' => true,
                ),
            )
        );
    }

}