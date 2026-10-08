<?php
/**
 * Template part for the Corporate News section with dynamic category filtering.
 *
 * @package TheCrisisAcademy
 */

$news_data = function_exists( 'thecrisisacademy_get_news_data' ) ? thecrisisacademy_get_news_data() : array(
	'preheading'       => __( 'Centro de Inteligencia', 'thecrisisacademy' ),
	'title'            => __( 'Actualidad Global y Análisis', 'thecrisisacademy' ),
	'description'      => __( 'Mantente informado de los últimos eventos, análisis e impacto de crisis y escándalos.', 'thecrisisacademy' ),
	'posts_per_page'   => 8,
	'orderby'          => 'date',
	'order'            => 'DESC',
	'show_filters'     => '1',
	'filter_all_label' => __( 'Todos', 'thecrisisacademy' ),
	'show_canvas'      => '1',
	'show_button'      => '1',
	'button_text'      => __( 'Ver todas las noticias', 'thecrisisacademy' ),
	'button_url'       => '',
	'button_target'    => '0',
);

// Get categories that are actually assigned to published 'news' posts
$news_post_ids = get_posts( array(
	'post_type'      => 'news',
	'post_status'    => 'publish',
	'posts_per_page' => -1,
	'fields'         => 'ids',
) );

$news_categories = ! empty( $news_post_ids ) ? get_terms( array(
	'taxonomy'   => 'category',
	'hide_empty' => true,
	'object_ids' => $news_post_ids,
) ) : array();

// Initial query for news posts
$initial_query = new WP_Query( array(
	'post_type'      => 'news',
	'posts_per_page' => ! empty( $news_data['posts_per_page'] ) ? (int) $news_data['posts_per_page'] : 8,
	'orderby'        => ! empty( $news_data['orderby'] ) ? $news_data['orderby'] : 'date',
	'order'          => ! empty( $news_data['order'] ) ? $news_data['order'] : 'DESC',
	'post_status'    => 'publish',
	'no_found_rows'  => true,
) );

$allowed_title_tags = array(
	'br'     => array(),
	'span'   => array( 'class' => array() ),
	'em'     => array(),
	'strong' => array(),
);
?>

<section id="news" class="block posts--body">
	<?php if ( ! empty( $news_data['show_canvas'] ) ) : ?>
		<canvas id="news-radio-waves-canvas" class="news-radio-waves-canvas" aria-hidden="true"></canvas>
	<?php endif; ?>
	<div class="content">       
		<header class="section-header center">
			<span class="sub-heading pretext-reveal"><?php echo esc_html( $news_data['preheading'] ); ?></span>
			<h2 class="title-section title-reveal"><?php echo wp_kses( $news_data['title'], $allowed_title_tags ); ?></h2>
			<?php if ( ! empty( $news_data['description'] ) ) : ?>
				<p class="description-section object-reveal"><?php echo esc_html( $news_data['description'] ); ?></p>
			<?php endif; ?>
		</header>

		<?php if ( ! empty( $news_data['show_filters'] ) && ! empty( $news_categories ) && ! is_wp_error( $news_categories ) ) : ?>
			<div class="news-filters object-reveal" role="tablist" aria-label="<?php esc_attr_e( 'Filtrar noticias por categoría', 'thecrisisacademy' ); ?>">
				<button type="button" class="news-filter-btn sub-heading active" data-category="all" role="tab" aria-selected="true">
					<?php echo esc_html( ! empty( $news_data['filter_all_label'] ) ? $news_data['filter_all_label'] : __( 'Todos', 'thecrisisacademy' ) ); ?>
				</button>
				<?php foreach ( $news_categories as $cat ) : ?>
					<button type="button" class="news-filter-btn sub-heading" data-category="<?php echo esc_attr( $cat->slug ); ?>" role="tab" aria-selected="false">
						<?php echo esc_html( $cat->name ); ?>
					</button>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<div id="news-results" class="news-results-container card-reveal">
			<div class="posts-grid">
				<?php
				if ( $initial_query->have_posts() ) {
					while ( $initial_query->have_posts() ) {
						$initial_query->the_post();
						stories_loop_template_part( get_post_format() );
					}
					wp_reset_postdata();
				} else {
					echo '<p class="no-news-found">' . esc_html__( 'No se encontraron noticias.', 'thecrisisacademy' ) . '</p>';
				}
				?>
			</div>
		</div>

		<?php if ( ! empty( $news_data['show_button'] ) ) :
			$archive_link = get_post_type_archive_link( 'news' );
			$btn_url      = ! empty( $news_data['button_url'] ) ? $news_data['button_url'] : $archive_link;
			$target_attr  = ! empty( $news_data['button_target'] ) ? ' target="_blank" rel="noopener noreferrer"' : '';
		?>
			<div class="news-footer-action center object-reveal">
				<a href="<?php echo esc_url( $btn_url ); ?>" class="btn primary news-archive-btn"<?php echo $target_attr; ?>>
					<span><?php echo esc_html( ! empty( $news_data['button_text'] ) ? $news_data['button_text'] : __( 'Ver todas las noticias', 'thecrisisacademy' ) ); ?></span>
					<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
				</a>
			</div>
		<?php endif; ?>
	</div>
</section>