<?php
/**
 * Individuals Page SEO & Structured Data Enhancements
 *
 * Provides dedicated SEO optimization for the Individuals (Particulares) landing page template:
 * - Automated Open Graph & Twitter Cards social metadata.
 * - Dynamic Schema.org JSON-LD graph (Course, EducationalOrganization, Person, FAQPage, Event, WebPage, BreadcrumbList).
 * - Optimized document title generation.
 * - Native WordPress admin SEO metabox with live Google SERP preview.
 *
 * All comments and DocBlocks are in English.
 *
 * @package TheCrisisAcademy
 * @subpackage SEO
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Retrieve default fallback values for Individuals SEO fields
 *
 * @return array
 */
function thecrisisacademy_get_individuals_seo_defaults() {
	return array(
		'title'         => 'Especialización en Comunicación para Manejo de Crisis | The Crisis Academy',
		'description'   => 'Domina la gestión de crisis en la era de la IA y protege lo que más importa: tu reputación. Conoce el Framework de Respuesta Inmediata y entrena con simulación inmersiva.',
		'keywords'      => 'especialización en crisis, comunicación de crisis, manejo de crisis para directivos, simulador de crisis con IA, vocería de crisis, reputación profesional, framework de respuesta inmediata, Carolina Eslava',
		'og_image'      => '',
		'canonical'     => '',
		'enable_schema' => '1',
		'enable_course' => '1',
		'enable_faq'    => '1',
		'enable_events' => '1',
	);
}

/**
 * Check if a third-party SEO plugin is handling meta tags
 *
 * @return bool
 */
if ( ! function_exists( 'thecrisisacademy_has_active_seo_plugin' ) ) {
	function thecrisisacademy_has_active_seo_plugin() {
		return defined( 'WPSEO_VERSION' ) // Yoast SEO
			|| defined( 'RANK_MATH_VERSION' ) // Rank Math
			|| defined( 'AIOSEO_VERSION' ) // All in One SEO
			|| defined( 'SEOPRESS_VERSION' ); // SEOPress
	}
}

/**
 * Retrieve sanitized Individuals SEO data for a post with fallback to defaults and ACF content
 *
 * @param int|null $post_id Post ID (defaults to current post)
 * @return array
 */
function thecrisisacademy_get_individuals_seo_data( $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	$defaults = thecrisisacademy_get_individuals_seo_defaults();

	if ( ! $post_id ) {
		return $defaults;
	}

	$title         = get_post_meta( $post_id, '_individuals_seo_title', true );
	$description   = get_post_meta( $post_id, '_individuals_seo_description', true );
	$keywords      = get_post_meta( $post_id, '_individuals_seo_keywords', true );
	$og_image      = get_post_meta( $post_id, '_individuals_seo_og_image', true );
	$canonical     = get_post_meta( $post_id, '_individuals_seo_canonical', true );
	$enable_schema = get_post_meta( $post_id, '_individuals_seo_enable_schema', true );
	$enable_course = get_post_meta( $post_id, '_individuals_seo_enable_course', true );
	$enable_faq    = get_post_meta( $post_id, '_individuals_seo_enable_faq', true );
	$enable_events = get_post_meta( $post_id, '_individuals_seo_enable_events', true );

	// Fallback to ACF Hero fields if custom SEO values are not set
	if ( empty( $title ) && function_exists( 'get_field' ) ) {
		$hero_title = get_field( 'hero_title', $post_id );
		if ( ! empty( $hero_title ) ) {
			$title = wp_strip_all_tags( $hero_title ) . ' | The Crisis Academy';
		}
	}

	if ( empty( $description ) && function_exists( 'get_field' ) ) {
		$hero_desc = get_field( 'hero_description', $post_id );
		if ( ! empty( $hero_desc ) ) {
			$description = wp_strip_all_tags( $hero_desc );
		}
	}

	// Smart fallback for social image: simulation image, founder photo or site icon
	if ( empty( $og_image ) ) {
		if ( function_exists( 'thecrisisacademy_get_founder_data' ) ) {
			$founder_data = thecrisisacademy_get_founder_data();
			if ( ! empty( $founder_data['photo_url'] ) ) {
				$og_image = $founder_data['photo_url'];
			}
		}

		if ( empty( $og_image ) && has_site_icon() ) {
			$og_image = get_site_icon_url( 512 );
		}
	}

	return array(
		'title'         => ! empty( $title ) ? $title : $defaults['title'],
		'description'   => ! empty( $description ) ? $description : $defaults['description'],
		'keywords'      => ! empty( $keywords ) ? $keywords : $defaults['keywords'],
		'og_image'      => ! empty( $og_image ) ? $og_image : $defaults['og_image'],
		'canonical'     => ! empty( $canonical ) ? $canonical : get_permalink( $post_id ),
		'enable_schema' => '' !== $enable_schema && false !== $enable_schema ? $enable_schema : $defaults['enable_schema'],
		'enable_course' => '' !== $enable_course && false !== $enable_course ? $enable_course : $defaults['enable_course'],
		'enable_faq'    => '' !== $enable_faq && false !== $enable_faq ? $enable_faq : $defaults['enable_faq'],
		'enable_events' => '' !== $enable_events && false !== $enable_events ? $enable_events : $defaults['enable_events'],
	);
}

