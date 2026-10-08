<?php
/**
 * Corporate Page SEO & Structured Data Enhancements
 *
 * Provides dedicated SEO optimization for the Corporate landing page template:
 * - Automated Open Graph & Twitter Cards social metadata.
 * - Dynamic Schema.org JSON-LD graph (Course, EducationalOrganization, Person, FAQPage, Event, WebPage).
 * - Optimized document title generation.
 * - Native WordPress admin SEO metabox with live Google SERP preview.
 *
 * @package TheCrisisAcademy
 * @subpackage SEO
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Retrieve default fallback values for Corporate SEO fields
 *
 * @return array
 */
function thecrisisacademy_get_corporate_seo_defaults() {
	return array(
		'title'          => 'Programa In-Company: Comunicación y Manejo de Crisis | The Crisis Academy',
		'description'    => 'Entrenamiento ejecutivo y simulación inmersiva de crisis corporativas en 60 minutos. Metodología científica liderada por Carolina Eslava. Protege la reputación y continuidad de tu empresa.',
		'keywords'       => 'comunicación de crisis, manejo de crisis corporativa, simulación de crisis, gestión de crisis empresarial, vocería de crisis, reputación corporativa, Carolina Eslava',
		'og_image'       => '',
		'canonical'      => '',
		'enable_schema'  => '1',
		'enable_course'  => '1',
		'enable_faq'     => '1',
		'enable_events'  => '1',
	);
}

/**
 * Retrieve sanitized Corporate SEO data for a post with fallback to defaults
 *
 * @param int|null $post_id Post ID (defaults to current post)
 * @return array
 */
function thecrisisacademy_get_corporate_seo_data( $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	$defaults = thecrisisacademy_get_corporate_seo_defaults();

	if ( ! $post_id ) {
		return $defaults;
	}

	$title         = get_post_meta( $post_id, '_corporate_seo_title', true );
	$description   = get_post_meta( $post_id, '_corporate_seo_description', true );
	$keywords      = get_post_meta( $post_id, '_corporate_seo_keywords', true );
	$og_image      = get_post_meta( $post_id, '_corporate_seo_og_image', true );
	$canonical     = get_post_meta( $post_id, '_corporate_seo_canonical', true );
	$enable_schema = get_post_meta( $post_id, '_corporate_seo_enable_schema', true );
	$enable_course = get_post_meta( $post_id, '_corporate_seo_enable_course', true );
	$enable_faq    = get_post_meta( $post_id, '_corporate_seo_enable_faq', true );
	$enable_events = get_post_meta( $post_id, '_corporate_seo_enable_events', true );

	// Smart fallback for social image: hero canvas thumbnail, founder photo or site icon
	if ( empty( $og_image ) && function_exists( 'thecrisisacademy_get_founder_data' ) ) {
		$founder_data = thecrisisacademy_get_founder_data( $post_id );
		if ( ! empty( $founder_data['photo_url'] ) ) {
			$og_image = $founder_data['photo_url'];
		}
	}

	return array(
		'title'          => ! empty( $title ) ? $title : $defaults['title'],
		'description'    => ! empty( $description ) ? $description : $defaults['description'],
		'keywords'       => ! empty( $keywords ) ? $keywords : $defaults['keywords'],
		'og_image'       => ! empty( $og_image ) ? $og_image : $defaults['og_image'],
		'canonical'      => ! empty( $canonical ) ? $canonical : get_permalink( $post_id ),
		'enable_schema'  => '' !== $enable_schema && false !== $enable_schema ? $enable_schema : $defaults['enable_schema'],
		'enable_course'  => '' !== $enable_course && false !== $enable_course ? $enable_course : $defaults['enable_course'],
		'enable_faq'     => '' !== $enable_faq && false !== $enable_faq ? $enable_faq : $defaults['enable_faq'],
		'enable_events'  => '' !== $enable_events && false !== $enable_events ? $enable_events : $defaults['enable_events'],
	);
}

/**
 * Filter document title for Corporate page to maximize CTR and search relevance
 *
 * @param array $title_parts Title parts array
 * @return array
 */
