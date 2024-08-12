<?php

/**
 * Template Name: Without Form Tags Template
 */

get_header();
?>

<?php
while(have_posts()):
    the_post(); ?>
    <div class="container-fluid gx-xl-4 gx-2">
        <div class="row dashboard-content g-xl-5 g-3 mb-5">
            <div class="col-lg-3 side-bar-nav">
                <div class="sidebar-toggler">
                    <svg xmlns="http://www.w3.org/2000/svg" width="26" height="19" viewBox="0 0 26 19" fill="none"
                        class="close">
                        <path
                            d="M10.5 13.4999V17.5799L1.5 9.49992L10.5 1.41992V5.49992C10.5 5.49992 24.67 4.74992 24.67 17.6699C20.25 10.4999 10.5 13.4999 10.5 13.4999Z"
                            stroke="#0832FF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M3.53003 11.5902L10.8 5.24023" stroke="#0832FF" stroke-miterlimit="10" />
                        <path d="M1.5 9.49992L2.88 10.6299L10.44 3.74992L10.5 1.41992L1.5 9.49992Z" fill="#0832FF" />
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" width="26" height="19" viewBox="0 0 26 19" fill="none"
                        class="open">
                        <path
                            d="M15.1699 13.08V17.16L24.1699 9.08L15.1699 1V5.08C15.1699 5.08 0.999922 4.33 0.999922 17.25C5.41992 10.08 15.1699 13.08 15.1699 13.08Z"
                            stroke="#0832FF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M22.1399 11.1703L14.8699 4.82031" stroke="#0832FF" stroke-miterlimit="10" />
                        <path d="M24.1699 9.08L22.7899 10.21L15.2299 3.33L15.1699 1L24.1699 9.08Z" fill="#0832FF" />
                    </svg>
                </div>
                <div class="sidebar-mob" style="position: sticky; top: 50px;">
                    <?php echo get_template_part('/sidebar-template/sidebar-template', 'page'); ?>
                </div>
            </div>
            <div class="col-lg-9">
                <?php get_template_part('/blocks-templates/module-title-template-block/module-title-template-block', 'page') ?>
                <div class="content-body">
                    <!-- <form id="custom-form"> -->
                    <?php the_content(); ?>
                    <!-- </form> -->

                </div>
            </div>
        </div>
    </div>

<?php endwhile; ?>
<?php
get_footer();
?>