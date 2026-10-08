<?php
/**
 * Custom Post Type: FAQ (Preguntas Frecuentes)
 *
 * Implements native CPT for corporate FAQs with custom ordering and automatic seeding.
 *
 * @package TheCrisisAcademy
 * @subpackage Inc
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Custom Post Type: FAQ (Preguntas Frecuentes)
 */
function thecrisisacademy_register_faq_cpt() {
	$labels = array(
		'name'                  => _x( 'Preguntas Frecuentes', 'Post Type General Name', 'thecrisisacademy' ),
		'singular_name'         => _x( 'Pregunta Frecuente', 'Post Type Singular Name', 'thecrisisacademy' ),
		'menu_name'             => __( 'FAQ', 'thecrisisacademy' ),
		'name_admin_bar'        => __( 'Pregunta FAQ', 'thecrisisacademy' ),
		'archives'              => __( 'Archivo de Preguntas', 'thecrisisacademy' ),
		'attributes'            => __( 'Atributos de Pregunta', 'thecrisisacademy' ),
		'all_items'             => __( 'Todas las Preguntas', 'thecrisisacademy' ),
		'add_new_item'          => __( 'Añadir Nueva Pregunta', 'thecrisisacademy' ),
		'add_new'               => __( 'Añadir Pregunta', 'thecrisisacademy' ),
		'new_item'              => __( 'Nueva Pregunta', 'thecrisisacademy' ),
		'edit_item'             => __( 'Editar Pregunta', 'thecrisisacademy' ),
		'update_item'           => __( 'Actualizar Pregunta', 'thecrisisacademy' ),
		'view_item'             => __( 'Ver Pregunta', 'thecrisisacademy' ),
		'view_items'            => __( 'Ver Preguntas', 'thecrisisacademy' ),
		'search_items'          => __( 'Buscar Preguntas', 'thecrisisacademy' ),
		'not_found'             => __( 'No se encontraron preguntas', 'thecrisisacademy' ),
		'not_found_in_trash'    => __( 'No se encontraron preguntas en la papelera', 'thecrisisacademy' ),
		'items_list'            => __( 'Lista de preguntas', 'thecrisisacademy' ),
		'items_list_navigation' => __( 'Navegación de lista de preguntas', 'thecrisisacademy' ),
		'filter_items_list'     => __( 'Filtrar lista de preguntas', 'thecrisisacademy' ),
	);

	$args = array(
		'label'               => __( 'Pregunta Frecuente', 'thecrisisacademy' ),
		'description'         => __( 'Preguntas y respuestas frecuentes para el acordeón corporativo', 'thecrisisacademy' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'page-attributes', 'revisions' ),
		'hierarchical'        => false,
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 22,
		'menu_icon'           => 'dashicons-editor-help',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => false,
		'can_export'          => true,
		'has_archive'         => false,
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'capability_type'     => 'post',
		'show_in_rest'        => true,
	);

	register_post_type( 'faq', $args );
}
add_action( 'init', 'thecrisisacademy_register_faq_cpt', 0 );

/**
 * Customize FAQ admin columns
 *
 * @param array $columns Existing columns
 * @return array
 */
function thecrisisacademy_faq_admin_columns( $columns ) {
	$new_columns = array(
		'cb'         => $columns['cb'],
		'title'      => __( 'Pregunta', 'thecrisisacademy' ),
		'faq_answer' => __( 'Respuesta', 'thecrisisacademy' ),
		'menu_order' => __( 'Orden', 'thecrisisacademy' ),
		'date'       => $columns['date'],
	);
	return $new_columns;
}
add_filter( 'manage_faq_posts_columns', 'thecrisisacademy_faq_admin_columns' );

/**
 * Render FAQ custom column content
 *
 * @param string $column  Column name
 * @param int    $post_id Post ID
 */
function thecrisisacademy_faq_admin_column_content( $column, $post_id ) {
	if ( 'faq_answer' === $column ) {
		$content = get_post_field( 'post_content', $post_id );
		$clean   = wp_strip_all_tags( $content );
		echo esc_html( wp_trim_words( $clean, 14, '...' ) );
	} elseif ( 'menu_order' === $column ) {
		$order = (int) get_post_field( 'menu_order', $post_id );
		echo '<strong style="color:#0284c7;">' . esc_html( (string) $order ) . '</strong>';
	}
}
add_action( 'manage_faq_posts_custom_column', 'thecrisisacademy_faq_admin_column_content', 10, 2 );

/**
 * Make menu_order sortable in FAQ list table
 *
 * @param array $columns Sortable columns
 * @return array
 */
