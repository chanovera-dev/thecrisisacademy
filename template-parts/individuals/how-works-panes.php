<?php
/**
 * How Works - Lightbox Panes
 * Extended content for each solution in #how-works, rendered inside the
 * shared lightbox (#corporate-lightbox) and opened via [data-open-lightbox].
 *
 * @package TheCrisisAcademy
 * @subpackage Individuals
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$panes = array();

if ( function_exists( 'have_rows' ) && have_rows( 'how_works_panes' ) ) {
	while ( have_rows( 'how_works_panes' ) ) {
		the_row();
		$panes[] = array(
			'pane_id'         => get_sub_field( 'pane_id' ),
			'pane_title'      => get_sub_field( 'pane_title' ),
			'article_number'  => get_sub_field( 'article_number' ),
			'article_title'   => get_sub_field( 'article_title' ),
			'article_content' => get_sub_field( 'article_content' ),
		);
	}
} elseif ( function_exists( 'get_field' ) ) {
	$raw_panes = get_field( 'how_works_panes' );
	if ( ! empty( $raw_panes ) && is_array( $raw_panes ) ) {
		foreach ( $raw_panes as $row ) {
			$panes[] = array(
				'pane_id'         => $row['pane_id'] ?? ( $row['field_how_works_pane_id'] ?? '' ),
				'pane_title'      => $row['pane_title'] ?? ( $row['field_how_works_pane_title'] ?? '' ),
				'article_number'  => $row['article_number'] ?? ( $row['field_how_works_pane_art_num'] ?? '' ),
				'article_title'   => $row['article_title'] ?? ( $row['field_how_works_pane_art_title'] ?? '' ),
				'article_content' => $row['article_content'] ?? ( $row['field_how_works_pane_art_content'] ?? '' ),
			);
		}
	}
}

if ( empty( $panes ) && function_exists( 'thecrisisacademy_get_default_how_works_panes_rows' ) ) {
	$raw_defaults = thecrisisacademy_get_default_how_works_panes_rows();
	foreach ( $raw_defaults as $row ) {
		$panes[] = array(
			'pane_id'         => $row['pane_id'] ?? '',
			'pane_title'      => $row['pane_title'] ?? '',
			'article_number'  => $row['article_number'] ?? '',
			'article_title'   => $row['article_title'] ?? '',
			'article_content' => $row['article_content'] ?? '',
		);
	}
}

if ( empty( $panes ) ) {
	return;
}
?>

<?php foreach ( $panes as $pane ) : 
	$pane_id         = ! empty( $pane['pane_id'] ) ? sanitize_html_class( $pane['pane_id'] ) : '';
	$pane_title      = $pane['pane_title'] ?? '';
	$article_number  = $pane['article_number'] ?? '';
	$article_title   = $pane['article_title'] ?? '';
	$article_content = $pane['article_content'] ?? '';
?>
<!-- Pane: <?php echo esc_html( $pane_title ); ?> -->
<div class="corporate-lightbox-pane" id="pane-<?php echo esc_attr( $pane_id ); ?>" data-pane="<?php echo esc_attr( $pane_id ); ?>" data-title="<?php echo esc_attr( $pane_title ); ?>">
	<article class="lightbox-article">
		<header class="lightbox-article-header">
			<?php if ( ! empty( $article_number ) ) : ?>
				<span class="sub-heading lightbox-article-number"><?php echo esc_html( $article_number ); ?></span>
			<?php endif; ?>
			<?php if ( ! empty( $article_title ) ) : ?>
				<h2 class="lightbox-article-title"><?php echo esc_html( $article_title ); ?></h2>
			<?php endif; ?>
		</header>
		<div class="lightbox-article-body">
			<?php echo wp_kses_post( $article_content ); ?>
		</div>
	</article>
</div>
<?php endforeach; ?>
