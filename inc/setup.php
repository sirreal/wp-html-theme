<?php
/**
 * Theme setup, asset enqueueing, and dequeues.
 *
 * @package HTML
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Theme setup. Registers theme supports and nav menu locations.
 */
function html_theme_setup() {
    load_theme_textdomain( 'html', get_template_directory() . '/languages' );

    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'custom-logo' );
    add_theme_support(
        'html5',
        array(
            'comment-list',
            'comment-form',
            'search-form',
            'gallery',
            'caption',
            'style',
            'script',
            'navigation-widgets',
        )
    );

    register_nav_menus(
        array(
            'primary' => __( 'Primary', 'html' ),
            'footer'  => __( 'Footer', 'html' ),
        )
    );
}
add_action( 'after_setup_theme', 'html_theme_setup' );

/**
 * Set $content_width if not already set.
 */
function html_content_width() {
    $GLOBALS['content_width'] = apply_filters( 'html_content_width', 800 );
}
add_action( 'after_setup_theme', 'html_content_width', 0 );

/**
 * Enqueue the theme stylesheet.
 */
function html_enqueue_assets() {
    wp_enqueue_style(
        'html-style',
        get_stylesheet_uri(),
        array(),
        wp_get_theme()->get( 'Version' )
    );
}
add_action( 'wp_enqueue_scripts', 'html_enqueue_assets' );

/**
 * Dequeue WP-injected styles we do not need on the front end.
 */
function html_dequeue_default_styles() {
    if ( is_admin() ) {
        return;
    }
    wp_dequeue_style( 'wp-block-library' );
    wp_dequeue_style( 'wp-block-library-theme' );
    wp_dequeue_style( 'global-styles' );
    wp_dequeue_style( 'classic-theme-styles' );
}
add_action( 'wp_enqueue_scripts', 'html_dequeue_default_styles', 100 );

/**
 * Remove WP emoji output.
 */
function html_disable_emojis() {
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
    remove_action( 'admin_print_styles', 'print_emoji_styles' );
    remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
    remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
    remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
    add_filter( 'tiny_mce_plugins', 'html_disable_emojis_tinymce' );
}
add_action( 'init', 'html_disable_emojis' );

/**
 * Filter out the wpemoji TinyMCE plugin.
 *
 * @param array $plugins TinyMCE plugins.
 * @return array
 */
function html_disable_emojis_tinymce( $plugins ) {
    if ( is_array( $plugins ) ) {
        return array_diff( $plugins, array( 'wpemoji' ) );
    }
    return array();
}
