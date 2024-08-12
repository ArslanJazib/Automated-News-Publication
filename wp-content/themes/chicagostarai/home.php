<?php
/**
 * Template Name: Home
 */

get_header();
?>

	<main id="primary" class="site-main">
		

		<?php get_template_part( 'page-modules/hero-section/hero-section', 'page' );?>
		<?php get_template_part( 'page-modules/about-us/about-us', 'page' );?>
		<?php get_template_part( 'page-modules/services/services', 'page' );?>
		<?php get_template_part( 'page-modules/contact-us-module/contact-us', 'page' );?>

	</main><!-- #main -->
	
	
<?php
//get_sidebar();
get_footer();
?>

<script>
	 jQuery(document).ready(function($) {
        var textArea = $('textarea').attr('rows', '2');
    });
</script>
