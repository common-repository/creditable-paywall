<?php

namespace creditablepaywall\classes;
use Creditable\CreditablePayWall;

class CreditablepaywallController {

	public static function getPaywallCheck() {
        global $post;
        if($post->post_type == 'revision'){
            return null;
        }

        $post_categories = wp_get_post_categories($post->ID,['fields' =>'all']);
        $post_tags = wp_get_post_tags($post->ID,['fields' =>'all']);
        $post_category = $post_categories[0] ?? null;
        $tags = '';

        if($post_tags){
            foreach($post_tags as $tag){
                $tags .= $tag->name.', '  ;
            }
        }
        if($post_category){
            $creditable_topic_id = $post_category->term_id;
            $creditable_topic_name = $post_category->name;
            $category_url = get_term_link($post_category->term_id);
            $category_description = $post_category->description;
        } else {
            $creditable_topic_id = '0';
            $creditable_topic_name = '';
        }

        $author = get_userdata($post->post_author);

        $creditable_article_id = $post->ID;
        $creditable_article_title = $post->post_title; // Alphanumeric (required)
        $creditable_article_url = get_permalink($post->ID); // Alphanumeric (required)
        $creditable_article_lang = get_locale(); // ISO (required)
        $creditable_article_authors  = $author->display_name ?? '';   // Alphanumeric (comma separated) String (optional)
        $creditable_article_desc = $post->post_excerpt; //optional
        $creditable_article_tags = $tags; // Alphanumeric (optional) comma delimited list or json (optional keywords, used to find recommended articles for users)
        $creditable_article_img = self::getImageURL($post->ID); // Alphanumeric (optional) URL for article image
        $creditable_topic_url = $category_url ?? ''; // Alphanumeric (optional)
        $creditable_topic_desc = $category_description ?? ''; // Alphanumeric (optional)

// GET LOCAL CREDITABLE JWT COOKIE
        $creditable_cookie = sanitize_text_field($_COOKIE['cjwt']) ?? "";

// Example usage
        $data = [
            'jwt' => $creditable_cookie,
            'article_id' => $creditable_article_id,
            'article_name' => $creditable_article_title,
            'topic_id' => $creditable_topic_id,
            'topic_name' => $creditable_topic_name,
            'topic_desc' => $creditable_topic_desc,
            'topic_url' => $creditable_topic_url,
            'article_url' => $creditable_article_url,
            'article_lang' => $creditable_article_lang,
            'article_authors' => $creditable_article_authors,
            'article_desc' => $creditable_article_desc,
            'article_tags' => $creditable_article_tags,
            'article_img' => $creditable_article_img
        ];

        $creditable = self::getCreditable();

        try {
            $result = $creditable->check($data);
            return $result;
        } catch (Exception $e) {
            echo 'Error: ' . esc_html($e->getMessage());
        }

	}

	public static function getImageURL($post_id){
        if (has_post_thumbnail($post_id)) {
            $featured_image_url = get_the_post_thumbnail_url($post_id, 'full'); // Change 'full' to your desired image size
           return $featured_image_url;
        } else {
            // Get the URL of the first image in the post content
            $content = get_the_content(null, false, $post_id);
            $first_image_url = '';
            preg_match('/<img.+?src=[\'"]([^\'"]+)[\'"].*?>/i', $content, $matches);
            if (isset($matches[1])) {
                return $matches[1];
            } else {
                // No featured image and no images in content
                return '';
            }
        }
    }

    public static function getCreditable() {
        $settings = \creditablepaywall\Creditablepaywall::get_settings();
        $apiKey = $settings['api_key'] ?? '';
        $options = [
            'environment' => 'prod' // prod or dev
        ];
        return new CreditablePayWall($apiKey, $options);
    }

}