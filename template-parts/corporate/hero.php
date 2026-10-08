<?php
/**
 * Template part for Hero section in Corporate template
 *
 * @package TheCrisisAcademy
 */

$hero_data = function_exists( 'thecrisisacademy_get_hero_data' )
	? thecrisisacademy_get_hero_data()
	: array();

$preheading         = $hero_data['preheading'] ?? 'Comunicación y Manejo de Crisis • Programa In-Company';
$title              = $hero_data['title'] ?? 'Tu empresa tiene 60 minutos. ¿Está preparada para responder?';
$points             = $hero_data['points'] ?? array();
$data_block_content = $hero_data['data_block_content'] ?? '';
$source_label       = $hero_data['source_label'] ?? 'PwC + Oxford Metrica';
$source_url         = $hero_data['source_url'] ?? 'https://nationalpreparednesscommission.uk/2023/04/how-to-unlock-value-through-resilience-and-evolve-for-disruption/';
$canvas_tags        = $hero_data['canvas_tags'] ?? array();

// Allowed tags for title (supports inline formatting)
$allowed_title_tags = array(
	'br'     => array(),
	'span'   => array( 'class' => array() ),
	'em'     => array(),
	'strong' => array(),
);
?>
<section id="hero" class="block white-background-01">
    <canvas id="hero-canvas" class="hero-canvas" data-tags="<?php echo esc_attr( implode( ',', $canvas_tags ) ); ?>"></canvas>
    <div class="hero-glow"></div>
    <div class="content content-grid">
        <div class="text">
            <span class="sub-heading warning pretext-reveal"><?php echo esc_html( $preheading ); ?></span>
            <h1 class="page-title"><?php echo wp_kses( $title, $allowed_title_tags ); ?></h1>
            
            <?php if ( ! empty( $points ) ) : 
                $points_count = count( $points );
            ?>
                <div class="points-slideshow object-reveal">
                    <ul class="card-points">
                        <?php foreach ( $points as $index => $point ) : 
                            $text = is_array( $point ) ? ( $point['text'] ?? '' ) : $point;
                            if ( '' === trim( $text ) ) continue;
                        ?>
                            <li class="<?php echo 0 === $index ? 'is-active' : ''; ?>">
                                <span><?php echo wp_kses_post( $text ); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php if ( $points_count > 1 ) : ?>
                        <div class="points-nav" role="tablist" aria-label="<?php esc_attr_e( 'Puntos clave', 'thecrisisacademy' ); ?>">
                            <?php for ( $i = 0; $i < $points_count; $i++ ) : ?>
                                <button type="button" class="point-dot <?php echo 0 === $i ? 'is-active' : ''; ?>" role="tab" aria-selected="<?php echo 0 === $i ? 'true' : 'false'; ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Punto %d de %d', 'thecrisisacademy' ), $i + 1, $points_count ) ); ?>"></button>
                            <?php endfor; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="visual">
            <div class="data-block object-reveal">
                <div class="block-glow" aria-hidden="true"></div>
                
                <?php echo wp_kses_post( $data_block_content ); ?>
                
                <canvas id="down-chart-canvas" aria-label="<?php esc_attr_e( 'Gráfico de caída del valor de mercado durante una crisis', 'thecrisisacademy' ); ?>" width="567" height="418"></canvas>
                
                <?php if ( ! empty( $source_label ) ) : ?>
                    <span class="source">
                        <?php esc_html_e( 'Fuente:', 'thecrisisacademy' ); ?>
                        <?php if ( ! empty( $source_url ) ) : ?>
                            <a href="<?php echo esc_url( $source_url ); ?>" target="_blank" rel="noopener noreferrer" title="<?php echo esc_attr( $source_label ); ?>"><?php echo esc_html( $source_label ); ?></a>
                        <?php else : ?>
                            <?php echo esc_html( $source_label ); ?>
                        <?php endif; ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>