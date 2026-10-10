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

$how_works_data = function_exists( 'thecrisisacademy_get_individuals_how_works_data' )
	? thecrisisacademy_get_individuals_how_works_data()
	: array();

$panes = ! empty( $how_works_data['panes'] )
	? $how_works_data['panes']
	: ( function_exists( 'thecrisisacademy_get_default_how_works_panes_rows' ) ? thecrisisacademy_get_default_how_works_panes_rows() : array() );

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
	$alt_pane_id     = ( 'how-works-pi6m' === $pane_id ) ? 'how-works-fec' : ( ( 'how-works-fec' === $pane_id ) ? 'how-works-pi6m' : '' );
?>
<!-- Pane: <?php echo esc_html( $pane_title ); ?> -->
<div class="corporate-lightbox-pane" id="pane-<?php echo esc_attr( $pane_id ); ?>" data-pane="<?php echo esc_attr( $pane_id ); ?>"<?php echo ! empty( $alt_pane_id ) ? ' data-alt-pane="' . esc_attr( $alt_pane_id ) . '"' : ''; ?> data-title="<?php echo esc_attr( $pane_title ); ?>">
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
