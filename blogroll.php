<?php

// A blogroll for WordPress
// To be used with the link manager plugin https://wordpress.org/plugins/link-manager/

// More info: https://josh.blog/2024/05/blogrolls

namespace Blogroll;

function custom_rewrite_rule() {
    add_rewrite_rule('^.well-known/recommendations.opml/?$', 'index.php?well_known_recommendations=1', 'top');
}
add_action('init', 'Blogroll\custom_rewrite_rule');

function custom_query_vars($query_vars) {
    $query_vars[] = 'well_known_recommendations';
    return $query_vars;
}
add_filter('query_vars', 'Blogroll\custom_query_vars');

function custom_template_include($template) {
    if (get_query_var('well_known_recommendations')) {
        return ABSPATH . 'wp-links-opml.php';
    }
    return $template;
}
add_filter('template_include', 'Blogroll\custom_template_include');

function add_recommendations_opml_link() {
    printf('<link rel="blogroll" type="text/xml" href="%s" />', home_url('/.well-known/recommendations.opml'));
}
add_action('wp_head', 'Blogroll\add_recommendations_opml_link');

function custom_rss_feed_item() {
    printf("\t<source:blogroll>%s</source:blogroll>\n", home_url('/.well-known/recommendations.opml'));
}
add_action('rss2_head', 'Blogroll\custom_rss_feed_item');

function blogroll_namespace() {
    echo 'xmlns:source="http://source.scripting.com/"';
}
add_action('rss2_ns', 'Blogroll\blogroll_namespace');
