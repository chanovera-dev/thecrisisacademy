<?php
/**
 * Corporate Thought / Target Audience & Manifesto Section (#thought)
 * Presents organizational profiles, target industries, and program core philosophy.
 *
 * @package TheCrisisAcademy
 */

$data = thecrisisacademy_get_thought_data();
?>
<section id="thought" class="block white-background-01">
    <div class="content thought-container">
        <!-- Section Header -->
        <header class="section-header center">
            <span class="sub-heading pretext-reveal"><?php echo esc_html( $data['preheading'] ); ?></span>
            <h2 class="title-section title-reveal"><?php echo wp_kses( $data['title'], array( 'br' => array(), 'span' => array( 'class' => array() ), 'em' => array(), 'strong' => array() ) ); ?></h2>
            <?php if ( ! empty( $data['description'] ) ) : ?>
                <p class="description-section object-reveal"><?php echo esc_html( $data['description'] ); ?></p>
            <?php endif; ?>
        </header>

        <div class="thought-flex">
            <!-- Column 1: Perfiles -->
            <div class="thought-card thought-col col-profiles card-reveal">
                <div class="thought-card-header">
                    <div class="big-badge sub-heading" aria-hidden="true">
                        <?php echo thecrisisacademy_get_thought_icon_svg( $data['profiles_icon'], 22, 22 ); ?>
                    </div>
                    <div>
                        <span class="thought-card-tag"><?php echo esc_html( $data['profiles_tag'] ); ?></span>
                        <h3 class="col-title"><?php echo esc_html( $data['profiles_title'] ); ?></h3>
                    </div>
                </div>
                <?php if ( ! empty( $data['profiles_items'] ) ) : ?>
                    <ul class="profiles-list">
                        <?php foreach ( $data['profiles_items'] as $profile_item ) : ?>
                            <li>
                                <span class="icon-bullet" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                </span>
                                <span class="text"><?php echo esc_html( $profile_item ); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <!-- Column 2: Sectores Clave -->
            <div class="thought-card thought-col col-sectors card-reveal">
                <div class="thought-card-header">
                    <div class="big-badge sub-heading" aria-hidden="true">
                        <?php echo thecrisisacademy_get_thought_icon_svg( $data['sectors_icon'], 22, 22 ); ?>
                    </div>
                    <div>
                        <span class="thought-card-tag"><?php echo esc_html( $data['sectors_tag'] ); ?></span>
                        <h3 class="col-title"><?php echo esc_html( $data['sectors_title'] ); ?></h3>
                    </div>
                </div>
                <?php if ( ! empty( $data['sectors_items'] ) ) : ?>
                    <div class="sectors-list">
                        <?php foreach ( $data['sectors_items'] as $s_idx => $sector_name ) : ?>
                            <div class="sector-item">
                                <span class="sector-num"><?php echo sprintf( '%02d', $s_idx + 1 ); ?></span>
                                <span class="sector-name"><?php echo esc_html( $sector_name ); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Column 3: Manifiesto -->
            <div class="thought-card thought-col col-manifesto card-reveal">
                <div class="thought-card-header">
                    <div class="big-badge sub-heading" aria-hidden="true">
                        <?php echo thecrisisacademy_get_thought_icon_svg( $data['manifesto_icon'], 22, 22 ); ?>
                    </div>
                    <div>
                        <span class="thought-card-tag"><?php echo esc_html( $data['manifesto_tag'] ); ?></span>
                        <h3 class="col-title"><?php echo esc_html( $data['manifesto_title'] ); ?></h3>
                    </div>
                </div>
                <div class="card-quote manifesto-editorial-box">
                    <div class="aside-decor" aria-hidden="true">
                        <div class="aside-holes">
                            <span class="aside-hole"></span>
                            <span class="aside-hole"></span>
                            <span class="aside-hole"></span>
                            <span class="aside-hole"></span>
                            <span class="aside-hole"></span>
                            <span class="aside-hole"></span>
                            <span class="aside-hole"></span>
                            <span class="aside-hole"></span>
                        </div>
                    </div>
                    <svg class="quote-icon" viewBox="0 0 24 24" width="22" height="22" fill="currentColor">
                        <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"></path>
                    </svg>
                    <?php if ( ! empty( $data['manifesto_lead'] ) ) : ?>
                        <p><?php echo esc_html( $data['manifesto_lead'] ); ?></p>
                    <?php endif; ?>
                    <?php if ( ! empty( $data['manifesto_statement'] ) ) : ?>
                        <p><?php echo wp_kses( $data['manifesto_statement'], array( 'strong' => array(), 'em' => array(), 'br' => array(), 'span' => array( 'class' => array() ) ) ); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>