function thecrisisacademy_faq_sortable_columns( $columns ) {
	$columns['menu_order'] = 'menu_order';
	return $columns;
}
add_filter( 'manage_edit-faq_sortable_columns', 'thecrisisacademy_faq_sortable_columns' );

/**
 * Register Metabox for FAQ Display Order
 */
function thecrisisacademy_register_faq_metaboxes() {
	add_meta_box(
		'thecrisisacademy_faq_order_metabox',
		'🔢 ' . __( 'Orden de Visualización', 'thecrisisacademy' ),
		'thecrisisacademy_render_faq_order_metabox',
		'faq',
		'side',
		'high'
	);
}
add_action( 'add_meta_boxes', 'thecrisisacademy_register_faq_metaboxes' );

/**
 * Render FAQ Display Order Metabox
 *
 * @param WP_Post $post Current post
 */
function thecrisisacademy_render_faq_order_metabox( $post ) {
	wp_nonce_field( 'thecrisisacademy_save_faq_order', 'thecrisisacademy_faq_order_nonce' );
	$order = (int) $post->menu_order;
	?>
	<p style="margin:0 0 8px 0; color:#64748b; font-size:12px;">
		<?php esc_html_e( 'Define la posición de esta pregunta dentro del acordeón (1 aparece primero, 2 segundo, etc.).', 'thecrisisacademy' ); ?>
	</p>
	<label for="thecrisisacademy_faq_menu_order" style="display:block; font-weight:600; margin-bottom:4px; font-size:13px;">
		<?php esc_html_e( 'Posición numérica:', 'thecrisisacademy' ); ?>
	</label>
	<input type="number" id="thecrisisacademy_faq_menu_order" name="thecrisisacademy_faq_menu_order" value="<?php echo esc_attr( (string) $order ); ?>" min="0" step="1" class="widefat" />
	<?php
}

/**
 * Save FAQ Display Order Metabox
 *
 * @param int $post_id Post ID
 */
function thecrisisacademy_save_faq_order_meta( $post_id ) {
	if ( ! isset( $_POST['thecrisisacademy_faq_order_nonce'] ) || ! wp_verify_nonce( $_POST['thecrisisacademy_faq_order_nonce'], 'thecrisisacademy_save_faq_order' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['thecrisisacademy_faq_menu_order'] ) ) {
		$order = absint( $_POST['thecrisisacademy_faq_menu_order'] );
		// Unhook to prevent infinite loop
		remove_action( 'save_post', 'thecrisisacademy_save_faq_order_meta' );
		wp_update_post( array(
			'ID'         => $post_id,
			'menu_order' => $order,
		) );
		add_action( 'save_post', 'thecrisisacademy_save_faq_order_meta' );
	}
}
add_action( 'save_post_faq', 'thecrisisacademy_save_faq_order_meta' );

/**
 * Initial dataset with the 10 corporate questions and answers from faq.php
 *
 * @return array
 */
