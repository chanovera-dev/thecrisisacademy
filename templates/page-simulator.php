<?php
/**
 * Template Name: Simulador de Crisis Full Page
 *
 * Crisis Simulator standalone template that inherits the Corporate Lightbox aesthetic.
 * Adheres to Essentialis / Stories theme HTML standards:
 * - <section class="block">
 * - <div class="content">
 *
 * All comments and DocBlocks are in English.
 *
 * @package TheCrisisAcademy
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header(); ?>

<section class="block sdc-page-section blue-background-00" id="sdc-page-section">
	<div class="content sdc-page-content">
		<?php
		$sim_output = '';
		if ( shortcode_exists( 'simulador_de_crisis' ) ) {
			$sim_output = do_shortcode( '[simulador_de_crisis]' );
		} elseif ( function_exists( 'sdc_simulator_shortcode' ) ) {
			$sim_output = sdc_simulator_shortcode();
		} else {
			$sim_output = '<p class="lightbox-empty-notice">' . esc_html__( 'El plugin Simulador de Crisis no se encuentra activo.', 'thecrisisacademy' ) . '</p>';
		}

		// Normalize modal title to H2 to guarantee a single semantic H1 on the page if needed
		$sim_output = str_replace( '<h1 class="sdc-title', '<h2 class="sdc-title', $sim_output );
		$sim_output = str_replace( '</h1>', '</h2>', $sim_output );

		echo $sim_output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		?>
	</div>
</section>

<?php
get_footer();
