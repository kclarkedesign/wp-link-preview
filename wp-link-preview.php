<?php
/*
Plugin Name: Link Preview
Plugin URI: https://example.com/link-preview
Description: Generates a link preview for a given URL.
Version: 1.0
Author: Your Name
Author URI: https://example.com
*/


// function enqueue_plugin_scripts()
// {
//     wp_enqueue_script('wp-link-preview', plugins_url('wp-link-preview/script.js'));
// }
// add_action('wp_enqueue_scripts', 'enqueue_plugin_scripts');

function wppreviewlink_plugin_styles()
{
    wp_register_style('wplink-preview-styles', plugins_url('css/style.css', __FILE__));
    wp_enqueue_style('wplink-preview-styles');
}

add_action('wp_enqueue_scripts', 'wppreviewlink_plugin_styles');

function link_preview_shortcode($atts)
{
    $atts = shortcode_atts(
        array(
            'url' => '',
            'cache_time' => 3600 // Default cache time in seconds, 1 hour
        ),
        $atts,
        'link_preview'
    );


    $url = $atts['url'];
    $cache_time = intval($atts['cache_time']);
    $cache_key = 'link_preview_' . md5($url);

    // Attempt to get the cached data
    $cached_data = get_transient($cache_key);
    if ($cached_data !== false) {
        return $cached_data;
    }

    // Fetch the HTML of the page as a string
    $html = file_get_contents($url);

    if (is_wp_error($html)) {
        return "Failed to retrieve link preview";
    }

    // Extract the title
    $titleRegex = '/<title.*?>(.*?)<\/title>/';
    preg_match($titleRegex, $html, $title);

    // Extract the description
    $descriptionRegex = '/<meta.*?name="description".*?content="(.*?)"/';
    preg_match($descriptionRegex, $html, $description);

    // Extract the image
    $imageRegex = '/<meta.*?property="og:image".*?content="(.*?)"/';
    preg_match($imageRegex, $html, $image);

    // Build the link preview HTML
    $output = '<div class="link-preview">';
    $output .= '<a class="link-preview__title" href="' . $url . '" target="_blank">' . $title[1] . '</a>';
    $output .= '<p class="link-preview__desc">' . $description[1] . '</p>';
    $output .= '<a href="' . $url . ' target="_blank"><img class="link-preview__img" src="' . $image[1] . '" alt="' . $title[1] . '"></a>';
    $output .= '</div>';

    // Cache the data for a certain amount of time
    set_transient($cache_key, $output, $cache_time);

    return $output;
}
add_shortcode('link_preview', 'link_preview_shortcode');