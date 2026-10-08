<?php
/**
 * The Crisis Academy functions and definitions
 *
 * Child theme for Stories WordPress Theme.
 *
 * @package TheCrisisAcademy
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Enable error logging to wp-content/debug.log
ini_set( 'log_errors', '1' );
ini_set( 'error_log', WP_CONTENT_DIR . '/debug.log' );

/**
 * Child Theme Constants
 */
define( 'THECRISISACADEMY_VERSION', '1.0.0' );
define( 'THECRISISACADEMY_DIR', get_stylesheet_directory() );
define( 'THECRISISACADEMY_URI', get_stylesheet_directory_uri() );

/**
 * Setup child theme defaults and support.
 */
function thecrisisacademy_setup() {
	// Make child theme available for translation.
	load_child_theme_textdomain( 'thecrisisacademy', THECRISISACADEMY_DIR . '/languages' );
}
add_action( 'after_setup_theme', 'thecrisisacademy_setup' );

/**
 * Automatically create default landing pages and assign their templates on theme activation.
 */
function thecrisisacademy_create_default_pages() {
	$default_pages = array(
		'corporativo'  => array(
			'title'    => __( 'Corporativo', 'thecrisisacademy' ),
			'template' => 'templates/corporate.php',
		),
		'particulares' => array(
			'title'    => __( 'Particulares', 'thecrisisacademy' ),
			'template' => 'templates/individuals.php',
		),
	);

	foreach ( $default_pages as $slug => $data ) {
		// Check if page exists by slug (any status: publish, draft, trash, etc.)
		$existing_page = get_page_by_path( $slug, OBJECT, 'page' );

		if ( ! $existing_page ) {
			$page_query = new WP_Query(
				array(
					'name'           => $slug,
					'post_type'      => 'page',
					'post_status'    => 'any',
					'posts_per_page' => 1,
					'no_found_rows'  => true,
				)
			);
			if ( ! empty( $page_query->posts ) ) {
				$existing_page = $page_query->posts[0];
			}
		}

		if ( ! $existing_page ) {
			// Create the page if it does not exist
			$page_id = wp_insert_post(
				array(
					'post_title'     => $data['title'],
					'post_name'      => $slug,
					'post_status'    => 'publish',
					'post_type'      => 'page',
					'comment_status' => 'closed',
					'ping_status'    => 'closed',
				)
			);

			if ( $page_id && ! is_wp_error( $page_id ) ) {
				update_post_meta( $page_id, '_wp_page_template', $data['template'] );
			}
		} else {
			// If it exists, ensure the correct template is assigned
			$current_template = get_post_meta( $existing_page->ID, '_wp_page_template', true );
			if ( $current_template !== $data['template'] ) {
				update_post_meta( $existing_page->ID, '_wp_page_template', $data['template'] );
			}

			// If it was in trash, restore it
			if ( 'trash' === $existing_page->post_status ) {
				wp_untrash_post( $existing_page->ID );
			}
		}
	}
}
add_action( 'after_switch_theme', 'thecrisisacademy_create_default_pages' );

/**
 * Enqueue scripts and styles for the child theme.
 */
