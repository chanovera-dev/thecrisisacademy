<?php
/**
 * Template part for Certification section in Individuals template
 *
 * @package TheCrisisAcademy
 */

// ── 00. Intro ─────────────────────────────────────────────────────────────
$cert_intro_subheading = function_exists( 'get_field' ) ? get_field( 'cert_intro_subheading' ) : null;
$cert_intro_title      = function_exists( 'get_field' ) ? get_field( 'cert_intro_title' ) : null;
$cert_intro_lead       = function_exists( 'get_field' ) ? get_field( 'cert_intro_lead' ) : null;

$cert_intro_subheading = ! empty( $cert_intro_subheading ) ? $cert_intro_subheading : 'Entrenamiento especializado';
$cert_intro_title      = ! empty( $cert_intro_title ) ? $cert_intro_title : 'La ruta definitiva para convertir a tu equipo en expertos en gestión de crisis';
$cert_intro_lead       = ! empty( $cert_intro_lead ) ? $cert_intro_lead : 'Cada crisis sin protocolo cuesta reputación, clientes y tiempo que nunca recuperarás.';

// ── 01. El momento crítico ───────────────────────────────────────────────
$cert_01_number      = function_exists( 'get_field' ) ? get_field( 'cert_01_number' ) : null;
$cert_01_header_text = function_exists( 'get_field' ) ? get_field( 'cert_01_header_text' ) : null;
$cert_01_eyebrow     = function_exists( 'get_field' ) ? get_field( 'cert_01_eyebrow' ) : null;
$cert_01_title       = function_exists( 'get_field' ) ? get_field( 'cert_01_title' ) : null;
$cert_01_lead        = function_exists( 'get_field' ) ? get_field( 'cert_01_lead' ) : null;
$cert_01_items       = function_exists( 'get_field' ) ? get_field( 'cert_01_items' ) : null;

$cert_01_number      = ! empty( $cert_01_number ) ? $cert_01_number : '01';
$cert_01_header_text = ! empty( $cert_01_header_text ) ? $cert_01_header_text : 'El momento crítico';
$cert_01_eyebrow     = ! empty( $cert_01_eyebrow ) ? $cert_01_eyebrow : 'Cuando todo cambia';
$cert_01_title       = ! empty( $cert_01_title ) ? $cert_01_title : 'En una crisis, cada decisión cuenta.';
$cert_01_lead        = ! empty( $cert_01_lead ) ? $cert_01_lead : 'Sin preparación, el tiempo se pierde, las respuestas se improvisan y la comunicación se fragmenta.';

if ( empty( $cert_01_items ) || ! is_array( $cert_01_items ) ) {
	$cert_01_items = array(
		array( 'text' => 'Pérdida de tiempo crítico' ),
		array( 'text' => 'Respuestas improvisadas' ),
		array( 'text' => 'Daño reputacional' ),
		array( 'text' => 'Mensajes contradictorios' ),
	);
}

// ── 02. La preparación ───────────────────────────────────────────────────
$cert_02_number      = function_exists( 'get_field' ) ? get_field( 'cert_02_number' ) : null;
$cert_02_header_text = function_exists( 'get_field' ) ? get_field( 'cert_02_header_text' ) : null;
$cert_02_eyebrow     = function_exists( 'get_field' ) ? get_field( 'cert_02_eyebrow' ) : null;
$cert_02_title       = function_exists( 'get_field' ) ? get_field( 'cert_02_title' ) : null;
$cert_02_lead        = function_exists( 'get_field' ) ? get_field( 'cert_02_lead' ) : null;
$cert_02_steps       = function_exists( 'get_field' ) ? get_field( 'cert_02_steps' ) : null;

$cert_02_number      = ! empty( $cert_02_number ) ? $cert_02_number : '02';
$cert_02_header_text = ! empty( $cert_02_header_text ) ? $cert_02_header_text : 'La preparación';
$cert_02_eyebrow     = ! empty( $cert_02_eyebrow ) ? $cert_02_eyebrow : 'Tu proceso de certificación';
$cert_02_title       = ! empty( $cert_02_title ) ? $cert_02_title : 'La respuesta no se improvisa. Se entrena.';
$cert_02_lead        = ! empty( $cert_02_lead ) ? $cert_02_lead : 'Una ruta práctica para pasar del diagnóstico a la acción y medir cómo responde el equipo.';

if ( empty( $cert_02_steps ) || ! is_array( $cert_02_steps ) ) {
	$cert_02_steps = array(
		array(
			'title'       => 'Diagnóstico',
			'description' => 'Detectamos las necesidades de la institución y definimos objetivos.',
		),
		array(
			'title'       => '6 módulos especializados',
			'description' => 'Contenido actualizado, casos reales y tendencias.',
		),
		array(
			'title'       => 'Simulación de crisis',
			'description' => 'Escenarios de alta intensidad en War Room.',
		),
		array(
			'title'       => 'Evaluación y ScoreCard',
			'description' => 'Medición del desempeño con KPIs: URR, MPR y TTR.',
		),
		array(
			'title'       => 'Certificación',
			'description' => 'Demuestra tu aprendizaje y recibe tu certificación profesional.',
		),
	);
}