/**
 * Filter document title for Individuals page to maximize CTR and search relevance
 *
 * @param array $title_parts Title parts array
 * @return array
 */
function thecrisisacademy_individuals_filter_document_title( $title_parts ) {
	if ( is_page_template( 'templates/individuals.php' ) ) {
		$seo_data = thecrisisacademy_get_individuals_seo_data();
		if ( ! empty( $seo_data['title'] ) ) {
			$title_parts['title'] = $seo_data['title'];
			// Unset tagline or site name to prevent redundant repetition if already present
			if ( strpos( $seo_data['title'], 'The Crisis Academy' ) !== false ) {
				unset( $title_parts['site'] );
				unset( $title_parts['tagline'] );
			}
		}
	}
	return $title_parts;
}
add_filter( 'document_title_parts', 'thecrisisacademy_individuals_filter_document_title', 20 );

/**
 * Output SEO meta tags, Open Graph, and Twitter Cards into <head> for Individuals template
 */
function thecrisisacademy_individuals_seo_head() {
	if ( ! is_page_template( 'templates/individuals.php' ) ) {
		return;
	}

	$post_id   = get_the_ID();
	$seo_data  = thecrisisacademy_get_individuals_seo_data( $post_id );
	$site_name = get_bloginfo( 'name' );
	$page_url  = ! empty( $seo_data['canonical'] ) ? $seo_data['canonical'] : get_permalink( $post_id );

	// Output standard meta tags and Open Graph only if no 3rd-party SEO plugin is active
	if ( ! thecrisisacademy_has_active_seo_plugin() ) {
		?>
		<!-- The Crisis Academy • Individuals SEO Metadata -->
		<meta name="description" content="<?php echo esc_attr( $seo_data['description'] ); ?>" />
		<?php if ( ! empty( $seo_data['keywords'] ) ) : ?>
		<meta name="keywords" content="<?php echo esc_attr( $seo_data['keywords'] ); ?>" />
		<?php endif; ?>
		<meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1" />
		<link rel="canonical" href="<?php echo esc_url( $page_url ); ?>" />

		<!-- Open Graph Metadata -->
		<meta property="og:locale" content="<?php echo esc_attr( get_locale() ); ?>" />
		<meta property="og:type" content="website" />
		<meta property="og:title" content="<?php echo esc_attr( $seo_data['title'] ); ?>" />
		<meta property="og:description" content="<?php echo esc_attr( $seo_data['description'] ); ?>" />
		<meta property="og:url" content="<?php echo esc_url( $page_url ); ?>" />
		<meta property="og:site_name" content="<?php echo esc_attr( $site_name ); ?>" />
		<?php if ( ! empty( $seo_data['og_image'] ) ) : ?>
		<meta property="og:image" content="<?php echo esc_url( $seo_data['og_image'] ); ?>" />
		<meta property="og:image:secure_url" content="<?php echo esc_url( $seo_data['og_image'] ); ?>" />
		<meta property="og:image:alt" content="<?php echo esc_attr( $seo_data['title'] ); ?>" />
		<?php endif; ?>

		<!-- Twitter Card Metadata -->
		<meta name="twitter:card" content="summary_large_image" />
		<meta name="twitter:title" content="<?php echo esc_attr( $seo_data['title'] ); ?>" />
		<meta name="twitter:description" content="<?php echo esc_attr( $seo_data['description'] ); ?>" />
		<?php if ( ! empty( $seo_data['og_image'] ) ) : ?>
		<meta name="twitter:image" content="<?php echo esc_url( $seo_data['og_image'] ); ?>" />
		<?php endif; ?>
		<!-- End The Crisis Academy Individuals SEO Metadata -->
		<?php
	}

	// Output Schema.org Structured Data (JSON-LD)
	if ( '1' === $seo_data['enable_schema'] ) {
		thecrisisacademy_output_individuals_jsonld( $post_id, $seo_data );
	}
}
add_action( 'wp_head', 'thecrisisacademy_individuals_seo_head', 1 );

