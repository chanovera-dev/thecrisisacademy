<?php
/**
 * Corporate Upcoming Events Section (#upcoming-events)
 * Powered by Custom Post Type 'event' with automatic past event expiration.
 * If no upcoming events are scheduled, this section is completely hidden.
 *
 * @package TheCrisisAcademy
 */

$today = current_time( 'Y-m-d' );

$events_query = new WP_Query( array(
	'post_type'      => 'event',
	'post_status'    => 'publish',
	'posts_per_page' => -1,
	'meta_key'       => '_event_date',
	'orderby'        => 'meta_value',
	'order'          => 'ASC',
	'meta_query'     => array(
		array(
			'key'     => '_event_date',
			'value'   => $today,
			'compare' => '>=',
			'type'    => 'DATE',
		),
	),
) );

// If no upcoming events are found, do not output the section at all
if ( ! $events_query->have_posts() ) {
	return;
}

$header_data = thecrisisacademy_get_upcoming_events_data();

// Spanish month names helper
$months_es = array(
	1  => 'enero',
	2  => 'febrero',
	3  => 'marzo',
	4  => 'abril',
	5  => 'mayo',
	6  => 'junio',
	7  => 'julio',
	8  => 'agosto',
	9  => 'septiembre',
	10 => 'octubre',
	11 => 'noviembre',
	12 => 'diciembre',
);
?>
<section id="upcoming-events" class="block">
    <div class="content">
        <div class="events-container">
            <header class="section-header">
                <span class="sub-heading pretext-reveal"><?php echo esc_html( $header_data['preheading'] ); ?></span>
                <h2 class="title-section title-reveal"><?php echo wp_kses( $header_data['title'], array( 'br' => array(), 'span' => array( 'class' => array() ), 'em' => array(), 'strong' => array() ) ); ?></h2>
            </header>
            <div class="upcoming-events__track-wrapper object-reveal" id="upcoming-events-track-wrapper">
                <div class="upcoming-events__track" id="upcoming-events-track">
                    <?php
                    while ( $events_query->have_posts() ) :
                        $events_query->the_post();
                        $event_id    = get_the_ID();
                        $event_date  = get_post_meta( $event_id, '_event_date', true );
                        $event_time  = get_post_meta( $event_id, '_event_time', true );
                        $event_loc   = get_post_meta( $event_id, '_event_location', true );
                        $event_feat  = get_post_meta( $event_id, '_event_featured', true );
                        $event_link  = get_post_meta( $event_id, '_event_link', true );

                        $card_is_featured = ( '1' === $event_feat );

                        // Date parts
                        $timestamp  = ! empty( $event_date ) ? strtotime( $event_date ) : current_time( 'timestamp' );
                        $day_num    = date( 'd', $timestamp );
                        $month_num  = (int) date( 'n', $timestamp );
                        $month_name = isset( $months_es[ $month_num ] ) ? $months_es[ $month_num ] : date( 'F', $timestamp );
                        $location   = ! empty( $event_loc ) ? $event_loc : '@Campus online - Zoom';
                    ?>
                        <div class="event-card<?php echo $card_is_featured ? ' event-card--featured' : ''; ?>">
                            <div class="event-card__date">
                                <span class="event-card__day"><?php echo esc_html( $day_num ); ?></span>
                                <span class="event-card__month"><?php echo esc_html( $month_name ); ?></span>
                            </div>
                            <div class="event-card__body">
                                <?php if ( ! empty( $event_link ) ) : ?>
                                    <p class="event-card__title">
                                        <a href="<?php echo esc_url( $event_link ); ?>" target="_blank" rel="noopener noreferrer" style="color:inherit; text-decoration:none;">
                                            <?php the_title(); ?>
                                        </a>
                                    </p>
                                <?php else : ?>
                                    <p class="event-card__title"><?php the_title(); ?></p>
                                <?php endif; ?>

                                <p class="event-card__time"><?php echo esc_html( $event_time ); ?></p>
                                <p class="event-card__location"><?php echo esc_html( $location ); ?></p>
                            </div>
                        </div>
                    <?php
                    endwhile;
                    wp_reset_postdata();
                    ?>
                </div>
                <div class="upcoming-events__scrollbar" id="upcoming-events-scrollbar" aria-hidden="true">
                    <div class="upcoming-events__scrollbar-thumb" id="upcoming-events-scrollbar-thumb"></div>
                </div>
            </div>
        </div>
    </div>
</section>