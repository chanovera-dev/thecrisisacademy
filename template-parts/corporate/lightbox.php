<?php
/**
 * Corporate Lightbox Component
 * Reusable modal window capable of presenting various interactive components,
 * starting with the Crisis Simulator plugin.
 *
 * @package TheCrisisAcademy
 * @subpackage Corporate
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="corporate-lightbox" id="corporate-lightbox" role="dialog" aria-modal="true" aria-hidden="true">
    <div class="corporate-lightbox-backdrop" data-close-lightbox tabindex="-1"></div>
    
    <div class="corporate-lightbox-dialog">
        <!-- Lightbox Toolbar -->
        <header class="corporate-lightbox-header">
            <div class="corporate-lightbox-header-left">
                <span class="lightbox-badge-status">
                    <span class="status-pulse-dot"></span>
                    <span class="lightbox-pane-title" id="corporate-lightbox-title">Simulador de Crisis</span>
                </span>
            </div>
            
            <div class="corporate-lightbox-header-right">
                <button type="button" class="corporate-lightbox-close-btn" data-close-lightbox aria-label="Cerrar modal">
                    <span class="close-label">ESC</span>
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
        </header>

        <!-- Lightbox Scrollable Body -->
        <div class="corporate-lightbox-body sdc-lightbox-scroll">
            <!-- Pane 1: Crisis Simulator -->
            <div class="corporate-lightbox-pane active" id="pane-crisis-simulator" data-pane="crisis-simulator" data-title="Simulador de Crisis">
                <?php
                $sim_output = '';
                if ( shortcode_exists( 'simulador_de_crisis' ) ) {
                    $sim_output = do_shortcode( '[simulador_de_crisis]' );
                } elseif ( function_exists( 'sdc_simulator_shortcode' ) ) {
                    $sim_output = sdc_simulator_shortcode();
                } else {
                    $sim_output = '<p class="lightbox-empty-notice">El plugin Simulador de Crisis no se encuentra activo.</p>';
                }

                // Normalize modal title to H2 to guarantee a single semantic H1 on the page
                $sim_output = str_replace( '<h1 class="sdc-title', '<h2 class="sdc-title', $sim_output );
                $sim_output = str_replace( '</h1>', '</h2>', $sim_output );

                echo $sim_output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                ?>
            </div>

            <!-- Future panes (e.g. video demos, contact forms, whitepapers) can be added here -->
            <?php
            /**
             * Allows template sections to register additional lightbox panes.
             * Each pane must use .corporate-lightbox-pane with data-pane and data-title.
             */
            do_action( 'tca_lightbox_panes' );
            ?>
        </div>
    </div>
</div>
