<?php

	/**
	 * 
	 * Template Name: Landing Page Template
	*/

	get_header('landingHeader');
?>

<main id="primary" class="site-main">

	<?php
		while (have_posts()) :
			the_post();
			the_content();
		endwhile; // End of the loop.
	?>

</main>

<?php
//get_sidebar();
get_footer('landingFooter');