<?php
/**
 * Corporate Page: Call To Action (CTA) Section (#cta)
 * WhatsApp registration form and points slideshow.
 *
 * @package TheCrisisAcademy
 */

$data   = thecrisisacademy_get_cta_data();
$points = ! empty( $data['points'] ) ? $data['points'] : array();
?>
<section id="cta" class="block blue-background-00">
    <div class="content animated-card content-grid">
        <div class="cta-spotlight-layer" aria-hidden="true"></div>
        <div class="cta-glow-orb cta-glow-orb-1" aria-hidden="true"></div>
        <div class="cta-glow-orb cta-glow-orb-2" aria-hidden="true"></div>
        
        <!-- Left Column: Intro & 3D Cube Animation -->
        <div class="cta-intro">
            <div class="logo card-reveal">
                <div class="ring-wrap">
                    <div class="glow-ring"></div>
                    <div class="scene">
                        <div class="cube">
                            <div class="face front">
                                <div class="grid"></div>
                                <div class="ai-eye">
                                    <div class="pupil"></div>
                                    <span class="node node-1"></span>
                                    <span class="node node-2"></span>
                                    <span class="node node-3"></span>
                                    <span class="node node-4"></span>
                                </div>
                            </div>
                            <div class="face back">
                                <div class="grid"></div>
                            </div>
                            <div class="face left">
                                <div class="grid"></div>
                            </div>
                            <div class="face right">
                                <div class="grid"></div>
                            </div>
                            <div class="face top">
                                <div class="grid"></div>
                            </div>
                            <div class="face bottom">
                                <div class="grid"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <span class="sub-heading pretext-reveal"><?php echo esc_html( $data['preheading'] ); ?></span>
            <h2 class="title-section title-reveal"><?php echo wp_kses( $data['title'], array( 'br' => array(), 'span' => array( 'class' => array() ), 'em' => array(), 'strong' => array() ) ); ?></h2>
            <?php if ( ! empty( $data['description'] ) ) : ?>
                <p class="cta-description object-reveal"><?php echo esc_html( $data['description'] ); ?></p>
            <?php endif; ?>
        </div>

        <!-- Right Column: Points Slideshow & WhatsApp Form -->
        <div class="cta-action card-reveal">
            <?php if ( ! empty( $points ) ) : ?>
                <div class="points-slideshow">
                    <ul class="card-points">
                        <?php foreach ( $points as $i => $pt ) : ?>
                            <li class="<?php echo $i === 0 ? 'is-active' : ''; ?>">
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                                <span><?php echo esc_html( $pt ); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="points-nav" role="tablist" aria-label="Puntos clave">
                        <?php foreach ( $points as $i => $pt ) : ?>
                            <button type="button" class="point-dot<?php echo $i === 0 ? ' is-active' : ''; ?>" role="tab" aria-selected="<?php echo $i === 0 ? 'true' : 'false'; ?>" aria-label="<?php echo esc_attr( sprintf( 'Punto %d de %d', $i + 1, count( $points ) ) ); ?>"></button>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <form id="cta-whatsapp-form" class="cta-form" data-phone="<?php echo esc_attr( $data['whatsapp_phone'] ); ?>">
                <div class="cta-form-fields">
                    <div class="form-group">
                        <label for="wa_name"><?php echo esc_html( $data['form_name_label'] ); ?></label>
                        <input type="text" id="wa_name" required="" placeholder="Ej. Juan Pérez">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="wa_email"><?php echo esc_html( $data['form_email_label'] ); ?></label>
                            <input type="email" id="wa_email" required="" placeholder="juan@empresa.com">
                        </div>
                        <div class="form-group">
                            <label for="wa_phone"><?php echo esc_html( $data['form_phone_label'] ); ?></label>
                            <input type="tel" id="wa_phone" required="" placeholder="+52 ...">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="wa_interest"><?php echo esc_html( $data['form_msg_label'] ); ?></label>
                        <textarea id="wa_interest" rows="2" required="" placeholder="Ej. Gestión de crisis, vocería..."></textarea>
                    </div>
                    
                    <!-- Botón y microcopy -->
                    <div class="cta-action-area">
                        <button type="submit" class="btn primary cta-pulse-btn" id="cta-main-btn" style="border:none; cursor:pointer; width:auto;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right-circle" viewBox="0 0 16 16" aria-hidden="true"><path fill-rule="evenodd" d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8m15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0M4.5 7.5a.5.5 0 0 0 0 1h5.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5z"></path></svg>
                            <?php echo esc_html( $data['button_text'] ); ?>
                        </button>

                        <?php if ( ! empty( $data['microcopy_text'] ) ) : ?>
                            <p class="cta-micro-copy">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-shield-check" viewBox="0 0 16 16" aria-hidden="true"><path d="M5.338 1.59a61 61 0 0 0-2.837.856.48.48 0 0 0-.328.39c-.554 4.157.726 7.19 2.253 9.188a10.7 10.7 0 0 0 2.287 2.233c.346.244.652.42.893.533q.18.085.293.118a1 1 0 0 0 .101.025 1 1 0 0 0 .1-.025q.114-.034.294-.118c.24-.113.547-.29.893-.533a10.7 10.7 0 0 0 2.287-2.233c1.527-1.997 2.807-5.031 2.253-9.188a.48.48 0 0 0-.328-.39c-.651-.213-1.75-.56-2.837-.855C9.552 1.29 8.531 1.067 8 1.067c-.53 0-1.552.223-2.662.524zM5.072.56C6.157.265 7.31 0 8 0s1.843.265 2.928.56c1.11.3 2.229.655 2.887.87a1.54 1.54 0 0 1 1.044 1.262c.596 4.477-.787 7.795-2.465 9.99a11.8 11.8 0 0 1-2.517 2.453 7 7 0 0 1-1.048.625c-.28.132-.581.24-.829.24s-.548-.108-.829-.24a7 7 0 0 1-1.048-.625 11.8 11.8 0 0 1-2.517-2.453C1.928 10.487.545 7.169 1.141 2.692A1.54 1.54 0 0 1 2.185 1.43 63 63 0 0 1 5.072.56"></path><path d="M10.854 5.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 7.793l2.646-2.647a.5.5 0 0 1 .708 0"></path></svg>
                                <?php echo esc_html( $data['microcopy_text'] ); ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="cta-success-message" style="display: none; text-align: center; padding: 2rem 0;">
                    <div class="success-icon" style="color: var(--wp--preset--color--primary); margin-bottom: 1rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                    </div>
                    <h3 style="color: #ffffff; font-size: 1.5rem; margin-bottom: 0.5rem;"><?php echo esc_html( $data['success_title'] ); ?></h3>
                    <p style="color: rgba(255,255,255,0.7); font-size: 1rem; margin-bottom: 1.5rem;"><?php echo esc_html( $data['success_desc'] ); ?></p>
                    <button type="button" class="btn secondary" id="cta-reset-btn" style="background: rgba(255,255,255,0.1); color: #fff; border: 1px solid rgba(255,255,255,0.2); padding: 0.5rem 1rem; border-radius: 99px; cursor: pointer; transition: all 0.3s ease;">
                        <?php echo esc_html( $data['reset_btn_text'] ); ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>