// ── 03. El formato ───────────────────────────────────────────────────────
$cert_03_number      = function_exists( 'get_field' ) ? get_field( 'cert_03_number' ) : null;
$cert_03_header_text = function_exists( 'get_field' ) ? get_field( 'cert_03_header_text' ) : null;
$cert_03_eyebrow     = function_exists( 'get_field' ) ? get_field( 'cert_03_eyebrow' ) : null;
$cert_03_title       = function_exists( 'get_field' ) ? get_field( 'cert_03_title' ) : null;
$cert_03_lead        = function_exists( 'get_field' ) ? get_field( 'cert_03_lead' ) : null;
$cert_03_formats     = function_exists( 'get_field' ) ? get_field( 'cert_03_formats' ) : null;

$cert_03_number      = ! empty( $cert_03_number ) ? $cert_03_number : '03';
$cert_03_header_text = ! empty( $cert_03_header_text ) ? $cert_03_header_text : 'El formato';
$cert_03_eyebrow     = ! empty( $cert_03_eyebrow ) ? $cert_03_eyebrow : 'Una ruta a tu medida';
$cert_03_title       = ! empty( $cert_03_title ) ? $cert_03_title : 'Aprende como mejor funciona para ti.';
$cert_03_lead        = ! empty( $cert_03_lead ) ? $cert_03_lead : 'Cursa los módulos de manera individual según tus necesidades o completa la ruta para obtener una Constancia Oficial.';

if ( empty( $cert_03_formats ) || ! is_array( $cert_03_formats ) ) {
	$cert_03_formats = array(
		array(
			'icon'        => 'online',
			'title'       => 'En línea',
			'description' => 'Cúrsalo en tiempo real.',
		),
		array(
			'icon'        => 'presencial',
			'title'       => 'Presencial',
			'description' => 'También disponible en formato presencial intensivo.',
		),
	);
}

// ── 04. El siguiente capítulo ────────────────────────────────────────────
$cert_04_number      = function_exists( 'get_field' ) ? get_field( 'cert_04_number' ) : null;
$cert_04_header_text = function_exists( 'get_field' ) ? get_field( 'cert_04_header_text' ) : null;
$cert_04_title       = function_exists( 'get_field' ) ? get_field( 'cert_04_title' ) : null;
$cert_04_points      = function_exists( 'get_field' ) ? get_field( 'cert_04_points' ) : null;
$cert_04_button_text = function_exists( 'get_field' ) ? get_field( 'cert_04_button_text' ) : null;
$cert_04_button_url  = function_exists( 'get_field' ) ? get_field( 'cert_04_button_url' ) : null;

$cert_04_number      = ! empty( $cert_04_number ) ? $cert_04_number : '04';
$cert_04_header_text = ! empty( $cert_04_header_text ) ? $cert_04_header_text : 'El siguiente capítulo';
$cert_04_title       = ! empty( $cert_04_title ) ? $cert_04_title : 'Obtén tu Certificado de Especialización en Comunicación de Crisis.';
$cert_04_button_text = ! empty( $cert_04_button_text ) ? $cert_04_button_text : 'Inscribirme ahora';
$cert_04_button_url  = ! empty( $cert_04_button_url ) ? $cert_04_button_url : '#cta';

if ( empty( $cert_04_points ) || ! is_array( $cert_04_points ) ) {
	$cert_04_points = array(
		array( 'text' => 'Grupos reducidos garantizados' ),
		array( 'text' => 'Avalado internacionalmente' ),
		array( 'text' => 'Instructores expertos en activo' ),
	);
}
?>
<section id="certification-00" class="block whiteprint-background">
    <div class="content">
        <header class="section-header center">
            <span class="sub-heading pretext-reveal"><?php echo esc_html( $cert_intro_subheading ); ?></span>
            <h2 class="title-section title-reveal"><?php echo wp_kses_post( $cert_intro_title ); ?></h2>
            <p class="lead title-reveal"><?php echo wp_kses_post( $cert_intro_lead ); ?></p>
        </header>
    </div>
</section>
<section id="certification-01" class="block blue-background-00">
    <div class="content">
        <header class="section-header center">
            <span class="sub-heading object-reveal">
                <span class="number"><?php echo esc_html( $cert_01_number ); ?></span>
                <span class="text"><?php echo esc_html( $cert_01_header_text ); ?></span>
            </span>
        </header>
        <div class="data content-grid">
            <div class="text">
                <span class="certification-story__eyebrow pretext-reveal"><?php echo esc_html( $cert_01_eyebrow ); ?></span>
                <h2 class="title-section title-reveal"><?php echo wp_kses_post( $cert_01_title ); ?></h2>
                <p class="lead title-reveal"><?php echo wp_kses_post( $cert_01_lead ); ?></p>
            </div>
            <ul class="certification-story">
                <?php foreach ( $cert_01_items as $index => $item ) : 
                    $item_text = is_array( $item ) ? ( $item['text'] ?? '' ) : $item;
                    if ( '' === trim( $item_text ) ) continue;
                ?>
                    <li class="object-reveal">
                        <span class="certification-story__step-number"><?php echo esc_html( sprintf( '%02d', $index + 1 ) ); ?></span>
                        <?php echo esc_html( $item_text ); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</section>
