<?php
/**
 * AJAX Handler for filtering news by category.
 *
 * @package TheCrisisAcademy
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Filter news posts via AJAX by category slug.
 */
function thecrisisacademy_ajax_filter_news() {
	// Verify nonce for security if provided
	$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
	if ( ! empty( $nonce ) && ! wp_verify_nonce( $nonce, 'thecrisisacademy_news_nonce' ) ) {
		wp_send_json_error( array( 'message' => __( 'Nonce no válido.', 'thecrisisacademy' ) ) );
	}

	$category = isset( $_POST['category'] ) ? sanitize_text_field( wp_unslash( $_POST['category'] ) ) : 'all';

	$posts_per_page = 8;
	$orderby        = 'date';
	$order          = 'DESC';

	if ( function_exists( 'thecrisisacademy_get_news_data' ) ) {
		$corporate_page_id = (int) get_option( 'page_on_front' );
		if ( ! $corporate_page_id ) {
			$corp_pages = get_pages( array(
				'meta_key'   => '_wp_page_template',
				'meta_value' => 'templates/corporate.php',
				'number'     => 1,
			) );
			if ( ! empty( $corp_pages ) ) {
				$corporate_page_id = $corp_pages[0]->ID;
			}
		}
		$news_settings = thecrisisacademy_get_news_data( $corporate_page_id );
		if ( ! empty( $news_settings['posts_per_page'] ) ) {
			$posts_per_page = (int) $news_settings['posts_per_page'];
		}
		if ( ! empty( $news_settings['orderby'] ) ) {
			$orderby = $news_settings['orderby'];
		}
		if ( ! empty( $news_settings['order'] ) ) {
			$order = $news_settings['order'];
		}
	}

	$args = array(
		'post_type'      => 'news',
		'posts_per_page' => $posts_per_page,
		'orderby'        => $orderby,
		'order'          => $order,
		'post_status'    => 'publish',
		'no_found_rows'  => true,
	);

	if ( ! empty( $category ) && 'all' !== $category ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'category',
				'field'    => 'slug',
				'terms'    => $category,
			),
		);
	}

	$query = new WP_Query( $args );

	ob_start();
	echo '<div class="posts-grid">';
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			stories_loop_template_part( get_post_format() );
		}
		wp_reset_postdata();
	} else {
		echo '<p class="no-news-found">' . esc_html__( 'No se encontraron noticias en esta categoría.', 'thecrisisacademy' ) . '</p>';
	}
	echo '</div>';
	$html = ob_get_clean();

	wp_send_json_success( array( 'html' => $html ) );
}
add_action( 'wp_ajax_thecrisisacademy_filter_news', 'thecrisisacademy_ajax_filter_news' );
add_action( 'wp_ajax_nopriv_thecrisisacademy_filter_news', 'thecrisisacademy_ajax_filter_news' );
