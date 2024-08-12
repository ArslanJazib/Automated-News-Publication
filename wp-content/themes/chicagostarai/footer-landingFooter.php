<?php

/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package chicagostarai
 */

//  get_template_part( 'template-parts/content', 'banner_cta' );
?>

<!-- footer section start here -->
<footer class="landing-footer">
    <div class="container">
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                <div class="media">
                    <img src="<?php echo get_template_directory_uri() . '/assets/images/footer-logo.png' ?>" alt="img">
                </div>
                <div class="about">
                    <p>The Future of AI Content Is Now
                        A one-click solution to creating original AI-generated content
                    </p>
                </div>
            </div>
            <div class="col-sm-12 col-md-6 col-lg-3 col-xl-3">
                <div class="links">
                    <h4>Links</h4>
                    <ul>
                        <li><a href="">Features</a></li>
                        <li><a href="">Testimonials</a></li>
                        <li><a href="">How It Works</a></li>
                        <li><a href="">Case Study</a></li>
                        <li><a href="">Artificial Intelligence</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-sm-12 col-md-6 col-lg-3 col-xl-3">
                <div class="links">
                    <h4>Company</h4>
                    <ul>
                        <li><a href="">About Us</a></li>
                        <li><a href="">Newsletters</a></li>
                        <li><a href="">Career</a></li>
                        <li><a href="">Contact Us</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-12">
                <div class="copy-write">
                    <div class="left-side">
                        <li><a href="">Privacy Policy</a></li>
                        <li><a href="">Terms & Conditions</a></li>
                        <li><a href="">Cookie Policy</a></li>
                    </div>
                    <p>Chicago Star</p>
                </div>
            </div>
        </div>
    </div>
</footer>
<!-- footer section end here -->


</div> <!-- Site div closing tag  -->
<?php wp_footer(); ?>

<script>
    //toggle classes on price page jquery
    jQuery(document).ready(function() {
        // Hide div by setting display to none
        jQuery("#flexSwitchCheckDefault").click(function() {
            jQuery(".new-price").toggle();
            jQuery(".updates-price").toggle();
            jQuery(".saved").toggle();

        });
    });
</script>
</body>

</html>