function thecrisisacademy_enqueue_scripts() {
	// Parent theme handles 'stories-style' using get_stylesheet_uri(), which resolves
	// to child style.css. Dequeue and replace it to avoid duplicate loading and ensure
	// proper cascade order.
	wp_dequeue_style( 'stories-style' );
	wp_deregister_style( 'stories-style' );

	// Enqueue parent theme base stylesheet.
	wp_enqueue_style(
		'stories-parent-style',
		get_template_directory_uri() . '/style.css',
		array(),
		file_exists( get_template_directory() . '/style.css' ) ? filemtime( get_template_directory() . '/style.css' ) : THECRISISACADEMY_VERSION
	);

	// Enqueue child theme stylesheet after parent core styles.
	wp_enqueue_style(
		'thecrisisacademy-style',
		get_stylesheet_uri(),
		array( 'stories-parent-style', 'stories-main' ),
		file_exists( THECRISISACADEMY_DIR . '/style.css' ) ? filemtime( THECRISISACADEMY_DIR . '/style.css' ) : THECRISISACADEMY_VERSION
	);

	// Optional child custom javascript.
	if ( file_exists( THECRISISACADEMY_DIR . '/assets/js/main.js' ) ) {
		wp_enqueue_script(
			'thecrisisacademy-main',
			THECRISISACADEMY_URI . '/assets/js/main.js',
			array( 'stories-main' ),
			filemtime( THECRISISACADEMY_DIR . '/assets/js/main.js' ),
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'thecrisisacademy_enqueue_scripts', 20 );

/**
 * Custom Post Types & Taxonomies
 */
require_once THECRISISACADEMY_DIR . '/inc/cpt-news.php';
require_once THECRISISACADEMY_DIR . '/inc/cpt-events.php';
require_once THECRISISACADEMY_DIR . '/inc/cpt-faq.php';

/**
 * Color Schemes
 */
require_once THECRISISACADEMY_DIR . '/inc/colors.php';

/**
 * AJAX News Filter
 */
require_once THECRISISACADEMY_DIR . '/inc/ajax-news.php';

/**
 * Page Custom Field Groups & Repeaters (Native ACF-alternative)
 * Centralizes all landing page fields (Corporate, Individuals, Team, etc.)
 */
require_once THECRISISACADEMY_DIR . '/inc/page-fields.php';

/**
 * Corporate SEO & Schema.org JSON-LD Structured Data
 */
require_once THECRISISACADEMY_DIR . '/inc/corporate-seo.php';

/**
 * Individuals SEO & Schema.org JSON-LD Structured Data
 */
require_once THECRISISACADEMY_DIR . '/inc/individuals-seo.php';

/**
 * Get corporate homepage assets
 */
function crisisacademy_get_assets()
{
    $assets_path = '/assets';

    return [
        'css' => [
            'corporate-styles' => $assets_path . '/css/corporate.css',
			'individuals-styles' => $assets_path . '/css/individuals.css',
			'simulator-styles' => $assets_path . '/css/simulator.css',
        ],
        'js' => [
            'gsap' => $assets_path . '/js/vendor/gsap.min.js',
            'gsap-scrolltrigger' => $assets_path . '/js/vendor/ScrollTrigger.min.js',
            'corporate-scripts' => $assets_path . '/js/corporate.js',
			'individuals-scripts' => $assets_path . '/js/individuals.js',
			'individuals-hero' => $assets_path . '/js/individuals-hero.js',
            'corporate-hero'    => $assets_path . '/js/corporate-hero.js',
			'simulator-scripts' => $assets_path . '/js/simulator.js'
        ]
    ];
}


/**
 * Helper function to enqueue a child theme stylesheet with versioning.
 */
function crisisacademy_enqueue_style($handle, $path, $media = 'all')
{
    $uri = get_stylesheet_directory_uri();
    $dir = get_stylesheet_directory();
    $src = (strpos($path, 'http') === 0) ? $path : $uri . $path;
    $ver = (strpos($path, 'http') === 0) ? '1.0' : (file_exists($dir . $path) ? filemtime($dir . $path) : time());

    wp_enqueue_style($handle, $src, [], $ver, $media);
}

/**
 * Helper function to enqueue a child theme script with versioning and dependency support.
 */
function crisisacademy_enqueue_script($handle, $path, $deps = [])
{
    $uri = get_stylesheet_directory_uri();
    $dir = get_stylesheet_directory();
    $src = (strpos($path, 'http') === 0) ? $path : $uri . $path;
    $ver = (strpos($path, 'http') === 0) ? '1.0' : (file_exists($dir . $path) ? filemtime($dir . $path) : time());

    wp_enqueue_script($handle, $src, $deps, $ver, true);
}

/**
 * Corporate Crisis Academy Template Assets
 */
function crisisacademy_templates() {
    if (is_page_template('templates/corporate.php')) {
		$a = crisisacademy_get_assets();

        function crisisacademy_unload_parts_header() {
            wp_dequeue_style( 'page' );
        }
        add_action( 'wp_enqueue_scripts', 'crisisacademy_unload_parts_header', 100 );

		// Enqueue GSAP & ScrollTrigger from vendor
        crisisacademy_enqueue_script('gsap', $a['js']['gsap']);
        crisisacademy_enqueue_script('gsap-scrolltrigger', $a['js']['gsap-scrolltrigger'], ['gsap']);

        crisisacademy_enqueue_style('corporate-styles', $a['css']['corporate-styles']);
        crisisacademy_enqueue_script('corporate-scripts', $a['js']['corporate-scripts'], ['gsap', 'gsap-scrolltrigger']);
        crisisacademy_enqueue_script('corporate-hero', $a['js']['corporate-hero'], ['gsap', 'corporate-scripts']);

		// Localize AJAX script parameters for corporate page
        wp_localize_script('corporate-scripts', 'thecrisisacademy_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('thecrisisacademy_news_nonce'),
        ));
	}

	if (is_page_template('templates/individuals.php')) {
		$a = crisisacademy_get_assets();

        function crisisacademy_unload_parts_header() {
            wp_dequeue_style( 'page' );
        }
        add_action( 'wp_enqueue_scripts', 'crisisacademy_unload_parts_header', 100 );

		// Enqueue GSAP & ScrollTrigger from vendor
        crisisacademy_enqueue_script('gsap', $a['js']['gsap']);
        crisisacademy_enqueue_script('gsap-scrolltrigger', $a['js']['gsap-scrolltrigger'], ['gsap']);

        // Enqueue Three.js and Stories WebGL Loop Gallery Slideshow
        wp_enqueue_script('three', 'https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js', [], 'r128', true);
        wp_enqueue_script('stories-loop-gallery', get_template_directory_uri() . '/assets/js/loop-gallery.js', ['three', 'gsap'], function_exists('stories_get_asset_version') ? stories_get_asset_version('/assets/js/loop-gallery.js') : '1.0.0', true);

        crisisacademy_enqueue_style('individuals-styles', $a['css']['individuals-styles']);
        crisisacademy_enqueue_script('individuals-scripts', $a['js']['individuals-scripts'], ['gsap', 'gsap-scrolltrigger', 'three', 'stories-loop-gallery']);
        crisisacademy_enqueue_script('individuals-hero', $a['js']['individuals-hero'], ['gsap', 'individuals-scripts']);

		// Localize AJAX script parameters for individuals page
        wp_localize_script('individuals-scripts', 'thecrisisacademy_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('thecrisisacademy_news_nonce'),
        ));
	}

	if ( is_page_template( 'templates/page-simulator.php' ) || is_page( 'simulador-de-crisis' ) ) {
		thecrisisacademy_customize_page_simulator_design();
	}
}
add_action( 'wp_enqueue_scripts', 'crisisacademy_templates' );