function thecrisisacademy_get_initial_faq_data() {
	return array(
		array(
			'question' => '¿En qué se especializa The Crisis Academy?',
			'answer'   => 'Nos especializamos en ayudar a organizaciones y líderes a diseñar, construir y escalar protocolos de respuesta ante crisis. Nuestra experiencia incluye estrategia de reputación, entrenamiento de voceros, simulacros en tiempo real y auditorías de riesgo para empresas de todos los sectores.',
			'order'    => 1,
		),
		array(
			'question' => '¿Trabajan con startups y empresas en etapas iniciales?',
			'answer'   => 'Sí, adaptamos nuestros marcos de trabajo para equipos ágiles. Creemos que es vital tener una estructura de respuesta rápida desde el día uno para proteger la reputación conforme el negocio escala.',
			'order'    => 2,
		),
		array(
			'question' => '¿Cuánto tiempo toma un proceso de consultoría típico?',
			'answer'   => 'El tiempo varía según la complejidad de la organización, pero un diagnóstico y plan de respuesta inicial suelen establecerse en un periodo de 4 a 6 semanas, con seguimiento continuo.',
			'order'    => 3,
		),
		array(
			'question' => '¿Pueden integrar Inteligencia Artificial en nuestra gestión de crisis?',
			'answer'   => 'Absolutamente. Implementamos herramientas de monitoreo impulsadas por IA para detección temprana de sentimientos y tendencias negativas, automatizando alertas para que tu equipo gane tiempo valioso.',
			'order'    => 4,
		),
		array(
			'question' => '¿Qué información necesitan de mi parte para empezar?',
			'answer'   => 'Únicamente acceso a tus canales de comunicación actuales, organigrama clave y un recuento de incidentes previos (si existen). A partir de ahí, realizamos un kick-off de inmersión profunda.',
			'order'    => 5,
		),
		array(
			'question' => '¿Cómo aseguran la calidad del entrenamiento de voceros?',
			'answer'   => 'Por medio de sesiones prácticas con simulaciones de escenarios reales. Grabamos y analizamos cada intervención para pulir lenguaje corporal, tono de voz y mensajes clave. Además, cada vocero recibe un informe de desempeño personalizado.',
			'order'    => 6,
		),
		array(
			'question' => '¿Cómo funciona el simulacro en tiempo real?',
			'answer'   => 'Montamos un escenario de crisis específico para tu empresa (ej. filtración de datos, accidente en planta) y te sometemos a una transmisión en vivo. En minutos, recibes feedback sobre tus respuestas, tono y manejo de preguntas difíciles.',
			'order'    => 7,
		),
		array(
			'question' => '¿Pueden diseñar un protocolo personalizado para mi empresa?',
			'answer'   => 'Sí, cada protocolo se diseña desde cero basándonos en tu sector, tamaño y riesgos específicos. Integramos tus herramientas actuales y definimos flujos claros de alerta y respuesta para que tu equipo sepa exactamente qué hacer.',
			'order'    => 8,
		),
		array(
			'question' => '¿Qué tipo de empresas han trabajado?',
			'answer'   => 'Hemos trabajado con empresas de tecnología, banca, salud, retail y gobiernos latinoamericanos. Nuestros marcos son adaptables a cualquier industria que enfrente riesgos reputacionales.',
			'order'    => 9,
		),
		array(
			'question' => '¿Cuál es el primer paso para contratar sus servicios?',
			'answer'   => 'Agenda una llamada introductoria gratuita de 15 minutos. En esa sesión evaluamos tu situación actual y te recomendamos el plan ideal para tu organización.',
			'order'    => 10,
		),
	);
}

/**
 * Seed default FAQs into the database
 *
 * @param bool $force Force re-seeding even if already seeded
 * @return int Number of inserted posts
 */
function thecrisisacademy_seed_default_faqs( $force = false ) {
	$inserted = 0;
	$initial_items = thecrisisacademy_get_initial_faq_data();

	foreach ( $initial_items as $item ) {
		// Avoid duplicate questions by checking if a post with this title exists
		$existing = get_page_by_title( $item['question'], OBJECT, 'faq' );
		if ( ! $existing || $force ) {
			$post_id = wp_insert_post( array(
				'post_title'   => $item['question'],
				'post_content' => $item['answer'],
				'post_status'  => 'publish',
				'post_type'    => 'faq',
				'menu_order'   => (int) $item['order'],
			) );

			if ( $post_id && ! is_wp_error( $post_id ) ) {
				$inserted++;
			}
		}
	}

	update_option( 'thecrisisacademy_faqs_seeded_v1', '1' );
	return $inserted;
}

/**
 * Automatically seed FAQs on first administrative visit if no FAQs exist
 */
function thecrisisacademy_auto_seed_faqs() {
	if ( ! is_admin() ) {
		return;
	}

	if ( get_option( 'thecrisisacademy_faqs_seeded_v1' ) !== '1' ) {
		$faq_count = (int) wp_count_posts( 'faq' )->publish;
		if ( 0 === $faq_count ) {
			thecrisisacademy_seed_default_faqs();
		}
	}
}
add_action( 'admin_init', 'thecrisisacademy_auto_seed_faqs' );

/**
 * Handle manual FAQ seeding button from metabox
 */
function thecrisisacademy_handle_manual_seed_faqs() {
	if ( ! current_user_can( 'edit_pages' ) ) {
		wp_die( esc_html__( 'Permisos insuficientes.', 'thecrisisacademy' ) );
	}

	check_admin_referer( 'thecrisisacademy_seed_faqs_action', 'thecrisisacademy_seed_faqs_nonce' );

	$count = thecrisisacademy_seed_default_faqs( false );

	$redirect_url = isset( $_GET['redirect_to'] ) ? esc_url_raw( wp_unslash( $_GET['redirect_to'] ) ) : admin_url( 'edit.php?post_type=faq' );
	$redirect_url = add_query_arg( 'faqs_seeded', $count, $redirect_url );

	wp_safe_redirect( $redirect_url );
	exit;
}
add_action( 'admin_post_thecrisisacademy_seed_faqs', 'thecrisisacademy_handle_manual_seed_faqs' );
