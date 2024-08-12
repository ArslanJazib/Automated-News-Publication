<?php

/**
 * chicagostarai functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package chicagostarai
 */

if (!defined('_S_VERSION')) {
	// Replace the version number of the theme on each release.
	define('_S_VERSION', '1.0.0');
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
 
 

function chicagostarai_setup()
{
	/*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on chicagostarai, use a find and replace
		* to change 'chicagostarai' to the name of your theme in all the template files.
		*/
	load_theme_textdomain('chicagostarai', get_template_directory() . '/languages');

	// Add default posts and comments RSS feed links to head.
	add_theme_support('automatic-feed-links');

	/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
	add_theme_support('title-tag');

	/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
	add_theme_support('post-thumbnails');

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'menu-1' => esc_html__('Primary', 'chicagostarai'),
			'menu-2' => esc_html__('Secondary', 'chicagostarai'),
		)
	);

	/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'chicagostarai_custom_background_args',
			array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support('customize-selective-refresh-widgets');

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);
}
add_action('after_setup_theme', 'chicagostarai_setup');

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function chicagostarai_content_width()
{
	$GLOBALS['content_width'] = apply_filters('chicagostarai_content_width', 640);
}
add_action('after_setup_theme', 'chicagostarai_content_width', 0);

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function chicagostarai_widgets_init()
{
	register_sidebar(
		array(
			'name'          => esc_html__('Sidebar', 'chicagostarai'),
			'id'            => 'sidebar-1',
			'description'   => esc_html__('Add widgets here.', 'chicagostarai'),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action('widgets_init', 'chicagostarai_widgets_init');

/**
 * Enqueue scripts and styles.
 */
function chicagostarai_scripts()
{
	wp_enqueue_style('font-awesome-style', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css', array(), '6.4.2');
	wp_enqueue_style('bootstrap-style', get_template_directory_uri() . '/assets/css/bootstrap.min.css', array(), '5.0.2');
	wp_enqueue_style('slick-style', get_template_directory_uri() . '/assets/css/slick.css', array(), '1.8.1');
	wp_enqueue_style('chicago-star-ai-style', get_stylesheet_uri(), array(), _S_VERSION);
	wp_style_add_data('chicago-star-ai-style', 'rtl', 'replace');

	wp_enqueue_script('jquery');
	wp_enqueue_script('bootstrap', get_template_directory_uri() . '/js/bootstrap.bundle.min.js', array(), '5.0.2', true);
	wp_enqueue_script('slick', get_template_directory_uri() . '/js/slick.min.js', array(), '1.8.1', true);
	wp_enqueue_script('chicago-star-ai-navigation', get_template_directory_uri() . '/js/navigation.js', array(), _S_VERSION, true);
	wp_enqueue_script('sweet-alert', 'https://cdn.jsdelivr.net/npm/sweetalert2@11', array(), true);
	wp_enqueue_script('custom_js', get_template_directory_uri() . '/js/custom.js', array(), _S_VERSION, true);

	if (is_singular() && comments_open() && get_option('thread_comments')) {
		wp_enqueue_script('comment-reply');
	}
	wp_localize_script('custom_js', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
}
add_action('wp_enqueue_scripts', 'chicagostarai_scripts');

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if (defined('JETPACK__VERSION')) {
	require get_template_directory() . '/inc/jetpack.php';
}

include_once(ABSPATH . 'wp-content/plugins/rssfeedsfetcher/rssfeedsfetcher.php');
include_once(ABSPATH . 'wp-content/plugins/chatgptrephraser/chatgptrephraser.php');




// AJAX URL
add_action('wp_footer', 'ajax_var');
function ajax_var()
{
	echo '<script type="text/javascript">
          var ajaxurl = "' . admin_url('admin-ajax.php') . '";
          </script>';
}

include_once 'function-files/send-article-parameter.php';
include_once 'function-files/newsmaster.php';
include_once 'function-files/fetch-url.php';
include_once 'function-files/gmail-content.php';



add_action('acf/init', 'eenews_custom_acf_init');
function eenews_custom_acf_init()
{

	/*check function exists*/
	if (function_exists('acf_register_block')) {

		/* New redesign blocks*/

		acf_register_block(
			array(
				'name' => 'add_hero_block_template',
				'title' => __('Add Hero Block Template'),
				'description' => __('Chicago Star Add Hero Block Module'),
				'render_template' => '/blocks-templates/add-hero-block-template/hero-block-template.php',
				'enqueue_assets' => function () {
					// assetEnqueue('chicago-star-style', '/ACF-block-modules/add-prompt-dashboard-block/add-prompt-dashboard-block.css', true, false);
					//assetEnqueue('chicago-star-script', '/blocks-templates/add-email-template-block/add-email-template-block.js', true, false);
				},

				'category' => 'blocks',
				'icon' => 'welcome-add-page',
				'keywords' => array('HeroBlock', 'Hero', ''),
				'multiple' => true,
				'mode' => 'edit',
			)
		);

		acf_register_block(
			array(
				'name' => 'add_timeline_section_block',
				'title' => __('Add Timeline Section Block Template'),
				'description' => __('Chicago Star Add Timeline Section Block Module'),
				'render_template' => '/blocks-templates/step-timeline-section-block/timeline-section-block-template.php',
				'enqueue_assets' => function () {
					// assetEnqueue('chicago-star-style', '/ACF-block-modules/add-prompt-dashboard-block/add-prompt-dashboard-block.css', true, false);
					//assetEnqueue('chicago-star-script', '/blocks-templates/add-email-template-block/add-email-template-block.js', true, false);
				},

				'category' => 'blocks',
				'icon' => 'welcome-add-page',
				'keywords' => array('TimelineBlock', 'Timeline', ''),
				'multiple' => true,
				'mode' => 'edit',
			)
		);

		acf_register_block(
			array(
				'name' => 'add_services_section_block',
				'title' => __('Add Services Section Block Template'),
				'description' => __('Chicago Star Add Services Section Block Module'),
				'render_template' => '/blocks-templates/services-features-section-block/services-section-block-template.php',
				'enqueue_assets' => function () {
					// assetEnqueue('chicago-star-style', '/ACF-block-modules/add-prompt-dashboard-block/add-prompt-dashboard-block.css', true, false);
					//assetEnqueue('chicago-star-script', '/blocks-templates/add-email-template-block/add-email-template-block.js', true, false);
				},

				'category' => 'blocks',
				'icon' => 'welcome-add-page',
				'keywords' => array('ServicesBlock', 'Services', ''),
				'multiple' => true,
				'mode' => 'edit',
			)
		);

		acf_register_block(
			array(
				'name' => 'add_home_about_section_block',
				'title' => __('Add Home About Section Block Template'),
				'description' => __('Chicago Star Add About Section Block Module'),
				'render_template' => '/blocks-templates/home-about-section-block/about-section-block-template.php',
				'enqueue_assets' => function () {
					// assetEnqueue('chicago-star-style', '/ACF-block-modules/add-prompt-dashboard-block/add-prompt-dashboard-block.css', true, false);
					//assetEnqueue('chicago-star-script', '/blocks-templates/add-email-template-block/add-email-template-block.js', true, false);
				},

				'category' => 'blocks',
				'icon' => 'welcome-add-page',
				'keywords' => array('AboutBlock', 'about', ''),
				'multiple' => true,
				'mode' => 'edit',
			)
		);

		acf_register_block(
			array(
				'name' => 'add_testimonial_section_block',
				'title' => __('Add Testimonial Block Template'),
				'description' => __('Chicago Star Add Testimonial Section Block Module'),
				'render_template' => '/blocks-templates/testimonial-section-block/testimonial-section-block-template.php',
				'enqueue_assets' => function () {
					// assetEnqueue('chicago-star-style', '/ACF-block-modules/add-prompt-dashboard-block/add-prompt-dashboard-block.css', true, false);
					//assetEnqueue('chicago-star-script', '/blocks-templates/add-email-template-block/add-email-template-block.js', true, false);
				},

				'category' => 'blocks',
				'icon' => 'welcome-add-page',
				'keywords' => array('TestimonialBlock', 'testimonials', ''),
				'multiple' => true,
				'mode' => 'edit',
			)
		);

		acf_register_block(
			array(
				'name' => 'add_home_video_section_block',
				'title' => __('Add Tutorial Video Block Template'),
				'description' => __('Chicago Star Add Tutorial Video Section Block Module'),
				'render_template' => '/blocks-templates/tutorial-video-section-block/tutorial-video-section-block-template.php',
				'enqueue_assets' => function () {
					// assetEnqueue('chicago-star-style', '/ACF-block-modules/add-prompt-dashboard-block/add-prompt-dashboard-block.css', true, false);
					//assetEnqueue('chicago-star-script', '/blocks-templates/add-email-template-block/add-email-template-block.js', true, false);
				},

				'category' => 'blocks',
				'icon' => 'welcome-add-page',
				'keywords' => array('TutorialBlock', 'video', ''),
				'multiple' => true,
				'mode' => 'edit',
			)
		);

		acf_register_block(
			array(
				'name' => 'add_pricing_textarea_block',
				'title' => __('Add Pricing Textarea Block Template'),
				'description' => __('Chicago Star Add Pricing Textarea Section Block Module'),
				'render_template' => '/blocks-templates/pricing-textarea-section-block/pricing-textarea-section-block-template.php',
				'enqueue_assets' => function () {
					// assetEnqueue('chicago-star-style', '/ACF-block-modules/add-prompt-dashboard-block/add-prompt-dashboard-block.css', true, false);
					//assetEnqueue('chicago-star-script', '/blocks-templates/add-email-template-block/add-email-template-block.js', true, false);
				},

				'category' => 'blocks',
				'icon' => 'welcome-add-page',
				'keywords' => array('PricingCTABlock', 'pricingCTA', 'pricingSection'),
				'multiple' => true,
				'mode' => 'edit',
			)
		);

		acf_register_block(
			array(
				'name' => 'add_pricing_table_block',
				'title' => __('Add Pricing Table Block Template'),
				'description' => __('Chicago Star Add Pricing Options Table Block Module'),
				'render_template' => '/blocks-templates/pricing-options-table-block/pricing-options-table-block-template.php',
				'enqueue_assets' => function () {
					// assetEnqueue('chicago-star-style', '/ACF-block-modules/add-prompt-dashboard-block/add-prompt-dashboard-block.css', true, false);
					//assetEnqueue('chicago-star-script', '/blocks-templates/add-email-template-block/add-email-template-block.js', true, false);
				},

				'category' => 'blocks',
				'icon' => 'welcome-add-page',
				'keywords' => array('PricingBlock', 'pricing', 'PricingTable'),
				'multiple' => true,
				'mode' => 'edit',
			)
		);

		acf_register_block(
			array(
				'name' => 'add_blog_posts_grid_block',
				'title' => __('Add Blog Posts Grid Block Template'),
				'description' => __('Chicago Star Add Blog Posts Grid Block Module'),
				'render_template' => '/blocks-templates/blog-posts-grid-block/blog-posts-grid-block-template.php',
				'enqueue_assets' => function () {
					// assetEnqueue('chicago-star-style', '/ACF-block-modules/add-prompt-dashboard-block/add-prompt-dashboard-block.css', true, false);
					//assetEnqueue('chicago-star-script', '/blocks-templates/add-email-template-block/add-email-template-block.js', true, false);
				},

				'category' => 'blocks',
				'icon' => 'welcome-add-page',
				'keywords' => array('BlogGrid', 'blog', 'BlogPosts', 'BlogBlock'),
				'multiple' => true,
				'mode' => 'edit',
			)
		);

		acf_register_block(
			array(
				'name' => 'add_url_block',
				'title' => __('Add Url Block'),
				'description' => __('Chicago Star Add URL Block Module'),
				'render_template' => '/blocks-templates/add-email-template-block/add-email-template-block.php',
				'enqueue_assets' => function () {
					// assetEnqueue('chicago-star-style', '/ACF-block-modules/add-prompt-dashboard-block/add-prompt-dashboard-block.css', true, false);
					assetEnqueue('chicago-star-script', '/blocks-templates/add-email-template-block/add-email-template-block.js', true, false);
				},

				'category' => 'blocks',
				'icon' => 'welcome-add-page',
				'keywords' => array('UrlBlock', 'Url', ''),
				'multiple' => true,
				'mode' => 'edit',
			)
		);

		acf_register_block(
			array(
				'name' => 'add_source_url_template_block',
				'title' => __('Add Source Url Template Block'),
				'description' => __('Chicago Star Add Source Block Module'),
				'render_template' => '/blocks-templates/add-source-url-template-block/add-source-url-template-block.php',
				'enqueue_assets' => function () {
					// assetEnqueue('chicago-star-style', '/ACF-block-modules/add-prompt-dashboard-block/add-prompt-dashboard-block.css', true, false);
					assetEnqueue('chicago-star-script', '/blocks-templates/add-source-url-template-block/add-source-url-template-block.js', true, false);
				},

				'category' => 'blocks',
				'icon' => 'welcome-add-page',
				'keywords' => array('SourceBlock', 'Source', ''),
				'multiple' => true,
				'mode' => 'edit',
			)
		);

		acf_register_block(
			array(
				'name' => 'add-prompt-template-block',
				'title' => __('Add Prompt Template Block'),
				'description' => __('Chicago Star Add Prompt Block Module'),
				'render_template' => '/blocks-templates/add-prompt-template-block/add-prompt-template-block.php',
				'enqueue_assets' => function () {
					// assetEnqueue('chicago-star-style', '/ACF-block-modules/add-prompt-dashboard-block/add-prompt-dashboard-block.css', true, false);
					assetEnqueue('chicago-star-script', '/blocks-templates/add-prompt-template-block/add-prompt-template-block.js', true, false);
				},

				'category' => 'blocks',
				'icon' => 'welcome-add-page',
				'keywords' => array('PromptBlock', 'Prompt', ''),
				'multiple' => true,
				'mode' => 'edit',
			)
		);

		acf_register_block(
			array(
				'name' => 'instructions-template-block',
				'title' => __('Add Instruction Block'),
				'description' => __('Chicago Star Instruction Block Module'),
				'render_template' => '/blocks-templates/instructions-template-block/instructions-template-block.php',
				'enqueue_assets' => function () {
					// assetEnqueue('chicago-star-style', '/ACF-block-modules/add-prompt-dashboard-block/add-prompt-dashboard-block.css', true, false);
					assetEnqueue('chicago-star-script', '/blocks-templates/instructions-template-block/instructions-template-block.js', true, false);
				},

				'category' => 'blocks',
				'icon' => 'welcome-add-page',
				'keywords' => array('instructionTemplate', 'Instruction', ''),
				'multiple' => true,
				'mode' => 'edit',
			)
		);

		acf_register_block(
			array(
				'name' => 'module-title-template-block',
				'title' => __('Module Title Block'),
				'description' => __('Chicago Star Instruction Block Module'),
				'render_template' => '/blocks-templates/instructions-template-block/module-title-template-block.php',
				'enqueue_assets' => function () {
					// assetEnqueue('chicago-star-style', '/ACF-block-modules/add-prompt-dashboard-block/add-prompt-dashboard-block.css', true, false);
					assetEnqueue('chicago-star-script', '/blocks-templates/instructions-template-block/module-title-template-block.js', true, false);
				},

				'category' => 'blocks',
				'icon' => 'welcome-add-page',
				'keywords' => array('ModuleTemplate', 'Instruction', ''),
				'multiple' => true,
				'mode' => 'edit',
			)
		);

		acf_register_block(
			array(
				'name' => 'custom-form-button-block',
				'title' => __('Submit From Button Block'),
				'description' => __('Chicago Star Submit From Button Block'),
				'render_template' => '/blocks-templates/custom-form-button-block/custom-form-button-block.php',
				'enqueue_assets' => function () {
					// assetEnqueue('chicago-star-style', '/ACF-block-modules/add-prompt-dashboard-block/add-prompt-dashboard-block.css', true, false);
					assetEnqueue('chicago-star-script', '/blocks-templates/custom-form-button-block/custom-form-button-block.js', true, false);
				},

				'category' => 'blocks',
				'icon' => 'welcome-add-page',
				'keywords' => array('SubmitFromTemplate', 'FormSubmission', ''),
				'multiple' => true,
				'mode' => 'edit',
			)
		);


		acf_register_block(
			array(
				'name' => 'artical-card-template-block',
				'title' => __('Articles History Block'),
				'description' => __('Chicago Star Article History Block'),
				'render_template' => '/blocks-templates/artical-card-template-block/artical-card-template-block.php',
				'enqueue_assets' => function () {
					// assetEnqueue('chicago-star-style', '/ACF-block-modules/add-prompt-dashboard-block/add-prompt-dashboard-block.css', true, false);
					// assetEnqueue('chicago-star-script', '/blocks-templates/artical-card-template-block/artical-card-template-block.js', true, false);
				},

				'category' => 'blocks',
				'icon' => 'welcome-add-page',
				'keywords' => array('ArticleHistoryTemplate', 'FetchingHistory', ''),
				'multiple' => true,
				'mode' => 'edit',
			)
		);


		acf_register_block(
			array(
				'name' => 'wrapper-block-template',
				'title' => __('Create Custom Article'),
				'description' => __('Create Custom Article Block'),
				'render_template' => '/blocks-templates/wrapper-block-template/wrapper-block-template.php',
				'enqueue_assets' => function () {
					// assetEnqueue('chicago-star-style', '/ACF-block-modules/add-prompt-dashboard-block/add-prompt-dashboard-block.css', true, false);
					// assetEnqueue('chicago-star-script', '/blocks-templates/wrapper-block-template/wrapper-block-template.js', true, false);
				},

				'category' => 'blocks',
				'icon' => 'welcome-add-page',
				'keywords' => array('CreateArticleTemplate', 'CreateArticle', ''),
				'multiple' => true,
				'mode' => 'edit',
			)
		);

		acf_register_block(
			array(
				'name' => 'wrapper-nwesmaster-template',
				'title' => __('News Master Block'),
				'description' => __('News Master Block'),
				'render_template' => '/blocks-templates/newsmaster-block-template/newsmaster-block-template.php',
				'enqueue_assets' => function () {
					// assetEnqueue('chicago-star-style', '/ACF-block-modules/add-prompt-dashboard-block/add-prompt-dashboard-block.css', true, false);
					// assetEnqueue('chicago-star-script', '/blocks-templates/newsmaster-block-template/newsmaster-block-template.js', true, false);
				},

				'category' => 'blocks',
				'icon' => 'welcome-add-page',
				'keywords' => array('Newsmasterblock', 'Newsmaster', ''),
				'multiple' => true,
				'mode' => 'edit',
			)
		);

		acf_register_block(
			array(
				'name' => 'fetch-url-template',
				'title' => __('Fetch URL Block'),
				'description' => __('Fetch URL Block'),
				'render_template' => '/blocks-templates/fetch-url-block/fetch-url-block.php',
				'enqueue_assets' => function () {
					// assetEnqueue('chicago-star-style', '/ACF-block-modules/add-prompt-dashboard-block/add-prompt-dashboard-block.css', true, false);
					// assetEnqueue('chicago-star-script', '/blocks-templates/fetch-url-block/fetch-url-block.js', true, false);
				},

				'category' => 'blocks',
				'icon' => 'welcome-add-page',
				'keywords' => array('Fetchurl', 'Fetchurl', ''),
				'multiple' => true,
				'mode' => 'edit',
			)
		);
	}
}

function assetEnqueue($handle, $src, $is_style = true, $is_admin = false)
{
	if ($is_style) {
		wp_enqueue_style($handle, get_template_directory_uri() . $src, array(), null);
	} else {
		wp_enqueue_script($handle, get_template_directory_uri() . $src, array(), null, true);
	}

	// Check if the script is enqueued before localizing
	if (wp_script_is($handle, 'enqueued')) {
		wp_localize_script($handle, 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
	} else {
		// Handle the error - you can log it or display a message
		error_log('Error: Script ' . $handle . ' not enqueued before trying to localize.');
	}
}

add_action('wp_ajax_my_custom_table_data', 'custom_posts_data_callback');
add_action('wp_ajax_nopriv_my_custom_table_data', 'custom_posts_data_callback');


$current_user_id = get_current_user_id();


function custom_posts_data_callback()
{
	global $current_user_id;
	// print_r($_POST);
	$params = $_POST;
	// Retrieve the necessary parameters from DataTables
	$post_type = $params['post_type'];
	$tax_term = $_POST['tax_term'];

	$draw = intval($params['draw']);
	$start = intval($params['start']);
	$length = intval($params['length']);
	$order = $params['order'][0];
	$search = $params['search']['value'];
	$post_cat = $params['post_cat'];
	$taxonomy = $params['taxanomy'];
	$OrderBy = $params['Order_By'];
	$OrderQuerry = $params['order'];
	$field = 'slug';
	$post_status = 'publish';

	$args = build_custom_query($post_type, $length, $start, $search, $post_cat, $taxonomy, $field, $OrderBy, $OrderQuerry, $post_status);
	$query = new WP_Query($args);
	$data = array();
	$total_records = $query->found_posts;
	if ($query->have_posts()) {
		$data_rows = '';
		while ($query->have_posts()) {
			$query->the_post();
			ob_start();
?>
			<div class="artical-wrapper">
				<div class="artical-content">
					<?php $post_id = get_the_ID(); ?>
					<h4>
						<?php echo get_the_title(); ?>
					</h4>
					<p>
						<?php
						$team_member_description = get_the_content();
						if (strlen($team_member_description) > 150) {

							$truncated_description = substr($team_member_description, 0, 150) . '...';
							$permalink = get_the_permalink($post_id);
							$team_member_description = $truncated_description;
						}
						echo strip_tags($team_member_description);
						?>
					</p>
					<div>
						<h6>Author :
							<?php echo get_the_author(); ?>
						</h6>
					</div>
				</div>
				<div class="artical-details"><span class="date">
						<?php echo get_the_date(); ?>
					</span>
					<div class="artical-controls">
						<?php
						if ($post_type == 'gmail_posts') {
							$gpt_id = get_field('gpt_post_ids', $post_id);

							if (empty($gpt_id[0])) { ?>

								<button class="btn btn-dashboard" id="send-to-gpt<?php echo $post_id; ?>" onclick="send_gmail_data_to_gpt(<?php echo $post_id; ?>)">Send To GPT</button>
							<?php
							} else {
								$default_wp_id = get_field('wp_post_id', $gpt_id[0]);
								$compareResults = get_field('compare_results', $gpt_id[0]);
							}
							?>
							<button class="btn btn-dashboard" id="sndtoblox-button<?php echo $post_id; ?>" <?php if (empty($default_wp_id) && !empty($gpt_id[0])) {
																												echo "style='display:block' onclick='create_wp_post_blox($gpt_id[0],$post_id)'";
																											} else {
																												echo 'style="display:none"';
																											} ?> type="button">Send to Blox
							</button>

							<button type="button" class="btn btn-dashboard" <?php if (empty($compareResults) && !empty($gpt_id[0])) {
																				echo "style='display:block' onclick='compare_orginal_gpt_content_gmail($gpt_id[0], $post_id)'";
																			} else {
																				echo 'style="display:none"';
																			} ?> id="Comparison_button<?php echo $post_id; ?>">COMPARE
							</button>
							<?php
						} else if ($post_type == 'gpt_posts') {

							if ($post_cat === 'from-fetch-from-url') {

								$term_name_to_check = 'From Fetch from URL'; // Replace with the term name you want to check

								// Check if the post has the specified term in the given taxonomy
								$has_term = has_term($term_name_to_check, $taxonomy, $post_id);

								// Take action based on whether the term was found or not
								if ($has_term) {

									$default_wp_id = get_field('wp_post_id', $post_id);
							?>

									<?php

									$block_test_user = get_field('block_test_user', 'option');

									if ($block_test_user) {
										$blocked_user_ids = array();
										foreach ($block_test_user as $user_id) {
											$blocked_user_ids[] = $user_id;
										}
									}

									if (in_array($current_user_id, $blocked_user_ids)) {

									?>
										<button class="btn btn-dashboard" id="sndtoblox-button<?php echo $post_id; ?>" <?php if (empty($default_wp_id)) {
																															echo "style='display:block' onclick='create_wp_post_blox($post_id,$post_id)'";
																														} else {
																															echo 'style="display:none"';
																														} ?> type="button" disabled>Send to Blox
										</button>
									<?php } else { ?>
										<button class="btn btn-dashboard" id="sndtoblox-button<?php echo $post_id; ?>" <?php if (empty($default_wp_id)) {
																															echo "style='display:block' onclick='create_wp_post_blox($post_id,$post_id)'";
																														} else {
																															echo 'style="display:none"';
																														} ?> type="button">Send to Blox
										</button>
									<?php } ?>
									<?php
									$compareResults = get_field('compare_results', $post_id);

									$compare_results_similarities = get_field('compare_results_similarities', $post_id);
									?>
									<button type="button" class="btn btn-dashboard" <?php if (empty($compareResults) && !empty($post_id)) {
																						echo "style='display:block' onclick='compare_orginal_gpt_content($post_id)'";
																					} else {
																						echo 'style="display:none"';
																					} ?> id="Comparison_button<?php echo $post_id; ?>">COMPARE
									</button>
								<?php

								}
							} else if ($post_cat === 'save-article-as-sample') {

								$term_name_to_check_one = 'Save Article As Sample';
								$term_name_to_check_two = 'From Fetch from URL';
								$term_name_to_check_three = 'From Newsmaster';   

								// Check if the post has the specified term in the given taxonomy
								$has_term_one = has_term($term_name_to_check_one, $taxonomy, $post_id);
								$has_term_two = has_term($term_name_to_check_two, $taxonomy, $post_id);
								$has_term_three = has_term($term_name_to_check_three, $taxonomy, $post_id);

								if($has_term_one && $has_term_two ) {

									$default_wp_id = get_field('wp_post_id', $post_id);
									$base_url = get_home_url() . "/?post_id=";
									$dynamic_url = esc_url($base_url . $post_id);

									?>
										<a href="<?php echo $dynamic_url; ?>" class="btn btn-dashboard" id="editDraftButton">View Input</a>
									<?php
								} else if($has_term_one && $has_term_three ) {
									$default_wp_id = get_field('wp_post_id', $post_id);
                                    $base_url = get_home_url() . "/chicago-star-ai/?post_id=";
                                    $dynamic_url = esc_url($base_url . $post_id);
									?>
										<a href="<?php echo $dynamic_url; ?>" class="btn btn-dashboard" id="editDraftButton">View Input</a>
									<?php
								}


							} else if ($post_cat === 'from-newsmaster') {

								$term_name_to_check = 'From Newsmaster'; // Replace with the term name you want to check

								// Check if the post has the specified term in the given taxonomy
								$has_term = has_term($term_name_to_check, $taxonomy, $post_id);

								// Take action based on whether the term was found or not
								if ($has_term) {

									$default_wp_id = get_field('wp_post_id', $post_id);
								?>

									<?php

									$block_test_user = get_field('block_test_user', 'option');

									if ($block_test_user) {
										$blocked_user_ids = array();
										foreach ($block_test_user as $user_id) {
											$blocked_user_ids[] = $user_id;
										}
									}

									if (in_array($current_user_id, $blocked_user_ids)) {

									?>
										<button class="btn btn-dashboard" id="sndtoblox-button<?php echo $post_id; ?>" <?php if (empty($default_wp_id)) {
																															echo "style='display:block' onclick='create_wp_post_blox($post_id,$post_id)'";
																														} else {
																															echo 'style="display:none"';
																														} ?> type="button" disabled>Send to Blox
										</button>
									<?php } else { ?>
										<button class="btn btn-dashboard" id="sndtoblox-button<?php echo $post_id; ?>" <?php if (empty($default_wp_id)) {
																															echo "style='display:block' onclick='create_wp_post_blox($post_id,$post_id)'";
																														} else {
																															echo 'style="display:none"';
																														} ?> type="button">Send to Blox
										</button>
									<?php } ?>
									<?php
									$compareResults = get_field('compare_results', $post_id);

									$compare_results_similarities = get_field('compare_results_similarities', $post_id);
									?>
									<button type="button" class="btn btn-dashboard" <?php if (empty($compareResults) && !empty($post_id)) {
																						echo "style='display:block' onclick='compare_orginal_gpt_content($post_id)'";
																					} else {
																						echo 'style="display:none"';
																					} ?> id="Comparison_button<?php echo $post_id; ?>">COMPARE
									</button>
								<?php
								}
							} else {

								$term_name_to_check_one = 'Save Archive Articles As Draft';
								$term_name_to_check_two = 'From Fetch from URL';
								$term_name_to_check_three = 'From Newsmaster';   

								// Check if the post has the specified term in the given taxonomy
								$has_term_one = has_term($term_name_to_check_one, $taxonomy, $post_id);
								$has_term_two = has_term($term_name_to_check_two, $taxonomy, $post_id);
								$has_term_three = has_term($term_name_to_check_three, $taxonomy, $post_id);

								if($has_term_one && $has_term_two ) {

									$default_wp_id = get_field('wp_post_id', $post_id);
									$base_url = get_home_url() . "/?post_id=";
									$dynamic_url = esc_url($base_url . $post_id);

									?>
										<a href="<?php echo $dynamic_url; ?>" class="btn btn-dashboard" id="editDraftButton">Edit Draft</a>
									<?php
								} else if($has_term_one && $has_term_three ) {
									$default_wp_id = get_field('wp_post_id', $post_id);
                                    $base_url = get_home_url() . "/chicago-star-ai/?post_id=";
                                    $dynamic_url = esc_url($base_url . $post_id);
									?>
										<a href="<?php echo $dynamic_url; ?>" class="btn btn-dashboard" id="editDraftButton">Edit Draft</a>
									<?php
								}
							}
						}
						?>

						<?php if ($post_cat === 'save-article-as-sample') { ?>
							<a target="_blank" href="<?php echo get_the_permalink($post_id) ?>" class="btn btn-dashboard">
								View Output
							</a>
						<?php } else if ($post_cat === 'save-archive-articles-as-draft') { ?>
							<a target="_blank" href="<?php echo get_the_permalink($post_id) ?>" class="btn btn-dashboard" style="display: none;">
								View Output
							</a>
						<?php	} else { ?>
							<a target="_blank" href="<?php echo get_the_permalink($post_id) ?>">
								<i class="fa-regular fa-eye"></i>
							</a>
						<?php } ?>

						<?php if (!current_user_can('administrator') && $post_cat === 'save-article-as-sample') { ?>
							<a href="<?php echo wp_nonce_url(admin_url('admin-post.php?action=delete_post&id=' . get_the_ID()), 'delete-post_' . get_the_ID()); ?>" onclick="return confirm('Are you sure you want to delete this post?');" style="display: none;">
								<i class="fa-regular fa-trash-can"></i>
							</a>
						<?php } else { ?>
							<a href="<?php echo wp_nonce_url(admin_url('admin-post.php?action=delete_post&id=' . get_the_ID()), 'delete-post_' . get_the_ID()); ?>" onclick="return confirm('Are you sure you want to delete this post?');">
								<i class="fa-regular fa-trash-can"></i>
							</a>
						<?php } ?>

					</div>
				</div>

			</div>
<?php



			$data_rows = ob_get_contents();
			ob_end_clean();
			$post_data = array(
				'content' => $data_rows,
				'date' => get_the_date(),
				'author' => get_the_author(),
			);
			$data[] = $post_data;
		}
		wp_reset_postdata();
		$response = array(
			'draw' => $draw,
			'recordsTotal' => $total_records,
			'recordsFiltered' => $total_records,
			// This assumes no filtering is applied, adjust as needed
			'data' => $data,
		);

		wp_send_json($response);

		wp_die();
	}
}
function build_custom_query($post_type = '', $length = -1, $start = 0, $search = '', $post_cat = '', $taxonomy = '', $field = 'slug', $orderby = '', $order = '', $post_status = 'publish') {
    $args = array(
        'post_type' => $post_type,
        'orderby' => $orderby,
        'order' => $order,
        'post_status' => $post_status,
    );

 
    if (is_user_logged_in() && !current_user_can('administrator')) {
        $current_user_id = get_current_user_id();
        if ($post_cat && $post_cat !== 'save-article-as-sample') {
            $args['author'] = $current_user_id;
        }
    }

    if ($length > 0) {
        $args['posts_per_page'] = $length;
    }
    if ($start > 0) {
        $args['offset'] = $start;
    }
    if ($search) {
        $args['s'] = $search;
    }

    $not_in_terms = array('save-archive-articles-as-draft', 'save-article-as-sample');
    if ($post_cat && in_array($post_cat, array('from-fetch-from-url', 'from-newsmaster'))) {
        $args['tax_query'] = array(
            'relation' => 'AND',
            array(
                'taxonomy' => $taxonomy,
                'field' => $field,
                'terms' => $post_cat,
            ),
            array(
                'taxonomy' => $taxonomy,
                'field' => $field,
                'terms' => $not_in_terms,
                'operator' => 'NOT IN'
            )
        );
    } else if ($post_cat) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => $taxonomy,
                'field' => $field,
                'terms' => $post_cat,
            ),
        );
    }
    return $args;
}



function acf_load_sample_field($field)
{
	$field['choices'] = get_post_type_choices();
	return $field;
}
add_filter('acf/load_field/name=history_post_type', 'acf_load_sample_field');
function get_post_type_choices()
{
	$args = array(
		'public' => true,
		'_builtin' => false
	);
	$post_types = get_post_types($args, 'objects');
	$choices = array();
	foreach ($post_types as $post_type) {
		$choices[$post_type->name] = $post_type->label;
	}

	return $choices;
}


function acf_load_gpt_categories_field($field)
{
	$field['choices'] = get_taxonomy_choices();
	return $field;
}
add_filter('acf/load_field/name=history_taxonomy', 'acf_load_gpt_categories_field');

function get_taxonomy_choices()
{

	$taxonomies = get_taxonomies(array('public' => true, '_builtin' => false), 'objects');
	$terms = array();

	if (!empty($taxonomies)) {
		foreach ($taxonomies as $taxonomy) {
			$terms[$taxonomy->name] = $taxonomy->label;
		}
	}

	return $terms;
}


function acf_load_gpt_categories_field_post($field)
{
	$taxonomy = get_field('history_taxonomy'); // Get the selected taxonomy from ACF field
	$field['choices'] = get_gpt_category_choices($taxonomy);
	return $field;
}
add_filter('acf/load_field/name=history_post_cat_category', 'acf_load_gpt_categories_field_post');

function get_gpt_category_choices($taxonomy)
{
	$terms = get_terms(
		array(
			'taxonomy' => $taxonomy,
			'hide_empty' => false,
		)
	);

	$choices = array();

	if (!empty($terms) && !is_wp_error($terms)) {
		foreach ($terms as $term) {
			$choices[$term->slug] = $term->name;
		}
	}

	return $choices;
}

function fetch_prompt_text()
{
	// Create an instance of the ChatGptRephraser class
	global $chat_gpt_rephraser;

	// Get the prompt
	$prompt_textarea = $chat_gpt_rephraser->get_prompt();

	// Update the ACF field with the prompt
	update_field('prompt_textarea', $prompt_textarea);
}

// Add this code to your theme's functions.php or a custom plugin

// function hide_textarea_input_box() {
//     echo '<style type="text/css">
//         .acf-field[data-name="prompt_textarea"] .acf-input textarea {
//             display: none;
//         }
//     </style>';
// }

// add_action('admin_head', 'hide_textarea_input_box');

// Add this code to your theme's functions.php or a custom plugin

function hide_textarea_input_box_and_title()
{
	echo '<style type="text/css">
        .acf-field[data-name="prompt_textarea"] {
            display: none;
        }
    </style>';
}

add_action('admin_head', 'hide_textarea_input_box_and_title');


function chat_gpt_model_fetch()
{
	// Create an instance of the ChatGptRephraser class
	global $chat_gpt_rephraser;

	// Get the prompt
	$gpt_model_options = $chat_gpt_rephraser->get_gpt_models();
}

// This hook automatically saves the ACF Changes in JSON form in the acf-json folder
function my_acf_json_save_point($path)
{
	return get_stylesheet_directory() . '/acf-json';
}
add_filter('acf/settings/save_json', 'my_acf_json_save_point');

// Functions to create and remove create custom pages with templates on theme activation and deactivation
function create_custom_pages_on_activation()
{
	// Check if the pages already exist to avoid duplicates
	$page_data = array(
		'Custom Articles' => 'chicago-template.php',
		'Custom Articles History' => 'chicago-template.php',
		'Fetch From URL' => 'chicago-template.php',
		'Fetch From URL History' => 'chicago-template.php',
		'Newsmaster' => 'chicago-template.php',
		'Newsmaster History' => 'chicago-template.php',
		'Automatically Fetched From URLS With Paywall' => 'chicago-template.php',
		'Automatically Fetched From URLS Without Paywall' => 'chicago-template.php',
		'Automatically Fetched Press Releases From GMAIL Inbox' => 'chicago-template.php',
	);

	foreach ($page_data as $title => $template) {
		$page_check = get_page_by_title($title);

		if (empty($page_check)) {
			// Page does not exist, so create it
			$page_id = wp_insert_post(array(
				'post_title'    => $title,
				'post_content'  => '',
				'post_status'   => 'publish',
				'post_type'     => 'page',
			));

			// Set the page template
			if ($page_id && !empty($template)) {
				update_post_meta($page_id, '_wp_page_template', $template);
			}

			// You can add additional customization for the newly created pages here
		}
	}
}

// Hook the function to the after_switch_theme action
add_action('after_switch_theme', 'create_custom_pages_on_activation');

// Function to remove custom pages
function remove_custom_pages_on_deactivation()
{
	$page_data = array(
		'Custom Articles' => 'chicago-template.php',
		'Custom Articles History' => 'chicago-template.php',
		'Fetch From URL' => 'chicago-template.php',
		'Fetch From URL History' => 'chicago-template.php',
		'Newsmaster' => 'chicago-template.php',
		'Newsmaster History' => 'chicago-template.php',
		'Automatically Fetched From URLS With Paywall' => 'chicago-template.php',
		'Automatically Fetched From URLS Without Paywall' => 'chicago-template.php',
		'Automatically Fetched Press Releases From GMAIL Inbox' => 'chicago-template.php',
	);

	foreach ($page_data as $title => $template) {
		$page = get_page_by_title($title);

		if (!empty($page)) {
			// Remove custom template association
			delete_post_meta($page->ID, '_wp_page_template');

			// Delete the page
			wp_delete_post($page->ID, true);
		}
	}
}

// Hook the function to the switch_theme action
add_action('switch_theme', 'remove_custom_pages_on_deactivation');

// add_filter('cron_schedules','custom_cron_schedules');
// function custom_cron_schedules($schedules){
// 	if(!isset($schedules["minutely"])){
// 		$schedules["minutely"] = array(
// 			'interval' => 60,
// 			'display' => __('Once every minute'));
// 	}
// 	return $schedules;
// }



// Add this action hook to handle the custom action
add_action('admin_post_delete_post', 'custom_delete_post');

function custom_delete_post()
{
	// Verify the nonce
	if (isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'delete-post_' . $_GET['id'])) {
		// Get the post ID
		$post_id = absint($_GET['id']);

		// Check if the user has permission to delete the post
		if (current_user_can('delete_post', $post_id)) {
			// Delete the post
			wp_delete_post($post_id, true);

			$redirect_url = wp_get_referer() ? wp_get_referer() : home_url();
			wp_safe_redirect($redirect_url);
			exit();
		}
	}

	// If nonce verification fails or user doesn't have permission, handle accordingly
	wp_die('Security check failed or insufficient permissions.');
}
add_filter('show_admin_bar', 'hide_admin_bar_for_non_admins');

function hide_admin_bar_for_non_admins($show) {
    if (!current_user_can('administrator')) {
        return false;
    }
    return $show;
}

// hide side bar tabs for non admins
function remove_menus_for_non_admins() {
    if (!current_user_can('manage_options')) {
        remove_menu_page('edit.php'); // Posts
        remove_menu_page('edit.php?post_type=page'); // Pages

        // Hide all custom post type menus
        global $wp_post_types;
        foreach ($wp_post_types as $post_type) {
            if (post_type_exists($post_type->name) && $post_type->name !== 'post' && $post_type->name !== 'page') {
                remove_menu_page('edit.php?post_type=' . $post_type->name);
            }
        }
    }
}
add_action('admin_menu', 'remove_menus_for_non_admins');


// redirect from restricted url
function redirect_non_admin_users() {
    if (!current_user_can('manage_options')) {
        wp_redirect(admin_url());
        exit;
    }
}
// For pages:
add_action('load-page.php', 'redirect_non_admin_users');

// For posts:
add_action('load-post.php', 'redirect_non_admin_users');

// For custom post types:
add_action('load-post-new.php', 'redirect_non_admin_users'); // Edit screen
add_action('load-edit.php', 'redirect_non_admin_users'); // Post list screen



// Edit GPT Post From Frontend
function update_editable_content() {

    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    $editable_content = isset($_POST['editable_content']) ? wp_kses_post($_POST['editable_content']) : '';

    if ($post_id && current_user_can('edit_post', $post_id)) {

$description = preg_replace('/<\/?span[^>]*>/', '', $editable_content);


		    wp_update_post(array('ID' => $post_id, 'post_content' => $description));
        wp_send_json_success('Content updated successfully');
    } else {
        wp_send_json_error('Error updating content');
    }
}

add_action('wp_ajax_update_editable_content', 'update_editable_content');


// Rephrase GPT Post 
function rephrase_gpt_content() {

	$status_Code = 0;
	$message = '';
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    $editable_content = isset($_POST['editable_content']) ? wp_kses_post($_POST['editable_content']) : '';
	$post_data = array();
	$post_data[] = $editable_content;
    if ($post_id) {

		global $chat_gpt_rephraser;
		$gpt_rephraser = $chat_gpt_rephraser->chatgpt_rephrasing_call($post_data);
		
		if(!empty($gpt_rephraser)){
			$message = 'Successfully generated rephrased Content. New Content updated';
			
			wp_update_post(array('ID' => $post_id, 'post_content' => $gpt_rephraser));			
			$status_Code = 200;
		}
		else{
			$status_Code = 404;
			$message = 'Error from Chatgpt';
		}
		
    } else {
        $message = 'Empty Post ID';
    }
	wp_send_json(['gpt_rephraser'=>$gpt_rephraser,'status_Code'=>$status_Code,'message'=>$message], JSON_FORCE_OBJECT);
}

add_action('wp_ajax_rephrase_gpt_content', 'rephrase_gpt_content');

function get_scrapped_data($custom_post_id)
{
	// Get scrapped post IDs
	$scrapped_ids = get_post_meta($custom_post_id, 'scraped_post_ids', true);
	$scrapped_data = '';
	if (!empty($scrapped_ids)) {

		$scrapped_data = array(
			'ids' => array(),
			'content' => array(),
		);
		// Check if $scrapped_ids is an array
		if (!is_array($scrapped_ids)) {
			// If not, convert it to an array
			$scrapped_ids = explode(',', $scrapped_ids);
			$scrapped_ids = array_filter($scrapped_ids, 'strlen');
		}

		$scrapped_data['ids'] = $scrapped_ids;
		// Get scrapped post content
		foreach ($scrapped_data['ids'] as $scrapped_id) {
			$scrapped_post = get_post($scrapped_id);

			if ($scrapped_post) {
				$post_data = array(
					'ID' => $scrapped_post->ID,
					'post_title' => $scrapped_post->post_title,
					'post_content' => $scrapped_post->post_content,
					'source_url' => get_field('_source_urls', $scrapped_post->ID),
					'_source_publish_date' => get_field('_source_publish_date', $scrapped_post->ID),

					// Add more fields as needed
				);

				$scrapped_data['content'][] = $post_data;
			}
		}
	}
	return $scrapped_data;
}

add_action('wp_ajax_update_post_content', 'update_post_content_callback');
add_action('wp_ajax_nopriv_update_post_content', 'update_post_content_callback');

function update_post_content_callback()
{
	if (isset($_POST['post_id']) && isset($_POST['updated_content'])) {
		$post_id = $_POST['post_id'];
		$updated_content = $_POST['updated_content'];

		$post_data = array(
			'ID' => $post_id,
			'post_content' => $updated_content,
		);

		wp_update_post($post_data);
	}

	wp_die();
}


add_action('wp_ajax_update_draft_content', 'update_draft_content_callback');
add_action('wp_ajax_nopriv_update_draft_content', 'update_draft_content_callback');

function update_draft_content_callback()
{
	if (isset($_POST['post_id']) || isset($_POST['scrape_post_title']) || isset($_POST['scrape_post_content']) || isset($_POST['scrape_post_urls']) || isset($_POST['updated_content']) || isset($_POST['custom_urls']) || isset($_POST['custom_titles']) || isset($_POST['custom_contents']) || isset($_POST['subject_value']) || isset($_POST['question_value']) || isset($_POST['prompt_value'])) {

		$post_id = $_POST['post_id'];

		$scrape_post_title = $_POST['scrape_post_title'];
		$scrape_post_content = $_POST['scrape_post_content'];
		$scrape_post_urls = $_POST['scrape_post_urls'];

		$custom_titles = $_POST['custom_titles'];
		$custom_contents = $_POST['custom_contents'];
		$custom_urls = $_POST['custom_urls'];

		$subject_value = $_POST['subject_value'];
		$question_value = $_POST['question_value'];
		$prompt_value = $_POST['prompt_value'];
 
		$updated_content = $_POST['updated_content'];

		$custom_post_id = get_field('custom_post_id', $post_id);

		$scrapped_data = get_scrapped_data($custom_post_id);

		if (!empty($scrapped_data)) {
			$scrapped_post_id = array_column($scrapped_data['content'], 'ID');
		}

		// Prepare post data to update
		$post_data = array(
			'ID' => $post_id,
			'post_content' => $updated_content,
		);

		// Update the post
		wp_update_post($post_data);
		update_post_meta($post_id, 'subject', $subject_value);
		update_post_meta($post_id, 'questions', $question_value);
		update_post_meta($post_id, 'used_prompt', $prompt_value);

		if ($scrapped_post_id && gettype($scrapped_post_id) == 'array') {

			foreach ($scrapped_post_id as $key => $id) {
				$post_data_extract = array(
					'ID' => $id,
					'post_title' => $scrape_post_title[$key],
					'post_content' => $scrape_post_content[$key],
				);

				wp_update_post($post_data_extract);
				update_post_meta($id, 'source_url', $scrape_post_urls[$key]);
			}
		}

		if ($custom_post_id) {

			$custom_urls_string = implode("," , $custom_urls);
			$custom_titles_string = implode("," , $custom_titles) ;
			$custom_contents_string = implode("," , $custom_contents) ;
			

			update_post_meta($custom_post_id, 'source_repeater_0_custom_source_url', $custom_urls_string);
			update_post_meta($custom_post_id, 'source_repeater_0_custom_source_title', $custom_titles_string);
			update_post_meta($custom_post_id, 'source_repeater_0_custom_source_content', $custom_contents_string);

		}
	}

	wp_die();
}


// Add new user role
function add_user_role() {
    add_role(
        'user',
        __('user'),
        array(
            // 'read' => true,
            // 'edit_posts' => true,
            // 'delete_posts' => true,
            // Add or remove capabilities as needed
        )
    );
}
add_action('init', 'add_user_role');

// Redirect 'user1' users away from the admin dashboard
// function redirect_user1_users_from_admin() {
//     if (is_admin() && !current_user_can('administrator')) {
//         $user = wp_get_current_user();
//         if (in_array('user1', $user->roles)) {
//             wp_redirect(home_url());
//             exit;
//         }
//     }
// }
// add_action('admin_init', 'redirect_user1_users_from_admin');





//Restrict users other than admin to login to wordpress dashboard
// add_action( 'init', 'blockusers_init' );
// function blockusers_init()
// {

// 	if (is_admin() && !current_user_can('administrator')) {
// 		wp_redirect(home_url());
// 		exit;
// 	}
// }
// blockusers_init();

function destroy_session_on_logout() {
    // Ensure the session is destroyed
    if (session_id()) {
        session_destroy();
    }
}
add_action('wp_logout', 'destroy_session_on_logout');