/**
 * Customize the design of page-template-page-simulator to inherit the Corporate Lightbox aesthetic.
 */
function thecrisisacademy_customize_page_simulator_design() {
	$a = crisisacademy_get_assets();

	// Enqueue corporate styles for design tokens and lightbox CSS
	crisisacademy_enqueue_style( 'simulator-styles', $a['css']['simulator-styles'] );

	// Enqueue GSAP for smooth interactive transitions
	crisisacademy_enqueue_script( 'gsap', $a['js']['gsap'] );
	crisisacademy_enqueue_script('simulator-scripts', $a['js']['simulator-scripts']);

	// Dequeue generic page stylesheet to avoid layout conflicts
	wp_dequeue_style( 'page' );
}

/**
 * Load child theme template for Simulador de Crisis full page.
 *
 * @param string $template Path to template file.
 * @return string
 */
function thecrisisacademy_simulator_template_include( $template ) {
	if ( is_page( 'simulador-de-crisis' ) || get_page_template_slug() === 'templates/page-simulator.php' ) {
		$theme_template = locate_template( array( 'templates/page-simulator.php', 'page-simulator.php' ) );
		if ( $theme_template ) {
			return $theme_template;
		}
	}
	return $template;
}
add_filter( 'template_include', 'thecrisisacademy_simulator_template_include', 20 );

/**
 * Templates that manage all content via structured custom metaboxes / ACF fields
 * and do not use the WordPress default content editor.
 *
 * @return array List of template file paths.
 */
function thecrisisacademy_get_no_editor_templates() {
	return array(
		'templates/corporate.php',
		'templates/individuals.php',
		'templates/page-simulator.php',
	);
}


/**
 * Disable block editor (Gutenberg) for Corporate and Particulares templates because
 * all content is managed via specialized structured metaboxes and ACF field groups.
 *
 * @param bool    $use_block_editor Whether to use the block editor.
 * @param WP_Post $post             The post being edited.
 * @return bool
 */
function thecrisisacademy_disable_block_editor_for_landing_templates( $use_block_editor, $post ) {
	if ( $post && 'page' === $post->post_type ) {
		$template = get_post_meta( $post->ID, '_wp_page_template', true );
		$is_front = ( (int) get_option( 'page_on_front' ) === $post->ID );
		if ( in_array( $template, thecrisisacademy_get_no_editor_templates(), true ) || $is_front ) {
			return false;
		}
	}
	return $use_block_editor;
}
add_filter( 'use_block_editor_for_post', 'thecrisisacademy_disable_block_editor_for_landing_templates', 10, 2 );

/**
 * Remove default content editor for Corporate and Particulares pages since their
 * layouts are entirely composed of modular custom metabox sections and ACF groups.
 */
function thecrisisacademy_remove_editor_support_for_landing_templates() {
	if ( isset( $_GET['post'] ) || isset( $_POST['post_ID'] ) ) {
		$post_id  = (int) ( $_GET['post'] ?? $_POST['post_ID'] );
		$template = get_post_meta( $post_id, '_wp_page_template', true );
		$is_front = ( (int) get_option( 'page_on_front' ) === $post_id );
		if ( in_array( $template, thecrisisacademy_get_no_editor_templates(), true ) || $is_front ) {
			remove_post_type_support( 'page', 'editor' );
		}
	}
}
add_action( 'admin_init', 'thecrisisacademy_remove_editor_support_for_landing_templates' );