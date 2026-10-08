<?php
/**
 * Corporate Template Part: Simulation Section
 *
 * Implements dynamic fields and repeater for .accordion-interactive-list.
 *
 * @package TheCrisisAcademy
 */

$sim_data = thecrisisacademy_get_simulation_data();
$items    = ! empty( $sim_data['items'] ) ? $sim_data['items'] : array();
?>
<section id="simulation" class="block blue-background-00">
    <div class="content content-grid">
        <div class="section-header center">
            <span class="sub-heading pretext-reveal" aria-label="<?= esc_attr( wp_strip_all_tags( $sim_data['preheading'] ) ); ?>"><?= esc_html( $sim_data['preheading'] ); ?></span>
            <h2 class="title-section title-reveal"><?= wp_kses( $sim_data['title'], array( 'br' => array(), 'span' => array( 'class' => array() ), 'strong' => array(), 'em' => array() ) ); ?></h2>
            <p class="simulation-intro-desc object-reveal">
                <?= esc_html( $sim_data['description'] ); ?>
            </p>
            <?php if ( ! empty( $sim_data['cta_text'] ) && ! empty( $sim_data['cta_url'] ) ) : ?>
                <div class="cta-wrapper object-reveal">
                    <a href="<?= esc_url( $sim_data['cta_url'] ); ?>" class="btn primary"<?php if ( ! empty( $sim_data['cta_lightbox'] ) ) : ?> data-open-lightbox="<?= esc_attr( $sim_data['cta_lightbox'] ); ?>"<?php endif; ?>>
                        <svg class="terminal-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="4 17 10 11 4 5"></polyline>
                            <line x1="12" y1="19" x2="20" y2="19"></line>
                        </svg>
                        <span><?= esc_html( $sim_data['cta_text'] ); ?></span>
                    </a>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="simulation-right">
            <div class="vulnerabilities-panel card-reveal">
                <div class="panel-header safari-toolbar">
                    <div class="safari-controls-left">
                        <div class="panel-traffic-lights">
                            <div class="panel-dot dot-red"></div>
                            <div class="panel-dot dot-yellow"></div>
                            <div class="panel-dot dot-green"></div>
                        </div>
                    </div>
                    <div class="safari-search-bar">
                        <svg class="safari-lock-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                        <span class="panel-title"><?= esc_html( $sim_data['panel_title'] ); ?></span>
                    </div>
                </div>
                <div class="accordion-interactive-list">
                    <?php foreach ( $items as $i => $item ) : 
                        $is_active = ( 0 === $i );
                    ?>
                        <div class="accordion-item <?= $is_active ? 'active' : ''; ?>">
                            <div class="sub-heading warning">
                                <?= thecrisisacademy_get_simulation_icon_svg( $item['icon'] ?? 'x' ); ?>
                            </div>
                            <div class="accordion-main">
                                <h3><?= esc_html( $item['title'] ); ?></h3>
                                <div class="accordion-expandable">
                                    <p><?= esc_html( $item['description'] ); ?></p>
                                </div>
                            </div>
                            <div class="accordion-arrow">
                                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>