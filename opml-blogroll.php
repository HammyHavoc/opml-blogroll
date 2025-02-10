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

// Prevent direct file access.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register a rewrite rule without an optional trailing slash.
 */
function custom_rewrite_rule() {
    add_rewrite_rule( '^.well-known/recommendations\.opml$', 'index.php?well_known_recommendations=1', 'top' );
}
add_action( 'init', __NAMESPACE__ . '\custom_rewrite_rule' );

/**
 * Add a custom query variable for the OPML feed.
 */
function custom_query_vars( $query_vars ) {
    $query_vars[] = 'well_known_recommendations';
    return $query_vars;
}
add_filter( 'query_vars', __NAMESPACE__ . '\custom_query_vars' );

/**
 * Use a custom template for the OPML output.
 */
function custom_template_include( $template ) {
    if ( get_query_var( 'well_known_recommendations' ) ) {
        return ABSPATH . 'wp-links-opml.php';
    }
    return $template;
}
add_filter( 'template_include', __NAMESPACE__ . '\custom_template_include' );

/**
 * Output a link tag in the HTML head for the OPML feed.
 */
function add_recommendations_opml_link() {
    printf(
        '<link rel="blogroll" type="text/xml" href="%s" />',
        esc_url( home_url( '/.well-known/recommendations.opml' ) )
    );
}
add_action( 'wp_head', __NAMESPACE__ . '\add_recommendations_opml_link' );

/**
 * Insert a link into the RSS feed.
 */
function custom_rss_feed_item() {
    printf(
        "\t<source:blogroll>%s</source:blogroll>\n",
        esc_url( home_url( '/.well-known/recommendations.opml' ) )
    );
}
add_action( 'rss2_head', __NAMESPACE__ . '\custom_rss_feed_item' );

/**
 * Add the blogroll namespace to the RSS feed.
 */
function blogroll_namespace() {
    echo ' xmlns:source="http://source.scripting.com/"';
}
add_action( 'rss2_ns', __NAMESPACE__ . '\blogroll_namespace' );

/**
 * Disable WordPress's canonical redirect for URLs containing the OPML feed.
 * This prevents WordPress from appending a trailing slash.
 */
function disable_opml_canonical_redirect( $redirect_url ) {
    // Unslash and sanitize the REQUEST_URI.
    $request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
    if ( strpos( $request_uri, '/.well-known/recommendations.opml' ) !== false ) {
        return false;
    }
    return $redirect_url;
}
add_filter( 'redirect_canonical', __NAMESPACE__ . '\disable_opml_canonical_redirect' );

/**
 * If the request URL has a trailing slash (e.g. /.well-known/recommendations.opml/),
 * explicitly redirect to the non-trailing slash version.
 */
function remove_trailing_slash_from_opml() {
    $request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
    $parts       = wp_parse_url( $request_uri );
    $path        = isset( $parts['path'] ) ? $parts['path'] : '';

    // Check if the path ends with a slash and, once removed, equals '/.well-known/recommendations.opml'.
    if ( '/' === substr( $path, -1 ) && untrailingslashit( $path ) === '/.well-known/recommendations.opml' ) {
        // Build the canonical URL. Preserve query vars if present.
        $redirect_url = home_url( '/.well-known/recommendations.opml' );
        if ( ! empty( $parts['query'] ) ) {
            $redirect_url = add_query_arg( null, null, $redirect_url );
        }
        wp_redirect( $redirect_url, 301 );
        exit;
    }
}
add_action( 'template_redirect', __NAMESPACE__ . '\remove_trailing_slash_from_opml', 0 );
