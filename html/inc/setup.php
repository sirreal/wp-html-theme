<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function html_theme_setup() {
	load_theme_textdomain( 'html', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'custom-logo' );
	add_theme_support( 'align-wide' );
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

	add_editor_style( 'style.css' );

	register_nav_menus(
		array(
			'primary' => __( 'Primary', 'html' ),
			'footer'  => __( 'Footer', 'html' ),
		)
	);
}
add_action( 'after_setup_theme', 'html_theme_setup' );

function html_content_width() {
	$GLOBALS['content_width'] = 800;
}
add_action( 'after_setup_theme', 'html_content_width', 0 );

function html_enqueue_assets() {
	wp_enqueue_style(
		'html-style',
		get_stylesheet_uri(),
		array(),
		wp_get_theme()->get( 'Version' )
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'html_enqueue_assets' );

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

function html_disable_emojis_tinymce( $plugins ) {
	return array_diff( $plugins, array( 'wpemoji' ) );
}
