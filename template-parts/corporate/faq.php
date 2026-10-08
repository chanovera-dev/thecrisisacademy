<?php
/**
 * Template part for Corporate FAQ Section (#faq-section)
 * Powered by Custom Post Type 'faq' and corporate metabox fields.
 *
 * @package TheCrisisAcademy
 */

$faq_data = function_exists( 'thecrisisacademy_get_faq_data' ) ? thecrisisacademy_get_faq_data() : array(
	'preheading'     => 'FAQ',
	'title'          => 'Preguntas más frecuentes',
	'cta_title'      => 'Agenda una llamada de 15 min',
	'cta_desc'       => 'Si tienes dudas, agenda una videollamada gratuita de 15 minutos antes de suscribirte a un plan.',
	'cta_btn_text'   => 'Reservar Llamada Gratuita',
	'cta_btn_url'    => '#cta',
	'cta_btn_target' => '0',
	'posts_per_page' => -1,
	'orderby'        => 'menu_order',
	'order'          => 'ASC',
	'first_open'     => '1',
);

$faq_query = new WP_Query( array(
	'post_type'      => 'faq',
	'post_status'    => 'publish',
	'posts_per_page' => ! empty( $faq_data['posts_per_page'] ) ? (int) $faq_data['posts_per_page'] : -1,
	'orderby'        => ! empty( $faq_data['orderby'] ) ? $faq_data['orderby'] : 'menu_order',
	'order'          => ! empty( $faq_data['order'] ) ? $faq_data['order'] : 'ASC',
	'no_found_rows'  => true,
) );

$allowed_title_tags = array(
	'br'     => array(),
	'span'   => array( 'class' => array() ),
	'em'     => array(),
	'strong' => array(),
);

$cta_target_attr = ! empty( $faq_data['cta_btn_target'] ) ? ' target="_blank" rel="noopener noreferrer"' : '';
?>
<section id="faq-section" class="block white-background-04">
    <div class="content">
        <div class="accordion-interactive-list">    
            <div class="animated-card faq-cta-card card-reveal">
                <div class="expanding-wave"></div>
                <div class="glow-orb glow-orb-1"></div>
                <div class="glow-orb glow-orb-2"></div>
                <div class="cta-spotlight-layer" aria-hidden="true"></div>
                <div class="section-header center">
                    <span class="sub-heading pretext-reveal" aria-label="<?php echo esc_attr( $faq_data['preheading'] ); ?>"><?php echo esc_html( $faq_data['preheading'] ); ?></span>
                    <h2 class="title-section faq-main-title title-reveal"><?php echo wp_kses( $faq_data['title'], $allowed_title_tags ); ?></h2>
                </div>
                <h3 class="faq-cta-title object-reveal"><?php echo esc_html( $faq_data['cta_title'] ); ?></h3>
                <p class="faq-cta-desc object-reveal"><?php echo esc_html( $faq_data['cta_desc'] ); ?></p>
                <a href="<?php echo esc_url( $faq_data['cta_btn_url'] ); ?>" class="faq-cta-button btn primary object-reveal"<?php echo $cta_target_attr; ?>>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-calendar2-week" viewBox="0 0 16 16"><path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M2 2a1 1 0 0 0-1 1v11a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1z"></path><path d="M2.5 4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H3a.5.5 0 0 1-.5-.5zM11 7.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm-3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm-5 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5z"></path></svg>
                    <?php echo esc_html( $faq_data['cta_btn_text'] ); ?>
                </a>
            </div>
            
            <?php
            if ( $faq_query->have_posts() ) :
                $item_index = 0;
                while ( $faq_query->have_posts() ) :
                    $faq_query->the_post();
                    $is_active = ( 0 === $item_index && ! empty( $faq_data['first_open'] ) );
                    ?>
                    <div class="accordion-item<?php echo $is_active ? ' active' : ''; ?> card-reveal">
                        <div class="accordion-main">
                            <h3><?php the_title(); ?></h3>
                            <div class="accordion-expandable">
                                <?php the_content(); ?>
                            </div>
                        </div>
                        <div class="accordion-arrow">
                            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </div>
                    </div>
                    <?php
                    $item_index++;
                endwhile;
                wp_reset_postdata();
            elseif ( function_exists( 'thecrisisacademy_get_initial_faq_data' ) ) :
                $initial_faqs = thecrisisacademy_get_initial_faq_data();
                foreach ( $initial_faqs as $i => $faq_item ) :
                    $is_active = ( 0 === $i && ! empty( $faq_data['first_open'] ) );
                    ?>
                    <div class="accordion-item<?php echo $is_active ? ' active' : ''; ?> card-reveal">
                        <div class="accordion-main">
                            <h3><?php echo esc_html( $faq_item['question'] ); ?></h3>
                            <div class="accordion-expandable">
                                <p><?php echo esc_html( $faq_item['answer'] ); ?></p>
                            </div>
                        </div>
                        <div class="accordion-arrow">
                            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </div>
                    </div>
                    <?php
                endforeach;
            endif;
            ?>
        </div>
    </div>
</section>