function thecrisisacademy_corporate_filter_document_title( $title_parts ) {
	if ( is_page_template( 'templates/corporate.php' ) ) {
		$seo_data = thecrisisacademy_get_corporate_seo_data();
		if ( ! empty( $seo_data['title'] ) ) {
			$title_parts['title'] = $seo_data['title'];
			// Unset tagline or site to prevent redundant repetition if already in the custom title
			if ( strpos( $seo_data['title'], 'The Crisis Academy' ) !== false ) {
				unset( $title_parts['site'] );
				unset( $title_parts['tagline'] );
			}
		}
	}
	return $title_parts;
}
add_filter( 'document_title_parts', 'thecrisisacademy_corporate_filter_document_title', 20 );

/**
 * Check if a third-party SEO plugin is handling meta tags
 *
 * @return bool
 */
function thecrisisacademy_has_active_seo_plugin() {
	return defined( 'WPSEO_VERSION' ) // Yoast SEO
		|| defined( 'RANK_MATH_VERSION' ) // Rank Math
		|| defined( 'AIOSEO_VERSION' ) // All in One SEO
		|| defined( 'SEOPRESS_VERSION' ); // SEOPress
}

/**
 * Output SEO meta tags, Open Graph, and Twitter Cards into <head> for Corporate template
 */
function thecrisisacademy_corporate_seo_head() {
	if ( ! is_page_template( 'templates/corporate.php' ) ) {
		return;
	}

	$post_id   = get_the_ID();
	$seo_data  = thecrisisacademy_get_corporate_seo_data( $post_id );
	$site_name = get_bloginfo( 'name' );
	$page_url  = ! empty( $seo_data['canonical'] ) ? $seo_data['canonical'] : get_permalink( $post_id );

	// Output standard meta tags and Open Graph only if no 3rd-party SEO plugin is active
	if ( ! thecrisisacademy_has_active_seo_plugin() ) {
		?>
		<!-- The Crisis Academy • Corporate SEO Metadata -->
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
		<!-- End The Crisis Academy Corporate SEO Metadata -->
		<?php
	}

	// Output Schema.org Structured Data (JSON-LD)
	if ( '1' === $seo_data['enable_schema'] ) {
		thecrisisacademy_output_corporate_jsonld( $post_id, $seo_data );
	}
}
add_action( 'wp_head', 'thecrisisacademy_corporate_seo_head', 1 );

/**
 * Generate and output comprehensive Schema.org JSON-LD graph
 *
 * @param int   $post_id  Current post ID
 * @param array $seo_data Sanitized SEO data
 */
