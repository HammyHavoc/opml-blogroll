<?php
/**
 * Plugin Name: OPML Blogroll
 * Plugin URI: https://github.com/HammyHavoc/opml-blogroll
 * Description: Adds a blogroll OPML feed to your WordPress site.
 * Version: 1.0
 * Author: Hammy Havoc
 * Author URI: https://hammyhavoc.com
 * License: GPL2
 */
namespace Blogroll;

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Use a strict rewrite rule (without an optional trailing slash)
function custom_rewrite_rule() {
    add_rewrite_rule('^.well-known/recommendations\.opml$', 'index.php?well_known_recommendations=1', 'top');
}
add_action('init', __NAMESPACE__ . '\custom_rewrite_rule');

// Add custom query variable
function custom_query_vars($query_vars) {
    $query_vars[] = 'well_known_recommendations';
    return $query_vars;
}
add_filter('query_vars', __NAMESPACE__ . '\custom_query_vars');

// Use custom template for the OPML output
function custom_template_include($template) {
    if ( get_query_var('well_known_recommendations') ) {
        return ABSPATH . 'wp-links-opml.php';
    }
    return $template;
}
add_filter('template_include', __NAMESPACE__ . '\custom_template_include');

// Add the OPML link to the page head
function add_recommendations_opml_link() {
    printf(
        '<link rel="blogroll" type="text/xml" href="%s" />',
        esc_url( home_url('/.well-known/recommendations.opml') )
    );
}
add_action('wp_head', __NAMESPACE__ . '\add_recommendations_opml_link');

// Add the OPML link to the RSS feed
function custom_rss_feed_item() {
    printf(
        "\t<source:blogroll>%s</source:blogroll>\n",
        esc_url( home_url('/.well-known/recommendations.opml') )
    );
}
add_action('rss2_head', __NAMESPACE__ . '\custom_rss_feed_item');

// Add the blogroll namespace to the RSS feed
function blogroll_namespace() {
    echo ' xmlns:source="http://source.scripting.com/"';
}
add_action('rss2_ns', __NAMESPACE__ . '\blogroll_namespace');

// Disable canonical redirect for the OPML URL to avoid trailing slash
function disable_opml_canonical_redirect( $redirect_url ) {
    if ( isset( $_SERVER['REQUEST_URI'] ) && strpos( $_SERVER['REQUEST_URI'], '/.well-known/recommendations.opml' ) !== false ) {
        return false;
    }
    return $redirect_url;
}
add_filter('redirect_canonical', __NAMESPACE__ . '\disable_opml_canonical_redirect');
