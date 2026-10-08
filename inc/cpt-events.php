<?php
/**
 * Custom Post Type: Events (Eventos)
 *
 * Implements native CPT for corporate events and automatic trashing of past events.
 *
 * @package TheCrisisAcademy
 * @subpackage Inc
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Custom Post Type: Event (Eventos)
 */
function thecrisisacademy_register_events_cpt() {
	$labels = array(
		'name'                  => _x( 'Eventos', 'Post Type General Name', 'thecrisisacademy' ),
		'singular_name'         => _x( 'Evento', 'Post Type Singular Name', 'thecrisisacademy' ),
		'menu_name'             => __( 'Eventos', 'thecrisisacademy' ),
		'name_admin_bar'        => __( 'Evento', 'thecrisisacademy' ),
		'archives'              => __( 'Archivo de Eventos', 'thecrisisacademy' ),
		'attributes'            => __( 'Atributos de Evento', 'thecrisisacademy' ),
		'all_items'             => __( 'Todos los Eventos', 'thecrisisacademy' ),
		'add_new_item'          => __( 'Añadir Nuevo Evento', 'thecrisisacademy' ),
		'add_new'               => __( 'Añadir Evento', 'thecrisisacademy' ),
		'new_item'              => __( 'Nuevo Evento', 'thecrisisacademy' ),
		'edit_item'             => __( 'Editar Evento', 'thecrisisacademy' ),
		'update_item'           => __( 'Actualizar Evento', 'thecrisisacademy' ),
		'view_item'             => __( 'Ver Evento', 'thecrisisacademy' ),
		'view_items'            => __( 'Ver Eventos', 'thecrisisacademy' ),
		'search_items'          => __( 'Buscar Eventos', 'thecrisisacademy' ),
		'not_found'             => __( 'No se encontraron eventos', 'thecrisisacademy' ),
		'not_found_in_trash'    => __( 'No se encontraron eventos en la papelera', 'thecrisisacademy' ),
		'featured_image'        => __( 'Imagen destacada', 'thecrisisacademy' ),
		'set_featured_image'    => __( 'Establecer imagen destacada', 'thecrisisacademy' ),
		'remove_featured_image' => __( 'Eliminar imagen destacada', 'thecrisisacademy' ),
		'use_featured_image'    => __( 'Usar como imagen destacada', 'thecrisisacademy' ),
		'items_list'            => __( 'Lista de eventos', 'thecrisisacademy' ),
		'items_list_navigation' => __( 'Navegación de lista de eventos', 'thecrisisacademy' ),
		'filter_items_list'     => __( 'Filtrar lista de eventos', 'thecrisisacademy' ),
	);

	$args = array(
		'label'               => __( 'Evento', 'thecrisisacademy' ),
		'description'         => __( 'Eventos corporativos y capacitaciones', 'thecrisisacademy' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 21,
		'menu_icon'           => 'dashicons-calendar-alt',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => false,
		'exclude_from_search' => true,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
		'show_in_rest'        => true,
	);

	register_post_type( 'event', $args );
}
add_action( 'init', 'thecrisisacademy_register_events_cpt', 0 );

/**
 * Register Metabox for Event Details
 */
function thecrisisacademy_register_event_metaboxes() {
	add_meta_box(
		'thecrisisacademy_event_details',
		'📅 ' . __( 'Detalles y Fecha del Evento', 'thecrisisacademy' ),
		'thecrisisacademy_render_event_metabox',
		'event',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'thecrisisacademy_register_event_metaboxes' );

/**
 * Render Event Details Metabox
 *
 * @param WP_Post $post Current post object
 */
function thecrisisacademy_render_event_metabox( $post ) {
	wp_nonce_field( 'thecrisisacademy_save_event_meta', 'thecrisisacademy_event_nonce' );

	$event_date     = get_post_meta( $post->ID, '_event_date', true );
	$event_time     = get_post_meta( $post->ID, '_event_time', true );
	$event_location = get_post_meta( $post->ID, '_event_location', true );
	$event_featured = get_post_meta( $post->ID, '_event_featured', true );
	$event_link     = get_post_meta( $post->ID, '_event_link', true );
	?>
	<div style="padding:10px 0;">
		<div style="margin-bottom:16px;">
			<label for="event_date" style="display:block; font-weight:600; margin-bottom:5px;">
				<?php esc_html_e( 'Fecha del Evento (Obligatorio)', 'thecrisisacademy' ); ?> <span style="color:#dc2626;">*</span>
			</label>
			<input type="date" id="event_date" name="event_date" value="<?php echo esc_attr( $event_date ); ?>" required style="padding:6px 10px; border-radius:4px; border:1px solid #cbd5e1; width:220px;" />
			<p class="description" style="margin-top:4px;">
				<?php esc_html_e( 'Los eventos cuya fecha sea anterior al día de hoy se moverán automáticamente a la papelera.', 'thecrisisacademy' ); ?>
			</p>
		</div>

		<div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px; margin-bottom:16px;">
			<div>
				<label for="event_time" style="display:block; font-weight:600; margin-bottom:5px;">
					<?php esc_html_e( 'Horario (Opcional)', 'thecrisisacademy' ); ?>
				</label>
				<input type="text" id="event_time" name="event_time" value="<?php echo esc_attr( $event_time ); ?>" placeholder="Ej. 10:00 hrs o 18:00 - 20:00 hrs" class="widefat" />
			</div>
			<div>
				<label for="event_location" style="display:block; font-weight:600; margin-bottom:5px;">
					<?php esc_html_e( 'Ubicación o Plataforma', 'thecrisisacademy' ); ?>
				</label>
				<input type="text" id="event_location" name="event_location" value="<?php echo esc_attr( $event_location ? $event_location : '@Campus online - Zoom' ); ?>" placeholder="Ej. @Campus online - Zoom" class="widefat" />
			</div>
		</div>

		<div style="margin-bottom:16px;">
			<label for="event_link" style="display:block; font-weight:600; margin-bottom:5px;">
				<?php esc_html_e( 'Enlace de Registro / Información (Opcional)', 'thecrisisacademy' ); ?>
			</label>
			<input type="url" id="event_link" name="event_link" value="<?php echo esc_url( $event_link ); ?>" placeholder="https://..." class="widefat" />
		</div>

		<div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:6px; padding:12px;">
			<label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-weight:600; margin:0;">
				<input type="checkbox" name="event_featured" value="1" <?php checked( $event_featured, '1' ); ?> />
				<span><?php esc_html_e( 'Destacar este evento (Aplica estilo visual destacado con borde distintivo)', 'thecrisisacademy' ); ?></span>
			</label>
		</div>
	</div>
	<?php
}

/**
 * Save Event Details Metabox
 *
 * @param int $post_id Post ID
 */
function thecrisisacademy_save_event_meta( $post_id ) {
	if ( ! isset( $_POST['thecrisisacademy_event_nonce'] ) || ! wp_verify_nonce( $_POST['thecrisisacademy_event_nonce'], 'thecrisisacademy_save_event_meta' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['event_date'] ) ) {
		update_post_meta( $post_id, '_event_date', sanitize_text_field( $_POST['event_date'] ) );
	}

	if ( isset( $_POST['event_time'] ) ) {
		update_post_meta( $post_id, '_event_time', sanitize_text_field( $_POST['event_time'] ) );
	}

	if ( isset( $_POST['event_location'] ) ) {
		update_post_meta( $post_id, '_event_location', sanitize_text_field( $_POST['event_location'] ) );
	}

	if ( isset( $_POST['event_link'] ) ) {
		update_post_meta( $post_id, '_event_link', esc_url_raw( $_POST['event_link'] ) );
	}

	$featured = isset( $_POST['event_featured'] ) ? '1' : '0';
	update_post_meta( $post_id, '_event_featured', $featured );

	// Trigger cleanup after saving
	thecrisisacademy_cleanup_past_events();
}
add_action( 'save_post_event', 'thecrisisacademy_save_event_meta' );

/**
 * Custom Admin Columns for Events
 *
 * @param array $columns Existing columns
 * @return array
 */
function thecrisisacademy_event_admin_columns( $columns ) {
	$new_columns = array(
		'cb'             => $columns['cb'],
		'title'          => __( 'Título del Evento', 'thecrisisacademy' ),
		'event_date'     => __( 'Fecha', 'thecrisisacademy' ),
		'event_time'     => __( 'Horario', 'thecrisisacademy' ),
		'event_location' => __( 'Ubicación', 'thecrisisacademy' ),
		'event_featured' => __( 'Destacado', 'thecrisisacademy' ),
		'event_status'   => __( 'Vigencia', 'thecrisisacademy' ),
		'date'           => $columns['date'],
	);
	return $new_columns;
}
add_filter( 'manage_event_posts_columns', 'thecrisisacademy_event_admin_columns' );

/**
 * Display Custom Column Data for Events
 *
 * @param string $column  Column name
 * @param int    $post_id Post ID
 */
function thecrisisacademy_event_admin_column_data( $column, $post_id ) {
	$today = current_time( 'Y-m-d' );
	switch ( $column ) {
		case 'event_date':
			$date = get_post_meta( $post_id, '_event_date', true );
			if ( $date ) {
				echo esc_html( wp_date( 'd F Y', strtotime( $date ) ) );
			} else {
				echo '<span style="color:#94a3b8;">—</span>';
			}
			break;

		case 'event_time':
			$time = get_post_meta( $post_id, '_event_time', true );
			echo esc_html( $time ? $time : '—' );
			break;

		case 'event_location':
			$loc = get_post_meta( $post_id, '_event_location', true );
			echo esc_html( $loc ? $loc : '—' );
			break;

		case 'event_featured':
			$featured = get_post_meta( $post_id, '_event_featured', true );
			echo '1' === $featured ? '<span style="color:#10b981; font-weight:700;">★ Sí</span>' : '<span style="color:#94a3b8;">No</span>';
			break;

		case 'event_status':
			$date = get_post_meta( $post_id, '_event_date', true );
			if ( ! $date ) {
				echo '<span style="color:#94a3b8;">Sin fecha</span>';
			} elseif ( $date < $today ) {
				echo '<span style="background:#fee2e2; color:#b91c1c; padding:2px 8px; border-radius:4px; font-weight:600; font-size:11px;">Vencido</span>';
			} else {
				echo '<span style="background:#dcfce7; color:#15803d; padding:2px 8px; border-radius:4px; font-weight:600; font-size:11px;">Próximo</span>';
			}
			break;
	}
}
add_action( 'manage_event_posts_custom_column', 'thecrisisacademy_event_admin_column_data', 10, 2 );

/**
 * Make Event Date Column Sortable
 *
 * @param array $columns Sortable columns
 * @return array
 */
function thecrisisacademy_event_sortable_columns( $columns ) {
	$columns['event_date'] = 'event_date';
	return $columns;
}
add_filter( 'manage_edit-event_sortable_columns', 'thecrisisacademy_event_sortable_columns' );

/**
 * Order Events by Event Date in Admin
 *
 * @param WP_Query $query Main query
 */
function thecrisisacademy_event_orderby_date( $query ) {
	if ( ! is_admin() || ! $query->is_main_query() ) {
		return;
	}

	if ( 'event' === $query->get( 'post_type' ) ) {
		$orderby = $query->get( 'orderby' );
		if ( 'event_date' === $orderby ) {
			$query->set( 'meta_key', '_event_date' );
			$query->set( 'orderby', 'meta_value' );
		}
	}
}
add_action( 'pre_get_posts', 'thecrisisacademy_event_orderby_date' );

/**
 * Automatically move past events to trash
 *
 * Checks for published or draft events whose event date is strictly before today,
 * and moves them to trash so they no longer appear on the site or clog active queries.
 */
function thecrisisacademy_cleanup_past_events() {
	$today = current_time( 'Y-m-d' );

	$past_events = get_posts( array(
		'post_type'      => 'event',
		'post_status'    => array( 'publish', 'draft' ),
		'posts_per_page' => 50,
		'meta_query'     => array(
			array(
				'key'     => '_event_date',
				'value'   => $today,
				'compare' => '<',
				'type'    => 'DATE',
			),
		),
		'fields'         => 'ids',
	) );

	if ( ! empty( $past_events ) ) {
		foreach ( $past_events as $event_id ) {
			wp_trash_post( $event_id );
		}
	}
}

/**
 * Run past events cleanup on schedule and on admin init (throttled)
 */
function thecrisisacademy_schedule_events_cleanup() {
	if ( ! wp_next_scheduled( 'thecrisisacademy_daily_events_cleanup' ) ) {
		wp_schedule_event( time(), 'daily', 'thecrisisacademy_daily_events_cleanup' );
	}
}
add_action( 'wp', 'thecrisisacademy_schedule_events_cleanup' );
add_action( 'thecrisisacademy_daily_events_cleanup', 'thecrisisacademy_cleanup_past_events' );

/**
 * Throttled cleanup on admin screen (max once every 30 minutes)
 */
function thecrisisacademy_admin_throttled_cleanup() {
	if ( false === get_transient( 'thecrisisacademy_events_cleanup_transient' ) ) {
		thecrisisacademy_cleanup_past_events();
		set_transient( 'thecrisisacademy_events_cleanup_transient', 1, 1800 ); // 30 minutes
	}
}
add_action( 'admin_init', 'thecrisisacademy_admin_throttled_cleanup' );

/**
 * Seed sample upcoming events if no events exist in the database
 */
function thecrisisacademy_seed_sample_events_if_empty() {
	if ( get_option( 'thecrisisacademy_events_seeded_v1' ) === '1' ) {
		return;
	}

	// Check if any event posts already exist (including trash)
	$existing = get_posts( array(
		'post_type'      => 'event',
		'post_status'    => 'any',
		'posts_per_page' => 1,
		'fields'         => 'ids',
	) );

	if ( ! empty( $existing ) ) {
		update_option( 'thecrisisacademy_events_seeded_v1', '1' );
		return;
	}

	$sample_events = array(
		array(
			'title'       => 'Investigación y estudios de crisis. Radar de riesgos: tendencias 2026 y casos actuales',
			'date'        => '2026-10-12',
			'time'        => '',
			'location'    => '@Campus online - Zoom',
			'featured'    => '1',
		),
		array(
			'title'       => 'Herramientas y parámetros de medición de una crisis y su respuesta',
			'date'        => '2026-10-19',
			'time'        => '',
			'location'    => '@Campus online - Zoom',
			'featured'    => '0',
		),
		array(
			'title'       => 'Entorno mediático y digital: el nuevo rol de la Inteligencia Artificial',
			'date'        => '2026-10-26',
			'time'        => '',
			'location'    => '@Campus online - Zoom',
			'featured'    => '0',
		),
		array(
			'title'       => 'Comunicación estratégica para manejo de crisis 4.0',
			'date'        => '2026-11-02',
			'time'        => '',
			'location'    => '@Campus online - Zoom',
			'featured'    => '0',
		),
		array(
			'title'       => 'Procesos para un manejo ágil de crisis en redes sociales',
			'date'        => '2026-11-09',
			'time'        => '',
			'location'    => '@Campus online - Zoom',
			'featured'    => '0',
		),
		array(
			'title'       => 'Control de narrativa y arquitectura de vocerías',
			'date'        => '2026-11-16',
			'time'        => '',
			'location'    => '@Campus online - Zoom',
			'featured'    => '0',
		),
	);

	foreach ( $sample_events as $event_data ) {
		$post_id = wp_insert_post( array(
			'post_title'   => $event_data['title'],
			'post_type'    => 'event',
			'post_status'  => 'publish',
		) );

		if ( $post_id && ! is_wp_error( $post_id ) ) {
			update_post_meta( $post_id, '_event_date', $event_data['date'] );
			update_post_meta( $post_id, '_event_time', $event_data['time'] );
			update_post_meta( $post_id, '_event_location', $event_data['location'] );
			update_post_meta( $post_id, '_event_featured', $event_data['featured'] );
		}
	}

	update_option( 'thecrisisacademy_events_seeded_v1', '1' );
}
add_action( 'init', 'thecrisisacademy_seed_sample_events_if_empty', 20 );
