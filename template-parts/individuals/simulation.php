<?php
/**
 * Individuals Template Part: Crisis Simulator Showcase
 *
 * Interactive showcase of the simulator stages. Tabs on the left drive a
 * dark "console" preview on the right (auto-advance with progress bars).
 * The CTA opens the simulator inside the shared lightbox (pane: crisis-simulator)
 * and falls back to the simulator page when JS is unavailable.
 *
 * @package TheCrisisAcademy
 * @subpackage Individuals
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$preheading     = function_exists( 'get_field' ) ? get_field( 'simulation_preheading' ) : '';
$title          = function_exists( 'get_field' ) ? get_field( 'simulation_title' ) : '';
$intro          = function_exists( 'get_field' ) ? get_field( 'simulation_intro' ) : '';
$console_status = function_exists( 'get_field' ) ? get_field( 'simulation_console_status' ) : '';
$sim_cta_url    = function_exists( 'get_field' ) ? get_field( 'simulation_cta_url' ) : '';
$sim_cta_label  = function_exists( 'get_field' ) ? get_field( 'simulation_cta_label' ) : '';
$sim_cta_lb     = function_exists( 'get_field' ) ? get_field( 'simulation_cta_lightbox' ) : '';

if ( empty( $preheading ) ) {
	$preheading = 'Simulador de crisis';
}
if ( empty( $title ) ) {
	$title = 'Experimenta la presión en tiempo real y descubre si tu equipo está preparado';
}
if ( empty( $intro ) ) {
	$intro = 'Tres etapas, un mismo reloj. Recorre el ciclo completo de una crisis y mide cómo responde tu equipo cuando cada minuto cuenta.';
}
if ( empty( $console_status ) ) {
	$console_status = 'Simulación en vivo';
}
if ( empty( $sim_cta_url ) ) {
	$sim_cta_url = home_url( '/simulador-de-crisis/' );
}
if ( empty( $sim_cta_label ) ) {
	$sim_cta_label = 'Simular crisis';
}
if ( empty( $sim_cta_lb ) ) {
	$sim_cta_lb = 'crisis-simulator';
}

// Verify if the Crisis Simulator plugin exists and is activated
$sim_plugin_rel_path = 'crisis-simulator/simulador-de-crisis.php';
$sim_plugin_abs_path = defined( 'WP_PLUGIN_DIR' ) ? WP_PLUGIN_DIR . '/' . $sim_plugin_rel_path : '';

if ( ! function_exists( 'is_plugin_active' ) && defined( 'ABSPATH' ) && file_exists( ABSPATH . 'wp-admin/includes/plugin.php' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

$is_simulator_active = ( ! empty( $sim_plugin_abs_path ) && file_exists( $sim_plugin_abs_path ) ) && (
	( function_exists( 'is_plugin_active' ) && is_plugin_active( $sim_plugin_rel_path ) )
	|| shortcode_exists( 'simulador_de_crisis' )
	|| function_exists( 'sdc_simulator_shortcode' )
	|| in_array( $sim_plugin_rel_path, (array) apply_filters( 'active_plugins', get_option( 'active_plugins', array() ) ), true )
);

$sim_stages = array();

if ( function_exists( 'have_rows' ) && have_rows( 'simulation_stages' ) ) {
	while ( have_rows( 'simulation_stages' ) ) {
		the_row();
		$sim_stages[] = array(
			'id'        => get_sub_field( 'id' ),
			'label'     => get_sub_field( 'label' ),
			'title'     => get_sub_field( 'title' ),
			'desc'      => get_sub_field( 'desc' ),
			'image'     => get_sub_field( 'image' ),
			'alt'       => get_sub_field( 'alt' ),
			'kpi'       => get_sub_field( 'kpi' ),
			'kpi_label' => get_sub_field( 'kpi_label' ),
		);
	}
} elseif ( function_exists( 'get_field' ) ) {
	$raw_stages = get_field( 'simulation_stages' );
	if ( ! empty( $raw_stages ) && is_array( $raw_stages ) ) {
		foreach ( $raw_stages as $row ) {
			$sim_stages[] = array(
				'id'        => $row['id'] ?? ( $row['field_sim_stage_id'] ?? '' ),
				'label'     => $row['label'] ?? ( $row['field_sim_stage_label'] ?? '' ),
				'title'     => $row['title'] ?? ( $row['field_sim_stage_title'] ?? '' ),
				'desc'      => $row['desc'] ?? ( $row['field_sim_stage_desc'] ?? '' ),
				'image'     => $row['image'] ?? ( $row['field_sim_stage_image'] ?? '' ),
				'alt'       => $row['alt'] ?? ( $row['field_sim_stage_alt'] ?? '' ),
				'kpi'       => $row['kpi'] ?? ( $row['field_sim_stage_kpi'] ?? '' ),
				'kpi_label' => $row['kpi_label'] ?? ( $row['field_sim_stage_kpi_label'] ?? '' ),
			);
		}
	}
}

if ( empty( $sim_stages ) && function_exists( 'thecrisisacademy_get_default_simulation_stages_rows' ) ) {
	$raw_defaults = thecrisisacademy_get_default_simulation_stages_rows();
	foreach ( $raw_defaults as $row ) {
		$sim_stages[] = array(
			'id'        => $row['id'] ?? '',
			'label'     => $row['label'] ?? '',
			'title'     => $row['title'] ?? '',
			'desc'      => $row['desc'] ?? '',
			'image'     => $row['image'] ?? '',
			'alt'       => $row['alt'] ?? '',
			'kpi'       => $row['kpi'] ?? '',
			'kpi_label' => $row['kpi_label'] ?? '',
		);
	}
}
?>
<section id="crisis-simulator" class="block whiteprint-background">
	<div class="content">
		<header class="section-header center">
			<span class="sub-heading pretext-reveal" aria-label="<?php echo esc_attr( $preheading ); ?>"><?php echo esc_html( $preheading ); ?></span>
			<h2 class="title-section title-reveal" aria-label="<?php echo esc_attr( wp_strip_all_tags( $title ) ); ?>"><?php echo wp_kses_post( $title ); ?></h2>
			<p class="simulator-intro object-reveal"><?php echo esc_html( $intro ); ?></p>
		</header>

		<div class="simulator-showcase content-grid" data-simulator-showcase>
			<!-- Stages (tabs) -->
			<div class="simulator-stages card-reveal">
				<div class="simulator-stages-list" role="tablist" aria-label="<?php esc_attr_e( 'Etapas del simulador de crisis', 'thecrisisacademy' ); ?>">
					<?php foreach ( $sim_stages as $i => $stage ) :
						$is_active = ( 0 === $i );
						$stage_id  = ! empty( $stage['id'] ) ? sanitize_html_class( $stage['id'] ) : 'stage-' . ( $i + 1 );
					?>
						<button
							type="button"
							role="tab"
							id="sim-tab-<?php echo esc_attr( $stage_id ); ?>"
							class="simulator-stage<?php echo $is_active ? ' is-active' : ''; ?>"
							aria-selected="<?php echo $is_active ? 'true' : 'false'; ?>"
							aria-controls="sim-panel-<?php echo esc_attr( $stage_id ); ?>"
							tabindex="<?php echo $is_active ? '0' : '-1'; ?>"
							data-stage-index="<?php echo (int) $i; ?>"
							data-kpi="<?php echo esc_attr( $stage['kpi'] ?? '' ); ?>"
							data-kpi-label="<?php echo esc_attr( $stage['kpi_label'] ?? '' ); ?>"
						>
							<span class="simulator-stage-text">
								<span class="sub-heading simulator-stage-code"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span>
								<span class="simulator-stage-label"><?php echo esc_html( $stage['label'] ?? '' ); ?></span>
							</span>
							<span class="simulator-stage-desc">
								<span class="simulator-stage-title"><?php echo esc_html( $stage['title'] ?? '' ); ?></span>
								<p><?php echo esc_html( $stage['desc'] ?? '' ); ?></p>
							</span>
							<span class="simulator-stage-progress" aria-hidden="true"><span></span></span>
						</button>
					<?php endforeach; ?>
				</div>
			</div>

			<!-- Console preview -->
			<div class="simulator-console card-reveal">
				<div class="simulator-console-bar">
					<span class="simulator-console-dots" aria-hidden="true"><i></i><i></i><i></i></span>
					<span class="simulator-console-status">
						<span class="simulator-console-pulse" aria-hidden="true"></span>
						<?php echo esc_html( $console_status ); ?>
					</span>
					<span class="simulator-console-clock" data-sim-clock aria-hidden="true">T+00:00:00</span>
				</div>

				<div class="simulator-console-screen">
					<?php if ( $is_simulator_active ) : ?>
						<a href="<?php echo esc_url( $sim_cta_url ); ?>" class="btn primary simulator-cta"<?php if ( ! empty( $sim_cta_lb ) ) : ?> data-open-lightbox="<?php echo esc_attr( $sim_cta_lb ); ?>"<?php endif; ?>>
							<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
								<polyline points="4 17 10 11 4 5"></polyline>
								<line x1="12" y1="19" x2="20" y2="19"></line>
							</svg>
							<span><?php echo esc_html( $sim_cta_label ); ?></span>
						</a>
					<?php endif; ?>
					<?php foreach ( $sim_stages as $i => $stage ) :
						$is_active = ( 0 === $i );
						$stage_id  = ! empty( $stage['id'] ) ? sanitize_html_class( $stage['id'] ) : 'stage-' . ( $i + 1 );

						$img_src = '';
						$img_alt = ! empty( $stage['alt'] ) ? $stage['alt'] : ( ! empty( $stage['title'] ) ? $stage['title'] : '' );

						if ( ! empty( $stage['image'] ) ) {
							if ( is_array( $stage['image'] ) ) {
								$img_src = $stage['image']['url'] ?? '';
								if ( empty( $img_alt ) && ! empty( $stage['image']['alt'] ) ) {
									$img_alt = $stage['image']['alt'];
								}
							} elseif ( is_numeric( $stage['image'] ) ) {
								$img_src = wp_get_attachment_image_url( $stage['image'], 'full' );
								if ( empty( $img_alt ) ) {
									$img_alt = get_post_meta( $stage['image'], '_wp_attachment_image_alt', true );
								}
							} elseif ( is_string( $stage['image'] ) ) {
								$img_src = $stage['image'];
							}
						}
					?>
						<figure
							role="tabpanel"
							id="sim-panel-<?php echo esc_attr( $stage_id ); ?>"
							class="simulator-screen<?php echo $is_active ? ' is-active' : ''; ?>"
							aria-labelledby="sim-tab-<?php echo esc_attr( $stage_id ); ?>"
							aria-hidden="<?php echo $is_active ? 'false' : 'true'; ?>"
						>
							<?php if ( ! empty( $img_src ) ) : ?>
								<img src="<?php echo esc_url( $img_src ); ?>" width="1000" height="429" alt="<?php echo esc_attr( $img_alt ); ?>" loading="lazy" decoding="async">
							<?php endif; ?>
						</figure>
					<?php endforeach; ?>

					<span class="simulator-scanline" aria-hidden="true"></span>
					<span class="simulator-corners" aria-hidden="true"><i></i><i></i><i></i><i></i></span>

					<?php
					$first_kpi       = ! empty( $sim_stages[0]['kpi'] ) ? $sim_stages[0]['kpi'] : '';
					$first_kpi_label = ! empty( $sim_stages[0]['kpi_label'] ) ? $sim_stages[0]['kpi_label'] : '';
					?>
					<div class="simulator-hud" aria-live="polite">
						<strong class="simulator-hud-kpi" data-sim-kpi><?php echo esc_html( $first_kpi ); ?></strong>
						<span class="simulator-hud-label" data-sim-kpi-label><?php echo esc_html( $first_kpi_label ); ?></span>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>