/**
 * Generate and output comprehensive Schema.org JSON-LD graph for Individuals page
 *
 * @param int   $post_id  Current post ID
 * @param array $seo_data Sanitized SEO data
 */
function thecrisisacademy_output_individuals_jsonld( $post_id, $seo_data ) {
	$home_url     = home_url( '/' );
	$page_url     = ! empty( $seo_data['canonical'] ) ? $seo_data['canonical'] : get_permalink( $post_id );
	$founder_data = function_exists( 'thecrisisacademy_get_founder_data' ) ? thecrisisacademy_get_founder_data() : array();
	$cta_data     = function_exists( 'thecrisisacademy_get_cta_data' ) ? thecrisisacademy_get_cta_data( $post_id ) : array();

	$graph = array();

	// 1. EducationalOrganization / Organization Schema
	$org_schema = array(
		'@type'         => 'EducationalOrganization',
		'@id'           => $home_url . '#organization',
		'name'          => 'The Crisis Academy',
		'alternateName' => 'Crisis Academy',
		'url'           => $home_url,
		'description'   => 'Academia especializada en entrenamiento directivo, simulación inmersiva y gestión estratégica de crisis reputacionales.',
		'founder'       => array(
			'@type' => 'Person',
			'@id'   => $home_url . '#founder',
			'name'  => ! empty( $founder_data['name'] ) ? $founder_data['name'] : 'Carolina Eslava',
		),
	);

	// Add site icon/logo if available
	$custom_logo_id = get_theme_mod( 'custom_logo' );
	if ( $custom_logo_id ) {
		$logo_url = wp_get_attachment_image_url( $custom_logo_id, 'full' );
		if ( $logo_url ) {
			$org_schema['logo'] = array(
				'@type'   => 'ImageObject',
				'@id'     => $home_url . '#logo',
				'url'     => esc_url( $logo_url ),
				'caption' => 'The Crisis Academy',
			);
		}
	}

	// Add ContactPoint from CTA WhatsApp info
	if ( ! empty( $cta_data['whatsapp_phone'] ) ) {
		$org_schema['contactPoint'] = array(
			'@type'             => 'ContactPoint',
			'telephone'         => sanitize_text_field( $cta_data['whatsapp_phone'] ),
			'contactType'       => 'training and admissions inquiries',
			'areaServed'        => array( 'MX', 'LATAM', 'ES', 'US' ),
			'availableLanguage' => array( 'Spanish', 'English' ),
		);
	}

	$graph[] = $org_schema;

	// 2. Person (Founder - Carolina Eslava) Schema
	$person_schema = array(
		'@type'       => 'Person',
		'@id'         => $home_url . '#founder',
		'name'        => ! empty( $founder_data['name'] ) ? $founder_data['name'] : 'Carolina Eslava',
		'jobTitle'    => ! empty( $founder_data['role'] ) ? $founder_data['role'] : 'Fundadora & Directora de The Crisis Academy',
		'worksFor'    => array(
			'@id' => $home_url . '#organization',
		),
		'description' => ! empty( $founder_data['quote'] ) ? wp_strip_all_tags( $founder_data['quote'] ) : 'Experta en gestión y comunicación de crisis con más de 2,000 ejecutivos entrenados bajo simulación activa.',
	);

	if ( ! empty( $founder_data['photo_url'] ) ) {
		$person_schema['image'] = esc_url( $founder_data['photo_url'] );
	}

	$graph[] = $person_schema;

	// 3. Course / EducationalOccupationalProgram Schema (Specialization for Individuals)
	if ( '1' === $seo_data['enable_course'] ) {
		$course_name = function_exists( 'get_field' ) ? get_field( 'hero_preheading', $post_id ) : '';
		if ( empty( $course_name ) ) {
			$course_name = 'Especialización en Comunicación para Manejo de Crisis';
		}

		$course_modules = array(
			array(
				'@type'          => 'CourseInstance',
				'name'           => 'Módulo 1: Detección Temprana y Radar de Señales Débiles',
				'courseMode'     => 'blended',
				'courseWorkload' => 'Metodología predictiva para anticipar crisis reputacionales antes del impacto viral.',
			),
			array(
				'@type'          => 'CourseInstance',
				'name'           => 'Módulo 2: Framework de Respuesta Inmediata y Vocería Bajo Presión',
				'courseMode'     => 'blended',
				'courseWorkload' => 'Control de narrativa, mensajes clave y protocolos de actuación en los primeros 60 minutos.',
			),
			array(
				'@type'          => 'CourseInstance',
				'name'           => 'Módulo 3: Simulación Inmersiva de Crisis con IA',
				'courseMode'     => 'blended',
				'courseWorkload' => 'Práctica interactiva en vivo con escenarios dinámicos de filtraciones, desinformación y ataques digitales.',
			),
			array(
				'@type'          => 'CourseInstance',
				'name'           => 'Módulo 4: Protocolos de Recuperación y Blindaje de Reputación',
				'courseMode'     => 'blended',
				'courseWorkload' => 'Auditoría posterior, mitigación de daños y restablecimiento de confianza con stakeholders.',
			),
		);

		$course_schema = array(
			'@type'                        => 'Course',
			'@id'                          => $page_url . '#course',
			'name'                         => $course_name,
			'description'                  => wp_strip_all_tags( $seo_data['description'] ),
			'provider'                     => array(
				'@id' => $home_url . '#organization',
			),
			'instructor'                   => array(
				'@id' => $home_url . '#founder',
			),
			'inLanguage'                   => 'es',
			'courseMode'                   => 'blended',
			'educationalCredentialAwarded' => 'Certificación en Comunicación para Manejo de Crisis',
			'timeRequired'                 => 'PT12H',
			'audience'                     => array(
				'@type'        => 'Audience',
				'audienceType' => 'Profesionales, directivos, voceros, comunicadores y consultores de reputación',
			),
			'hasCourseInstance'            => $course_modules,
			'offers'                       => array(
				'@type'         => 'Offer',
				'category'      => 'Certificación Profesional',
				'availability'  => 'https://schema.org/InStock',
				'priceCurrency' => 'USD',
				'url'           => $page_url . '#cta',
			),
		);

		$graph[] = $course_schema;
	}

	// 4. FAQPage Schema
	if ( '1' === $seo_data['enable_faq'] && function_exists( 'thecrisisacademy_get_faq_schema_items' ) ) {
		$faq_items = thecrisisacademy_get_faq_schema_items( $post_id );
		if ( ! empty( $faq_items ) ) {
			$faq_schema = array(
				'@type'      => 'FAQPage',
				'@id'        => $page_url . '#faq',
				'mainEntity' => $faq_items,
			);
			$graph[] = $faq_schema;
		}
	}

	// 5. Upcoming Events Schema
	if ( '1' === $seo_data['enable_events'] && function_exists( 'thecrisisacademy_get_events_schema_items' ) ) {
		$events_schema = thecrisisacademy_get_events_schema_items();
		if ( ! empty( $events_schema ) ) {
			foreach ( $events_schema as $evt ) {
				$graph[] = $evt;
			}
		}
	}

	// 6. WebPage & BreadcrumbList Schema
	$webpage_schema = array(
		'@type'          => 'WebPage',
		'@id'            => $page_url . '#webpage',
		'url'            => $page_url,
		'name'           => $seo_data['title'],
		'description'    => $seo_data['description'],
		'isPartOf'       => array(
			'@id' => $home_url . '#website',
		),
		'about'          => array(
			'@id' => $page_url . '#course',
		),
		'breadcrumb'     => array(
			'@type'           => 'BreadcrumbList',
			'@id'             => $page_url . '#breadcrumb',
			'itemListElement' => array(
				array(
					'@type'    => 'ListItem',
					'position' => 1,
					'name'     => 'Inicio',
					'item'     => $home_url,
				),
				array(
					'@type'    => 'ListItem',
					'position' => 2,
					'name'     => 'Particulares',
					'item'     => $page_url,
				),
			),
		),
	);

	$graph[] = $webpage_schema;

	// Output valid JSON-LD
	$json_ld = array(
		'@context' => 'https://schema.org',
		'@graph'   => $graph,
	);

	echo "\n" . '<script type="application/ld+json">' . wp_json_encode( $json_ld, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) . "</script>\n";
}

