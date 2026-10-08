<?php
/**
 * Template part for Hero section in Individuals template
 *
 * @package TheCrisisAcademy
 */
// Retrieve ACF field values with safe fallback to defaults
$preheading  = function_exists( 'get_field' ) ? get_field( 'hero_preheading' ) : null;
$title       = function_exists( 'get_field' ) ? get_field( 'hero_title' ) : null;
$description = function_exists( 'get_field' ) ? get_field( 'hero_description' ) : null;
$tags_field  = function_exists( 'get_field' ) ? get_field( 'hero_canvas_tags' ) : null;

// Fallback defaults
$default_preheading  = 'Especialización en Comunicación para Manejo de Crisis';
$default_title       = 'Domina la gestión de crisis en la era de la IA y protege lo que más importa: tu reputación';
$default_description = 'Conoce cómo manejar el Framework de Respuesta Inmediata ante Crisis y Escándalos. El mismo sistema que usan empresas internacionales';

if ( empty( $preheading ) ) {
	$preheading = $default_preheading;
}

if ( empty( $title ) ) {
	$title = $default_title;
}

if ( empty( $description ) ) {
	$description = $default_description;
}

// Process canvas tags: from ACF string or existing $hero_data array
if ( ! empty( $tags_field ) ) {
	$canvas_tags = array_filter( array_map( 'trim', explode( ',', $tags_field ) ) );
} elseif ( ! empty( $hero_data['canvas_tags'] ) && is_array( $hero_data['canvas_tags'] ) ) {
	$canvas_tags = $hero_data['canvas_tags'];
} else {
	$canvas_tags = array();
}
?>
<section id="hero" class="block white-background-01">
    <canvas id="hero-canvas" class="hero-canvas" data-tags="<?php echo esc_attr( implode( ',', $canvas_tags ) ); ?>"></canvas>
    <div class="hero-glow"></div>
    <div class="content">
        <div class="text">
            <span class="sub-heading warning pretext-reveal"><?php echo esc_html( $preheading ); ?></span>
            <h1 class="page-title"><?php echo wp_kses_post( $title ); ?></h1>
            <p><?php echo wp_kses_post( $description ); ?></p>
        </div>
    </div>
</section>