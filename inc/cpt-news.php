<?php
/**
 * Register Custom Post Type: News (Noticias)
 *
 * Extracted from cpt-noticias.json to run natively within the child theme
 * without external plugin dependencies.
 *
 * @package TheCrisisAcademy
 * @subpackage Inc
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Register Custom Post Type: News (Noticias)
 */
function thecrisisacademy_register_news_cpt() {
	$labels = array(
		'name'                     => _x( 'Noticias', 'Post Type General Name', 'thecrisisacademy' ),
		'singular_name'            => _x( 'Noticia', 'Post Type Singular Name', 'thecrisisacademy' ),
		'menu_name'                => __( 'Noticias', 'thecrisisacademy' ),
		'name_admin_bar'           => __( 'Noticia', 'thecrisisacademy' ),
		'archives'                 => __( 'Archivo de Noticias', 'thecrisisacademy' ),
		'attributes'               => __( 'Atributos de Noticia', 'thecrisisacademy' ),
		'parent_item_colon'        => __( 'Noticia Padre:', 'thecrisisacademy' ),
		'all_items'                => __( 'Todas las Noticias', 'thecrisisacademy' ),
		'add_new_item'             => __( 'Añadir Nueva Noticia', 'thecrisisacademy' ),
		'add_new'                  => __( 'Añadir Noticia', 'thecrisisacademy' ),
		'new_item'                 => __( 'Nueva Noticia', 'thecrisisacademy' ),
		'edit_item'                => __( 'Editar Noticia', 'thecrisisacademy' ),
		'update_item'              => __( 'Actualizar Noticia', 'thecrisisacademy' ),
		'view_item'                => __( 'Ver Noticia', 'thecrisisacademy' ),
		'view_items'               => __( 'Ver Noticias', 'thecrisisacademy' ),
		'search_items'             => __( 'Buscar Noticias', 'thecrisisacademy' ),
		'not_found'                => __( 'No se encontraron noticias', 'thecrisisacademy' ),
		'not_found_in_trash'       => __( 'No se encontraron noticias en la papelera', 'thecrisisacademy' ),
		'featured_image'           => __( 'Imagen destacada', 'thecrisisacademy' ),
		'set_featured_image'       => __( 'Establecer imagen destacada', 'thecrisisacademy' ),
		'remove_featured_image'    => __( 'Eliminar imagen destacada', 'thecrisisacademy' ),
		'use_featured_image'       => __( 'Usar como imagen destacada', 'thecrisisacademy' ),
		'insert_into_item'         => __( 'Insertar en noticia', 'thecrisisacademy' ),
		'uploaded_to_this_item'    => __( 'Subido a esta noticia', 'thecrisisacademy' ),
		'items_list'               => __( 'Lista de noticias', 'thecrisisacademy' ),
		'items_list_navigation'    => __( 'Navegación de lista de noticias', 'thecrisisacademy' ),
		'filter_items_list'        => __( 'Filtrar lista de noticias', 'thecrisisacademy' ),
		'filter_by_date'           => __( 'Filtrar noticias por fecha', 'thecrisisacademy' ),
		'item_published'           => __( 'Noticia publicada.', 'thecrisisacademy' ),
		'item_published_privately' => __( 'Noticia publicada privadamente.', 'thecrisisacademy' ),
		'item_reverted_to_draft'   => __( 'Noticia devuelta a borrador.', 'thecrisisacademy' ),
		'item_scheduled'           => __( 'Noticia programada.', 'thecrisisacademy' ),
		'item_updated'             => __( 'Noticia actualizada.', 'thecrisisacademy' ),
		'item_link'                => __( 'Enlace de la noticia', 'thecrisisacademy' ),
		'item_link_description'    => __( 'Un enlace a una noticia.', 'thecrisisacademy' ),
	);

	$args = array(
		'label'                 => __( 'Noticia', 'thecrisisacademy' ),
		'description'           => __( 'Noticias relacionadas con crisis mediáticas', 'thecrisisacademy' ),
		'labels'                => $labels,
		'supports'              => array(
			'title',
			'editor',
			'thumbnail',
			'author',
			'trackbacks',
			'revisions',
			'custom-fields',
			'excerpt',
			'page-attributes',
			'post-formats',
		),
		'taxonomies'            => array( 'category', 'post_tag' ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 20,
		'menu_icon'             => 'dashicons-admin-links',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => 'news',
		'exclude_from_search'   => true,
		'publicly_queryable'    => true,
		'capability_type'       => 'post',
		'map_meta_cap'          => true,
		'show_in_rest'          => true,
		'rest_base'             => 'news',
		'rest_controller_class' => 'WP_REST_Posts_Controller',
		'rewrite'               => array(
			'slug'       => 'news',
			'with_front' => true,
			'feeds'      => false,
			'pages'      => true,
		),
		'query_var'             => true,
		'delete_with_user'      => false,
	);

	register_post_type( 'news', $args );

	// Enable post formats and category taxonomy support for news.
	register_taxonomy_for_object_type( 'post_format', 'news' );
	register_taxonomy_for_object_type( 'category', 'news' );
	register_taxonomy_for_object_type( 'post_tag', 'news' );
}
add_action( 'init', 'thecrisisacademy_register_news_cpt', 0 );

/**
 * Flush rewrite rules once after post type registration to prevent 404s.
 */
function thecrisisacademy_flush_news_rewrites() {
	if ( get_option( 'thecrisisacademy_news_rewrite_flushed_v1' ) !== '1' ) {
		flush_rewrite_rules( false );
		update_option( 'thecrisisacademy_news_rewrite_flushed_v1', '1' );
	}
}
add_action( 'init', 'thecrisisacademy_flush_news_rewrites', 99 );