function thecrisisacademy_output_corporate_jsonld( $post_id, $seo_data ) {
	$home_url   = home_url( '/' );
	$page_url   = ! empty( $seo_data['canonical'] ) ? $seo_data['canonical'] : get_permalink( $post_id );
	$site_name  = get_bloginfo( 'name' );
	$founder_data = function_exists( 'thecrisisacademy_get_founder_data' ) ? thecrisisacademy_get_founder_data( $post_id ) : array();
	$cta_data     = function_exists( 'thecrisisacademy_get_cta_data' ) ? thecrisisacademy_get_cta_data( $post_id ) : array();

	$graph = array();

	// 1. EducationalOrganization / Organization Schema
	$org_schema = array(
		'@type'          => 'EducationalOrganization',
		'@id'            => $home_url . '#organization',
		'name'           => 'The Crisis Academy',
		'alternateName'  => 'Crisis Academy',
		'url'            => $home_url,
		'description'    => 'Academia especializada en entrenamiento directivo, simulación inmersiva y gestión estratégica de crisis corporativas.',
		'founder'        => array(
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
				'@type'      => 'ImageObject',
				'@id'        => $home_url . '#logo',
				'url'        => esc_url( $logo_url ),
				'caption'    => 'The Crisis Academy',
			);
		}
	}

	// Add ContactPoint from CTA WhatsApp info
	if ( ! empty( $cta_data['whatsapp_phone'] ) ) {
		$org_schema['contactPoint'] = array(
			'@type'             => 'ContactPoint',
			'telephone'         => sanitize_text_field( $cta_data['whatsapp_phone'] ),
			'contactType'       => 'corporate training sales',
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
		'description' => ! empty( $founder_data['quote'] ) ? wp_strip_all_tags( $founder_data['quote'] ) : 'Experta en gestión y comunicación de crisis corporativa con más de 2,000 ejecutivos entrenados bajo simulación activa.',
	);

	if ( ! empty( $founder_data['photo_url'] ) ) {
		$person_schema['image'] = esc_url( $founder_data['photo_url'] );
	}

	$graph[] = $person_schema;

	// 3. Course / EducationalOccupationalProgram Schema
	if ( '1' === $seo_data['enable_course'] ) {
		$hero_data = function_exists( 'thecrisisacademy_get_hero_data' ) ? thecrisisacademy_get_hero_data( $post_id ) : array();
		$program_name = ! empty( $hero_data['preheading'] ) ? $hero_data['preheading'] : 'Programa In-Company: Comunicación y Manejo de Crisis';

		$course_schema = array(
			'@type'                        => 'Course',
			'@id'                          => $page_url . '#course',
			'name'                         => $program_name,
			'description'                  => wp_strip_all_tags( $seo_data['description'] ),
			'provider'                     => array(
				'@id' => $home_url . '#organization',
			),
			'instructor'                   => array(
				'@id' => $home_url . '#founder',
			),
			'inLanguage'                   => 'es',
			'courseMode'                   => 'blended',
			'educationalCredentialAwarded' => 'Certificación In-Company en Gestión y Comunicación de Crisis',
			'timeRequired'                 => 'PT60M',
			'audience'                     => array(
				'@type'        => 'Audience',
				'audienceType' => 'Comités directivos, CEOs, directores de comunicación, RRHH y operaciones',
			),
			'hasCourseInstance'            => array(
				array(
					'@type'          => 'CourseInstance',
					'courseMode'     => 'blended',
					'courseWorkload' => 'Simulación de crisis activa y taller directivo intensivo de respuesta en 60 minutos',
				),
			),
			'offers'                       => array(
				'@type'         => 'Offer',
				'category'      => 'In-Company Corporate Training',
				'availability'  => 'https://schema.org/InStock',
				'priceCurrency' => 'USD',
				'url'           => $page_url . '#cta',
			),
		);

		$graph[] = $course_schema;
	}

	// 4. FAQPage Schema
	if ( '1' === $seo_data['enable_faq'] ) {
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
	if ( '1' === $seo_data['enable_events'] ) {
		$events_schema = thecrisisacademy_get_events_schema_items();
		if ( ! empty( $events_schema ) ) {
			foreach ( $events_schema as $evt ) {
				$graph[] = $evt;
			}
		}
	}

	// 6. WebPage Schema
	$webpage_schema = array(
		'@type'          => 'WebPage',
		'@id'            => $page_url . '#webpage',
		'url'            => $page_url,
		'name'           => $seo_data['title'],
		'description'    => $seo_data['description'],
		'inLanguage'     => 'es',
		'isPartOf'       => array(
			'@type' => 'WebSite',
			'@id'   => $home_url . '#website',
			'url'   => $home_url,
			'name'  => $site_name,
		),
		'about'          => array(
			'@id' => $page_url . '#course',
		),
		'datePublished'  => get_the_date( 'c', $post_id ),
		'dateModified'   => get_the_modified_date( 'c', $post_id ),
		'breadcrumb'     => array(
			'@type'           => 'BreadcrumbList',
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
					'name'     => get_the_title( $post_id ),
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
 * Retrieve FAQ items formatted for Schema.org FAQPage
 *
 * @param int $post_id Corporate Page ID
 * @return array
 */
function thecrisisacademy_get_faq_schema_items( $post_id ) {
	$faq_data = function_exists( 'thecrisisacademy_get_faq_data' ) ? thecrisisacademy_get_faq_data( $post_id ) : array();

	$faq_query = new WP_Query( array(
		'post_type'      => 'faq',
		'post_status'    => 'publish',
		'posts_per_page' => ! empty( $faq_data['posts_per_page'] ) ? (int) $faq_data['posts_per_page'] : -1,
		'orderby'        => ! empty( $faq_data['orderby'] ) ? $faq_data['orderby'] : 'menu_order',
		'order'          => ! empty( $faq_data['order'] ) ? $faq_data['order'] : 'ASC',
		'no_found_rows'  => true,
	) );

	$items = array();

	if ( $faq_query->have_posts() ) {
		while ( $faq_query->have_posts() ) {
			$faq_query->the_post();
			$question = get_the_title();
			$answer   = wp_strip_all_tags( get_the_content() );

			if ( ! empty( $question ) && ! empty( $answer ) ) {
				$items[] = array(
					'@type'          => 'Question',
					'name'           => $question,
					'acceptedAnswer' => array(
						'@type' => 'Answer',
						'text'  => $answer,
					),
				);
			}
		}
		wp_reset_postdata();
	} elseif ( function_exists( 'thecrisisacademy_get_initial_faq_data' ) ) {
		$initial_faqs = thecrisisacademy_get_initial_faq_data();
		foreach ( $initial_faqs as $faq_item ) {
			if ( ! empty( $faq_item['question'] ) && ! empty( $faq_item['answer'] ) ) {
				$items[] = array(
					'@type'          => 'Question',
					'name'           => $faq_item['question'],
					'acceptedAnswer' => array(
						'@type' => 'Answer',
						'text'  => wp_strip_all_tags( $faq_item['answer'] ),
					),
				);
			}
		}
	}

	return $items;
}

/**
 * Retrieve upcoming events formatted for Schema.org Event
 *
 * @return array
 */
function thecrisisacademy_get_events_schema_items() {
	$today = current_time( 'Y-m-d' );

	$events_query = new WP_Query( array(
		'post_type'      => 'event',
		'post_status'    => 'publish',
		'posts_per_page' => 10,
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
		'no_found_rows'  => true,
	) );

	$events = array();

	if ( $events_query->have_posts() ) {
		while ( $events_query->have_posts() ) {
			$events_query->the_post();
			$evt_id   = get_the_ID();
			$evt_date = get_post_meta( $evt_id, '_event_date', true );
			$evt_time = get_post_meta( $evt_id, '_event_time', true );
			$evt_loc  = get_post_meta( $evt_id, '_event_location', true );
			$evt_link = get_post_meta( $evt_id, '_event_link', true );

			$start_iso = ! empty( $evt_date ) ? $evt_date . 'T09:00:00' : current_time( 'c' );
			$location_name = ! empty( $evt_loc ) ? $evt_loc : 'Campus Online - The Crisis Academy';

			$events[] = array(
				'@type'               => 'Event',
				'@id'                 => get_permalink( $evt_id ),
				'name'                => get_the_title(),
				'startDate'           => $start_iso,
				'eventStatus'         => 'https://schema.org/EventScheduled',
				'eventAttendanceMode' => 'https://schema.org/OnlineEventAttendanceMode',
				'location'            => array(
					'@type' => 'VirtualLocation',
					'name'  => $location_name,
					'url'   => ! empty( $evt_link ) ? esc_url( $evt_link ) : home_url( '/' ),
				),
				'organizer'           => array(
					'@type' => 'EducationalOrganization',
					'name'  => 'The Crisis Academy',
					'url'   => home_url( '/' ),
				),
			);
		}
		wp_reset_postdata();
	}

	return $events;
}

/**
 * Register Corporate SEO Metabox in WordPress Admin
 */
function thecrisisacademy_register_corporate_seo_metabox() {
	if ( ! function_exists( 'add_meta_box' ) ) {
		return;
	}

	global $post;

	if ( ! $post ) {
		return;
	}

	$template = get_post_meta( $post->ID, '_wp_page_template', true );
	$is_front = ( (int) get_option( 'page_on_front' ) === $post->ID );

	if ( 'templates/corporate.php' !== $template && ! $is_front ) {
		return;
	}

	add_meta_box(
		'corporate_seo_metabox',
		'🔍 Optimización SEO y Redes Sociales (Open Graph, Twitter Cards y Schema.org)',
		'thecrisisacademy_render_corporate_seo_metabox',
		'page',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'thecrisisacademy_register_corporate_seo_metabox', 5 );

/**
 * Render Corporate SEO Metabox HTML in Page Editor
 *
 * @param WP_Post $post Current post object
 */
function thecrisisacademy_render_corporate_seo_metabox( $post ) {
	wp_nonce_field( 'corporate_seo_metabox_save', 'corporate_seo_nonce' );

	$seo = thecrisisacademy_get_corporate_seo_data( $post->ID );
	?>
	<div class="corporate-metabox-wrapper corporate-seo-metabox-wrap" style="padding: 15px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen-Sans, Ubuntu, Cantarell, sans-serif;">
		
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
						<div style="font-size: 12px; color: #4d5156; line-height: 1.2; word-break: break-all;" id="serp-preview-url"><?php echo esc_html( get_permalink( $post->ID ) ); ?></div>
					</div>
				</div>
				<h3 style="font-size: 20px; line-height: 1.3; color: #1a0dab; margin: 4px 0 6px; cursor: pointer; text-decoration: none;" id="serp-preview-title">
					<?php echo esc_html( $seo['title'] ); ?>
				</h3>
				<p style="font-size: 14px; line-height: 1.58; color: #4d5156; margin: 0;" id="serp-preview-desc">
					<?php echo esc_html( $seo['description'] ); ?>
				</p>
			</div>
		</div>

		<!-- SEO Fields Grid -->
		<div style="display: grid; grid-template-columns: 1fr; gap: 20px;">
			
			<!-- Field: SEO Title -->
			<div class="form-field">
				<label for="corporate_seo_title" style="display: flex; justify-content: space-between; font-weight: 600; margin-bottom: 6px; font-size: 14px;">
					<span>Título SEO (&lt;title&gt; tag y Open Graph Title)</span>
					<span id="corporate_seo_title_count" style="font-size: 12px; font-weight: normal; color: #646970;">0 / 60 caracteres</span>
				</label>
				<input type="text" id="corporate_seo_title" name="corporate_seo[title]" value="<?php echo esc_attr( $seo['title'] ); ?>" style="width: 100%; padding: 8px 12px; font-size: 14px; border: 1px solid #8c8f94; border-radius: 4px;" placeholder="Ej. Programa In-Company de Manejo de Crisis | The Crisis Academy" />
				<p style="margin: 4px 0 0; font-size: 12px; color: #646970;">Recomendado entre 50 y 60 caracteres para evitar que Google lo corte en resultados de búsqueda.</p>
			</div>

			<!-- Field: Meta Description -->
			<div class="form-field">
				<label for="corporate_seo_description" style="display: flex; justify-content: space-between; font-weight: 600; margin-bottom: 6px; font-size: 14px;">
					<span>Meta Descripción (&lt;meta name="description"&gt;)</span>
					<span id="corporate_seo_desc_count" style="font-size: 12px; font-weight: normal; color: #646970;">0 / 160 caracteres</span>
				</label>
				<textarea id="corporate_seo_description" name="corporate_seo[description]" rows="3" style="width: 100%; padding: 8px 12px; font-size: 14px; border: 1px solid #8c8f94; border-radius: 4px;" placeholder="Describe la propuesta de valor del programa corporate para invitar a hacer clic..."><?php echo esc_textarea( $seo['description'] ); ?></textarea>
				<p style="margin: 4px 0 0; font-size: 12px; color: #646970;">Recomendado entre 145 y 160 caracteres. Incluye un llamado a la acción persuasivo.</p>
			</div>

			<!-- Field: Focus Keywords -->
			<div class="form-field">
				<label for="corporate_seo_keywords" style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 14px;">
					Palabras Clave Principales (Separadas por comas)
				</label>
				<input type="text" id="corporate_seo_keywords" name="corporate_seo[keywords]" value="<?php echo esc_attr( $seo['keywords'] ); ?>" style="width: 100%; padding: 8px 12px; font-size: 14px; border: 1px solid #8c8f94; border-radius: 4px;" placeholder="comunicación de crisis, manejo de crisis, simulación de crisis..." />
			</div>

			<!-- Field: Open Graph Image -->
			<div class="form-field">
				<label for="corporate_seo_og_image" style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 14px;">
					Imagen para Redes Sociales (Open Graph / WhatsApp / LinkedIn / Twitter)
				</label>
				<div style="display: flex; gap: 10px; align-items: center;">
					<input type="text" id="corporate_seo_og_image" name="corporate_seo[og_image]" value="<?php echo esc_url( $seo['og_image'] ); ?>" style="flex: 1; padding: 8px 12px; font-size: 14px; border: 1px solid #8c8f94; border-radius: 4px;" placeholder="https://..." />
					<button type="button" class="button button-secondary" id="corporate_seo_upload_btn">Subir / Seleccionar Imagen</button>
				</div>
				<div id="corporate_seo_img_preview" style="margin-top: 10px; max-width: 250px;">
					<?php if ( ! empty( $seo['og_image'] ) ) : ?>
						<img src="<?php echo esc_url( $seo['og_image'] ); ?>" style="max-width: 100%; height: auto; border-radius: 4px; border: 1px solid #dcdcde;" alt="Preview" />
					<?php endif; ?>
				</div>
				<p style="margin: 4px 0 0; font-size: 12px; color: #646970;">Dimensiones recomendadas: 1200x630 píxeles para que se vea perfecta en WhatsApp, LinkedIn y Twitter.</p>
			</div>

			<!-- Field: Canonical URL Override -->
			<div class="form-field">
				<label for="corporate_seo_canonical" style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 14px;">
					URL Canónica (&lt;link rel="canonical"&gt;)
				</label>
				<input type="url" id="corporate_seo_canonical" name="corporate_seo[canonical]" value="<?php echo esc_attr( $seo['canonical'] ); ?>" style="width: 100%; padding: 8px 12px; font-size: 14px; border: 1px solid #8c8f94; border-radius: 4px;" placeholder="<?php echo esc_url( get_permalink( $post->ID ) ); ?>" />
				<p style="margin: 4px 0 0; font-size: 12px; color: #646970;">Déjalo vacío para usar la URL permanente de esta página automáticamente.</p>
			</div>

			<!-- Schema.org Toggles -->
			<div style="background: #f6f7f7; border: 1px solid #dcdcde; border-radius: 6px; padding: 15px; margin-top: 10px;">
				<h4 style="margin: 0 0 10px; font-size: 14px; font-weight: 600; color: #1d2327;">
					Datos Estructurados Rich Snippets (Schema.org JSON-LD)
				</h4>
				
				<label style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px; cursor: pointer;">
					<input type="checkbox" name="corporate_seo[enable_schema]" value="1" <?php checked( '1', $seo['enable_schema'] ); ?> />
					<span style="font-weight: 600;">Activar Gráfico Schema.org JSON-LD para esta página</span>
				</label>

				<div style="margin-left: 24px; display: flex; flex-direction: column; gap: 8px;">
					<label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
						<input type="checkbox" name="corporate_seo[enable_course]" value="1" <?php checked( '1', $seo['enable_course'] ); ?> />
						<span>Incluir Schema de Curso Ejecutivo / Capacitación In-Company (<code>Course</code>)</span>
					</label>

					<label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
						<input type="checkbox" name="corporate_seo[enable_faq]" value="1" <?php checked( '1', $seo['enable_faq'] ); ?> />
						<span>Incluir Schema de Preguntas Frecuentes enriquecidas (<code>FAQPage</code>)</span>
					</label>

					<label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
						<input type="checkbox" name="corporate_seo[enable_events]" value="1" <?php checked( '1', $seo['enable_events'] ); ?> />
						<span>Incluir Schema de Próximos Eventos programados (<code>Event</code>)</span>
					</label>
				</div>
			</div>

		</div>
	</div>

	<script>
	(function() {
		// Live counter and SERP preview script
		var titleInput = document.getElementById('corporate_seo_title');
		var descInput  = document.getElementById('corporate_seo_description');
		var titleCount = document.getElementById('corporate_seo_title_count');
		var descCount  = document.getElementById('corporate_seo_desc_count');
		var serpTitle  = document.getElementById('serp-preview-title');
		var serpDesc   = document.getElementById('serp-preview-desc');

		function updateCounters() {
			if (titleInput && titleCount && serpTitle) {
				var tLen = titleInput.value.length;
				titleCount.textContent = tLen + ' / 60 caracteres';
				titleCount.style.color = (tLen >= 40 && tLen <= 65) ? '#007017' : (tLen > 65 ? '#b32d2e' : '#646970');
				serpTitle.textContent = titleInput.value || 'Título SEO de la Página';
			}
			if (descInput && descCount && serpDesc) {
				var dLen = descInput.value.length;
				descCount.textContent = dLen + ' / 160 caracteres';
				descCount.style.color = (dLen >= 120 && dLen <= 165) ? '#007017' : (dLen > 165 ? '#b32d2e' : '#646970');
				serpDesc.textContent = descInput.value || 'Meta descripción del resultado en Google...';
			}
		}

		if (titleInput) titleInput.addEventListener('input', updateCounters);
		if (descInput) descInput.addEventListener('input', updateCounters);
		updateCounters();

		// Media Uploader for OG Image
		var uploadBtn = document.getElementById('corporate_seo_upload_btn');
		var imgInput  = document.getElementById('corporate_seo_og_image');
		var preview   = document.getElementById('corporate_seo_img_preview');

		if (uploadBtn && imgInput) {
			uploadBtn.addEventListener('click', function(e) {
				e.preventDefault();
				if (typeof wp !== 'undefined' && wp.media) {
					var frame = wp.media({
						title: 'Seleccionar Imagen para Redes Sociales',
						button: { text: 'Usar como Imagen Social' },
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
				}
			});
		}
	})();
	</script>
	<?php
}

/**
 * Save Corporate SEO Metabox Data
 *
 * @param int     $post_id Post ID
 * @param WP_Post $post    Post object
 */
function thecrisisacademy_save_corporate_seo_metabox( $post_id, $post = null ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! isset( $_POST['corporate_seo_nonce'] ) || ! wp_verify_nonce( $_POST['corporate_seo_nonce'], 'corporate_seo_metabox_save' ) ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['corporate_seo'] ) && is_array( $_POST['corporate_seo'] ) ) {
		$seo_data = $_POST['corporate_seo'];

		if ( isset( $seo_data['title'] ) ) {
			update_post_meta( $post_id, '_corporate_seo_title', sanitize_text_field( $seo_data['title'] ) );
		}
		if ( isset( $seo_data['description'] ) ) {
			update_post_meta( $post_id, '_corporate_seo_description', sanitize_textarea_field( $seo_data['description'] ) );
		}
		if ( isset( $seo_data['keywords'] ) ) {
			update_post_meta( $post_id, '_corporate_seo_keywords', sanitize_text_field( $seo_data['keywords'] ) );
		}
		if ( isset( $seo_data['og_image'] ) ) {
			update_post_meta( $post_id, '_corporate_seo_og_image', esc_url_raw( trim( $seo_data['og_image'] ) ) );
		}
		if ( isset( $seo_data['canonical'] ) ) {
			update_post_meta( $post_id, '_corporate_seo_canonical', esc_url_raw( trim( $seo_data['canonical'] ) ) );
		}

		update_post_meta( $post_id, '_corporate_seo_enable_schema', ! empty( $seo_data['enable_schema'] ) ? '1' : '0' );
		update_post_meta( $post_id, '_corporate_seo_enable_course', ! empty( $seo_data['enable_course'] ) ? '1' : '0' );
		update_post_meta( $post_id, '_corporate_seo_enable_faq', ! empty( $seo_data['enable_faq'] ) ? '1' : '0' );
		update_post_meta( $post_id, '_corporate_seo_enable_events', ! empty( $seo_data['enable_events'] ) ? '1' : '0' );
	}
}
add_action( 'save_post', 'thecrisisacademy_save_corporate_seo_metabox', 10, 2 );