/**
 * Register Meta Boxes for Individuals Page Template SEO
 *
 * @param string  $post_type Post type
 * @param WP_Post $post      Post object
 */
function thecrisisacademy_register_individuals_seo_metabox( $post_type, $post ) {
	if ( ! function_exists( 'add_meta_box' ) || 'page' !== $post_type || ! $post ) {
		return;
	}

	$template = get_post_meta( $post->ID, '_wp_page_template', true );

	if ( 'templates/individuals.php' !== $template ) {
		return;
	}

	add_meta_box(
		'individuals_seo_metabox',
		'🔍 Optimización SEO y Redes Sociales — Particulares (Open Graph, Twitter Cards y Schema.org)',
		'thecrisisacademy_render_individuals_seo_metabox',
		'page',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'thecrisisacademy_register_individuals_seo_metabox', 5, 2 );

/**
 * Render Individuals SEO Metabox HTML in Page Editor
 *
 * @param WP_Post $post Current post object
 */
function thecrisisacademy_render_individuals_seo_metabox( $post ) {
	wp_nonce_field( 'individuals_seo_metabox_save', 'individuals_seo_nonce' );

	$seo = thecrisisacademy_get_individuals_seo_data( $post->ID );
	?>
	<div class="corporate-metabox-wrapper individuals-seo-metabox-wrap" style="padding: 15px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen-Sans, Ubuntu, Cantarell, sans-serif;">
		
		<!-- SERP Preview Card -->
		<div style="background: #ffffff; border: 1px solid #dcdcde; border-radius: 8px; padding: 18px; margin-bottom: 25px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
			<h4 style="margin: 0 0 12px; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; color: #50575e;">
				Vista Previa en Google (SERP Snippet Preview)
			</h4>
			<div style="font-family: Arial, sans-serif; max-width: 600px; padding: 10px 0;">
				<div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
					<div style="width: 24px; height: 24px; background: #000; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 11px; font-weight: bold;">TCA</div>
					<div>
						<div style="font-size: 14px; color: #202124; line-height: 1.2;">The Crisis Academy</div>
						<div style="font-size: 12px; color: #4d5156; line-height: 1.2; word-break: break-all;" id="ind-serp-preview-url"><?php echo esc_html( get_permalink( $post->ID ) ); ?></div>
					</div>
				</div>
				<h3 style="font-size: 20px; line-height: 1.3; color: #1a0dab; margin: 4px 0 6px; cursor: pointer; text-decoration: none;" id="ind-serp-preview-title">
					<?php echo esc_html( $seo['title'] ); ?>
				</h3>
				<p style="font-size: 14px; line-height: 1.5; color: #4d5156; margin: 0;" id="ind-serp-preview-desc">
					<?php echo esc_html( $seo['description'] ); ?>
				</p>
			</div>
			<p style="margin: 8px 0 0; font-size: 11px; color: #8c8f94;">
				* Simulación aproximada de visualización en resultados de búsqueda de escritorio.
			</p>
		</div>

		<!-- SEO Fields Grid -->
		<div style="display: grid; grid-template-columns: 1fr; gap: 20px;">

			<!-- SEO Title -->
			<div>
				<label for="individuals_seo_title" style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 13px;">
					Título SEO (<title> tag y og:title)
				</label>
				<input type="text"
					id="individuals_seo_title"
					name="individuals_seo[title]"
					value="<?php echo esc_attr( $seo['title'] ); ?>"
					style="width: 100%; max-width: 100%; padding: 8px 12px; font-size: 14px; border: 1px solid #8c8f94; border-radius: 4px;"
					placeholder="Ej. Especialización en Comunicación para Manejo de Crisis | The Crisis Academy"
				/>
				<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 4px;">
					<span style="font-size: 12px; color: #646970;">Longitud recomendada: 50–60 caracteres.</span>
					<span id="ind-title-counter" style="font-size: 12px; font-weight: 600; color: #2271b1;">0 / 60</span>
				</div>
			</div>

			<!-- Meta Description -->
			<div>
				<label for="individuals_seo_description" style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 13px;">
					Meta Descripción (Snippet SERP y og:description)
				</label>
				<textarea
					id="individuals_seo_description"
					name="individuals_seo[description]"
					rows="3"
					style="width: 100%; max-width: 100%; padding: 8px 12px; font-size: 14px; border: 1px solid #8c8f94; border-radius: 4px;"
					placeholder="Resumen atractivo orientado al clic (CTR) sobre la especialización para particulares..."
				><?php echo esc_textarea( $seo['description'] ); ?></textarea>
				<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 4px;">
					<span style="font-size: 12px; color: #646970;">Longitud recomendada: 140–160 caracteres.</span>
					<span id="ind-desc-counter" style="font-size: 12px; font-weight: 600; color: #2271b1;">0 / 160</span>
				</div>
			</div>

			<!-- Keywords -->
			<div>
				<label for="individuals_seo_keywords" style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 13px;">
					Palabras Clave (Keywords meta tag)
				</label>
				<input type="text"
					id="individuals_seo_keywords"
					name="individuals_seo[keywords]"
					value="<?php echo esc_attr( $seo['keywords'] ); ?>"
					style="width: 100%; max-width: 100%; padding: 8px 12px; font-size: 14px; border: 1px solid #8c8f94; border-radius: 4px;"
					placeholder="Separadas por comas. Ej: comunicación de crisis, vocería, reputación..."
				/>
				<span style="display: block; margin-top: 4px; font-size: 12px; color: #646970;">Términos clave relevantes para motores de búsqueda secundarios y archivo interno.</span>
			</div>

			<!-- Open Graph Social Image -->
			<div style="background: #f6f7f7; padding: 15px; border-radius: 6px; border: 1px solid #dcdcde;">
				<label for="individuals_seo_og_image" style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 13px;">
					Imagen para Redes Sociales (Open Graph & Twitter Card)
				</label>
				<div style="display: flex; gap: 10px; align-items: center; margin-bottom: 8px;">
					<input type="text"
						id="individuals_seo_og_image"
						name="individuals_seo[og_image]"
						value="<?php echo esc_attr( $seo['og_image'] ); ?>"
						style="flex: 1; padding: 8px 12px; font-size: 14px; border: 1px solid #8c8f94; border-radius: 4px;"
						placeholder="https://..."
					/>
					<button type="button" class="button button-secondary" id="ind_upload_og_image_btn">
						Seleccionar Imagen
					</button>
				</div>
				<p style="margin: 0; font-size: 12px; color: #646970;">
					Tamaño óptimo recomendado: <strong>1200 x 630 px</strong> (JPG o PNG). Si se deja vacío, se usará la imagen institucional por defecto.
				</p>
				<div id="ind_og_image_preview" style="margin-top: 10px; max-width: 240px;">
					<?php if ( ! empty( $seo['og_image'] ) ) : ?>
						<img src="<?php echo esc_url( $seo['og_image'] ); ?>" style="max-width:100%; height:auto; border-radius:4px; border:1px solid #dcdcde;" alt="Social Preview" />
					<?php endif; ?>
				</div>
			</div>

			<!-- Canonical URL -->
			<div>
				<label for="individuals_seo_canonical" style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 13px;">
					URL Canónica Personalizada (opcional)
				</label>
				<input type="url"
					id="individuals_seo_canonical"
					name="individuals_seo[canonical]"
					value="<?php echo esc_attr( $seo['canonical'] ); ?>"
					style="width: 100%; max-width: 100%; padding: 8px 12px; font-size: 14px; border: 1px solid #8c8f94; border-radius: 4px;"
					placeholder="<?php echo esc_attr( get_permalink( $post->ID ) ); ?>"
				/>
				<span style="display: block; margin-top: 4px; font-size: 12px; color: #646970;">Por defecto se utiliza el permalink oficial de la página para evitar contenido duplicado.</span>
			</div>

			<!-- Schema.org Toggles -->
			<div style="background: #f0f6fc; border: 1px solid #c8d8ea; border-radius: 6px; padding: 15px;">
				<h4 style="margin: 0 0 10px; font-size: 13px; color: #0c4a6e; text-transform: uppercase; letter-spacing: 0.5px;">
					Datos Estructurados Rich Snippets (Schema.org JSON-LD)
				</h4>
				<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 12px;">
					<label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
						<input type="checkbox" name="individuals_seo[enable_schema]" value="1" <?php checked( '1', $seo['enable_schema'] ); ?> />
						<span style="font-weight: 600;">Activar Gráfico Schema.org JSON-LD</span>
					</label>
					<label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
						<input type="checkbox" name="individuals_seo[enable_course]" value="1" <?php checked( '1', $seo['enable_course'] ); ?> />
						<span>Incluir Schema de Especialización (Course)</span>
					</label>
					<label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
						<input type="checkbox" name="individuals_seo[enable_faq]" value="1" <?php checked( '1', $seo['enable_faq'] ); ?> />
						<span>Incluir Preguntas Frecuentes (FAQPage)</span>
					</label>
					<label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
						<input type="checkbox" name="individuals_seo[enable_events]" value="1" <?php checked( '1', $seo['enable_events'] ); ?> />
						<span>Incluir Próximos Eventos (Event)</span>
					</label>
				</div>
			</div>

		</div>
	</div>

	<!-- Interactive SERP live-update script -->
	<script>
	(function() {
		var titleInput = document.getElementById('individuals_seo_title');
		var descInput  = document.getElementById('individuals_seo_description');
		var titlePrev  = document.getElementById('ind-serp-preview-title');
		var descPrev   = document.getElementById('ind-serp-preview-desc');
		var titleCount = document.getElementById('ind-title-counter');
		var descCount  = document.getElementById('ind-desc-counter');

		function updateCounters() {
			if (titleInput && titleCount) {
				var tLen = titleInput.value.length;
				titleCount.textContent = tLen + ' / 60';
				titleCount.style.color = (tLen > 60) ? '#d63638' : ((tLen >= 40) ? '#00a32a' : '#2271b1');
				if (titlePrev) {
					titlePrev.textContent = titleInput.value || '<?php echo esc_js( $seo['title'] ); ?>';
				}
			}
			if (descInput && descCount) {
				var dLen = descInput.value.length;
				descCount.textContent = dLen + ' / 160';
				descCount.style.color = (dLen > 160) ? '#d63638' : ((dLen >= 120) ? '#00a32a' : '#2271b1');
				if (descPrev) {
					descPrev.textContent = descInput.value || '<?php echo esc_js( $seo['description'] ); ?>';
				}
			}
		}

		if (titleInput) {
			titleInput.addEventListener('input', updateCounters);
		}
		if (descInput) {
			descInput.addEventListener('input', updateCounters);
		}
		updateCounters();

		// Media Library Uploader for OG Image
		var uploadBtn = document.getElementById('ind_upload_og_image_btn');
		var imgInput  = document.getElementById('individuals_seo_og_image');
		var preview   = document.getElementById('ind_og_image_preview');

		if (uploadBtn && typeof wp !== 'undefined' && wp.media) {
			uploadBtn.addEventListener('click', function(e) {
				e.preventDefault();
				var frame = wp.media({
					title: 'Seleccionar imagen para Redes Sociales (Open Graph)',
					button: { text: 'Usar esta imagen' },
					multiple: false
				});
				frame.on('select', function() {
					var attachment = frame.state().get('selection').first().toJSON();
					imgInput.value = attachment.url;
					if (preview) {
						preview.innerHTML = '<img src="' + attachment.url + '" style="max-width:100%; height:auto; border-radius:4px; border:1px solid #dcdcde;" alt="Preview" />';
					}
				});
				frame.open();
			});
		}
	})();
	</script>
	<?php
}

/**
 * Save Individuals SEO Metabox Data
 *
 * @param int     $post_id Post ID
 * @param WP_Post $post    Post object
 */
function thecrisisacademy_save_individuals_seo_metabox( $post_id, $post = null ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! isset( $_POST['individuals_seo_nonce'] ) || ! wp_verify_nonce( $_POST['individuals_seo_nonce'], 'individuals_seo_metabox_save' ) ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['individuals_seo'] ) && is_array( $_POST['individuals_seo'] ) ) {
		$seo_data = $_POST['individuals_seo'];

		if ( isset( $seo_data['title'] ) ) {
			update_post_meta( $post_id, '_individuals_seo_title', sanitize_text_field( $seo_data['title'] ) );
		}
		if ( isset( $seo_data['description'] ) ) {
			update_post_meta( $post_id, '_individuals_seo_description', sanitize_textarea_field( $seo_data['description'] ) );
		}
		if ( isset( $seo_data['keywords'] ) ) {
			update_post_meta( $post_id, '_individuals_seo_keywords', sanitize_text_field( $seo_data['keywords'] ) );
		}
		if ( isset( $seo_data['og_image'] ) ) {
			update_post_meta( $post_id, '_individuals_seo_og_image', esc_url_raw( trim( $seo_data['og_image'] ) ) );
		}
		if ( isset( $seo_data['canonical'] ) ) {
			update_post_meta( $post_id, '_individuals_seo_canonical', esc_url_raw( trim( $seo_data['canonical'] ) ) );
		}

		update_post_meta( $post_id, '_individuals_seo_enable_schema', ! empty( $seo_data['enable_schema'] ) ? '1' : '0' );
		update_post_meta( $post_id, '_individuals_seo_enable_course', ! empty( $seo_data['enable_course'] ) ? '1' : '0' );
		update_post_meta( $post_id, '_individuals_seo_enable_faq', ! empty( $seo_data['enable_faq'] ) ? '1' : '0' );
		update_post_meta( $post_id, '_individuals_seo_enable_events', ! empty( $seo_data['enable_events'] ) ? '1' : '0' );
	}
}
add_action( 'save_post', 'thecrisisacademy_save_individuals_seo_metabox', 10, 2 );
