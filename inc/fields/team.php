<?php
/**
 * Team Page Custom Field Groups & Repeaters (Native ACF-alternative)
 *
 * Provides native WordPress meta boxes and data getters for the Team
 * landing page template (templates/team.php).
 *
 * @package TheCrisisAcademy
 * @subpackage Team
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ==========================================================================
   1. Helper: Determine if current post is using Team Template
   ========================================================================== */

function thecrisisacademy_is_team_page( $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}
	if ( ! $post_id ) {
		return false;
	}

	$template = get_page_template_slug( $post_id );
	if ( 'templates/team.php' === $template ) {
		return true;
	}

	$post = get_post( $post_id );
	if ( $post && in_array( $post->post_name, array( 'equipo', 'team' ), true ) ) {
		return true;
	}

	return false;
}

/* ==========================================================================
   2. Data Getters with Defaults
   ========================================================================== */

function thecrisisacademy_get_team_hero_data( $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}
	return array(
		'tag'         => get_post_meta( $post_id, 'team_hero_tag', true ) ?: 'Nuestro Equipo',
		'title'       => get_post_meta( $post_id, 'team_hero_title', true ) ?: 'Especialistas en Gestión y Comunicación Estratégica de Crisis',
		'description' => get_post_meta( $post_id, 'team_hero_description', true ) ?: 'Conoce al equipo de consultores, analistas y voceros que lideran la preparación y respuesta ante situaciones críticas.',
	);
}

/* ==========================================================================
   3. Meta Boxes Registration for Team
   ========================================================================== */

function thecrisisacademy_register_team_metaboxes() {
	$screen  = get_current_screen();
	$post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : ( isset( $_POST['post_ID'] ) ? absint( $_POST['post_ID'] ) : 0 );

	if ( ! $post_id && $screen && 'page' === $screen->post_type ) {
		global $post;
		$post_id = $post ? $post->ID : 0;
	}

	if ( ! $post_id || ! thecrisisacademy_is_team_page( $post_id ) ) {
		return;
	}

	// 1. Team Hero
	add_meta_box(
		'thecrisisacademy_team_hero_metabox',
		'Equipo · Hero (Cabecera)',
		'thecrisisacademy_render_team_hero_metabox',
		'page',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'thecrisisacademy_register_team_metaboxes', 10 );

/* ==========================================================================
   4. Meta Box Renders
   ========================================================================== */

function thecrisisacademy_render_team_hero_metabox( $post ) {
	if ( function_exists( 'thecrisisacademy_render_individuals_metabox_styles' ) ) {
		thecrisisacademy_render_individuals_metabox_styles();
	}
	$data = thecrisisacademy_get_team_hero_data( $post->ID );
	?>
	<div class="tca-ind-wrap">
		<div class="tca-ind-field">
			<label class="tca-ind-label" for="team_hero_tag">Etiqueta Superior</label>
			<input type="text" id="team_hero_tag" name="team_hero[tag]" value="<?php echo esc_attr( $data['tag'] ); ?>" class="large-text" />
		</div>
		<div class="tca-ind-field">
			<label class="tca-ind-label" for="team_hero_title">Título Principal</label>
			<textarea id="team_hero_title" name="team_hero[title]" rows="2" class="large-text"><?php echo esc_textarea( $data['title'] ); ?></textarea>
		</div>
		<div class="tca-ind-field">
			<label class="tca-ind-label" for="team_hero_description">Descripción</label>
			<textarea id="team_hero_description" name="team_hero[description]" rows="3" class="large-text"><?php echo esc_textarea( $data['description'] ); ?></textarea>
		</div>
	</div>
	<?php
}

/* ==========================================================================
   5. Save Handler for Team
   ========================================================================== */

function thecrisisacademy_save_team_metaboxes( $post_id, $post ) {
	if ( ! $post instanceof WP_Post || 'page' !== $post->post_type ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_page', $post_id ) ) {
		return;
	}
	if ( ! thecrisisacademy_is_team_page( $post_id ) ) {
		return;
	}

	if ( isset( $_POST['team_hero'] ) && is_array( $_POST['team_hero'] ) ) {
		$hero = $_POST['team_hero'];
		update_post_meta( $post_id, 'team_hero_tag', sanitize_text_field( $hero['tag'] ?? '' ) );
		update_post_meta( $post_id, 'team_hero_title', sanitize_text_field( $hero['title'] ?? '' ) );
		update_post_meta( $post_id, 'team_hero_description', sanitize_textarea_field( $hero['description'] ?? '' ) );
	}
}
add_action( 'save_post', 'thecrisisacademy_save_team_metaboxes', 10, 2 );
