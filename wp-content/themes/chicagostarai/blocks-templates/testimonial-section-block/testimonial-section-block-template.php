<?php
$testimonial_section_title = get_field('testimonial_section_title');
$testimonial_section_heading = get_field('testimonial_section_heading');
$testimonial_section_subheading = get_field('testimonial_section_subheading');
$testimonial_count = get_field('total_number_of_testimonials');

if(!$testimonial_count){$testimonial_count=6;}
$args = array(
    'post_type' => 'testimonials',
    'post_status' => 'publish',
    'posts_per_page' => $testimonial_count
);
// The Query
$query = new WP_Query($args);
?>
<section class="section-testimonail">
   <div class="container">
      <div class="row">
         <div class="col-12">
            <div class="head">
               <h3><?php if($testimonial_section_title){echo $testimonial_section_title;} ?></h3>
               <h2><?php if($testimonial_section_heading){echo $testimonial_section_heading;} ?></h2>
               <p><?php if($testimonial_section_subheading){echo $testimonial_section_subheading;} ?></p>
            </div>
            <!-- slider -->
            <div class="sliders">
               <div id="carouselExampleControls" class="carousel slide" data-bs-ride="carousel">
                  <div class="carousel-inner">
                    <?php
                    if($query->have_posts()):
                        // The Loop
                        $count = 0; // Initialize iteration count
                        while ($query->have_posts()) : $query->the_post();
                           $id = get_the_id();
                           $testimonial_person_name = get_field('testimonial_person_name', $id);
                            $testimonial_person_designation = get_field('testimonial_person_designation', $id);
                            $testimonial_words = get_field('main_testimonial_sentence', $id);
                            $testimonial_icon = get_field('testimonial_image', $id);
                    
                            // Check if iteration is odd or even
                            if ($count % 2 == 0) {
                                // Add HTML for even iteration
                                ?>
                                <div class="carousel-item <?php if($count==0){echo 'active';} ?>"><div class="slide-inner">
                            <?php
                            }
                            ?>
                    
                            <div class="slide-card">
                                <h4><?php echo get_the_title(); ?></h4>
                                <p><?php if ($testimonial_words) {
                                        echo $testimonial_words;
                                    } ?></p>
                                <div class="slide-media">
                                    <div class="media">
                                        <?php if ($testimonial_icon) : ?>
                                            <img src="<?php echo $testimonial_icon; ?>" alt="img"/>
                                        <?php endif; ?>
                                    </div>
                                    <div class="slide-intro">
                                        <h5><?php if ($testimonial_person_name) {
                                                echo $testimonial_person_name;
                                            } ?></h5>
                                        <h6><?php if ($testimonial_person_designation) {
                                                echo $testimonial_person_designation;
                                            } ?></h6>
                                    </div>
                                </div>
                            </div>
                    
                            <?php
                            // Check if iteration is odd or even
                            if ($count % 2 != 0) {
                                // Add HTML for odd iteration
                                echo '</div></div>';
                            }
                            $count++; // Increment iteration count
                        endwhile;
                        wp_reset_query();
                    else:
                        echo '<div style="color:white;">Please Add Testimonials To Display Here!!</div>';
                    endif;
                    // Reset Query
                    wp_reset_query();
                    ?>
                  </div>
                  <button class="slide-btn carousel-control-prev" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="prev">
                     <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M7.1775 4.44727L2.625 8.99977L7.1775 13.5523" stroke="#C3D4E9" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M15.3749 9H2.75244" stroke="#C3D4E9" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                     </svg>
                  </button>
                  <button class="slide-btn  carousel-control-next" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="next">
                     <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10.8223 4.44727L15.3748 8.99977L10.8223 13.5523" stroke="white" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M2.625 9H15.2475" stroke="white" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                     </svg>
                  </button>
               </div>
            </div>
            <!-- slider -->
         </div>
      </div>
   </div>
</section>