<section id="certification-02" class="block blue-background-00">
    <div class="content">
        <header class="section-header center">
            <span class="sub-heading object-reveal">
                <span class="number"><?php echo esc_html( $cert_02_number ); ?></span>
                <span class="text"><?php echo esc_html( $cert_02_header_text ); ?></span>
            </span>
        </header>
        <div class="data content-grid">
            <div class="text">
                <span class="certification-story__eyebrow pretext-reveal"><?php echo esc_html( $cert_02_eyebrow ); ?></span>
                <h2 class="title-section title-reveal"><?php echo wp_kses_post( $cert_02_title ); ?></h2>
                <p class="lead title-reveal"><?php echo wp_kses_post( $cert_02_lead ); ?></p>
            </div>
            <ol class="certification-story">
                <?php foreach ( $cert_02_steps as $index => $step ) : ?>
                    <li class="object-reveal">
                        <span class="certification-story__step-number"><?php echo esc_html( sprintf( '%02d', $index + 1 ) ); ?></span>
                        <div>
                            <h4><?php echo esc_html( $step['title'] ?? '' ); ?></h4>
                            <p><?php echo esc_html( $step['description'] ?? '' ); ?></p>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ol>
        </div>
    </div>
</section>
<section id="certification-03" class="block blue-background-00">
    <div class="content">
        <header class="section-header center">
            <span class="sub-heading object-reveal">
                <span class="number"><?php echo esc_html( $cert_03_number ); ?></span>
                <span class="text"><?php echo esc_html( $cert_03_header_text ); ?></span>
            </span>
        </header>
        <div class="data content-grid">
            <div class="text">
                <span class="certification-story__eyebrow pretext-reveal"><?php echo esc_html( $cert_03_eyebrow ); ?></span>
                <h2 class="title-section title-reveal"><?php echo wp_kses_post( $cert_03_title ); ?></h2>
                <p class="lead title-reveal"><?php echo wp_kses_post( $cert_03_lead ); ?></p>
            </div>
            <div class="certification-story">
                <?php foreach ( $cert_03_formats as $format ) : 
                    $format_icon = $format['icon'] ?? 'online';
                    $icon_html   = function_exists( 'thecrisisacademy_get_certification_format_icon' ) 
                        ? thecrisisacademy_get_certification_format_icon( $format_icon ) 
                        : '';
                ?>
                    <div class="certification-story__card card-reveal">
                        <span class="certification-story__format-mark big-badge sub-heading">
                            <?php echo $icon_html; ?>
                        </span>
                        <div>
                            <h4><?php echo esc_html( $format['title'] ?? '' ); ?></h4>
                            <p><?php echo esc_html( $format['description'] ?? '' ); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
<section id="certification-04" class="block blue-background-00">
    <div class="content">
        <header class="section-header center">
            <span class="sub-heading object-reveal">
                <span class="number"><?php echo esc_html( $cert_04_number ); ?></span>
                <span class="text"><?php echo esc_html( $cert_04_header_text ); ?></span>
            </span>
        </header>
        <div class="data center">
            <h2 class="title-section"><?php echo wp_kses_post( $cert_04_title ); ?></h2>
            <div class="points-slideshow">
                <ul class="card-points">
                    <?php foreach ( $cert_04_points as $index => $point ) : 
                        $text = is_array( $point ) ? ( $point['text'] ?? '' ) : $point;
                        if ( '' === trim( $text ) ) continue;
                    ?>
                        <li class="<?php echo 0 === $index ? 'is-active' : ''; ?>">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            <span><?php echo esc_html( $text ); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <?php if ( count( $cert_04_points ) > 1 ) : ?>
                    <div class="points-nav" role="tablist" aria-label="Puntos clave">
                        <?php foreach ( $cert_04_points as $index => $point ) : ?>
                            <button type="button" class="point-dot <?php echo 0 === $index ? 'is-active' : ''; ?>" role="tab" aria-selected="<?php echo 0 === $index ? 'true' : 'false'; ?>" aria-label="<?php echo esc_attr( sprintf( 'Punto %d de %d', $index + 1, count( $cert_04_points ) ) ); ?>"></button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <a class="btn primary cert-btn-primary" href="<?php echo esc_url( $cert_04_button_url ); ?>" id="cert-main-button">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
                <?php echo esc_html( $cert_04_button_text ); ?>
            </a>
        </div>
    </div>
</section>
