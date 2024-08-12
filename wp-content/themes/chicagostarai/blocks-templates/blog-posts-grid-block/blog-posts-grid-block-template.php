<?php

$blog_posts_title = get_field('blog_posts_grid_block_title');
$blog_posts_heading = get_field('blog_posts_grid_block_heading');
$blog_posts_desc = get_field('blog_posts_grid_block_desc');
$blog_posts_count = get_field('blog_posts_grid_block_count');
$blog_post_link_title = get_field('blog_posts_grid_block_link_text');

if (!$blog_posts_count) {
   $blog_posts_count = 5;
}

$args = array(
   'post_type' => 'gpt_posts',
   'post_status' => 'publish',
   'posts_per_page' => $blog_posts_count,
   'tax_query' => array(
      array(
         'taxonomy' => 'gpt_categories',
         'field' => 'slug',
         'terms' => 'save-article-as-sample'
      )
   )
);


// The Query
$query = new WP_Query($args);

?>
<!-- blog posts grid section start here -->
<section class="section-services generated-articles">
   <div class="container ">
      <div class="row">
         <div class="col-12">
            <h3><?php if ($blog_posts_title) {
                     echo $blog_posts_title;
                  } ?> </h3>
            <h2><?php if ($blog_posts_heading) {
                     echo $blog_posts_heading;
                  } ?> </h2>
            <div class="para">
               <p><?php if ($blog_posts_desc) {
                     echo $blog_posts_desc;
                  } ?> </p>
            </div>
         </div>
         <div class="col-12">
            <div class="articles-wrapper">
               <?php
               if ($query->have_posts()) :
                  // The Loop
                  while ($query->have_posts()) : $query->the_post();
               ?>
                     <div class="articles-card">
                        <div class="description">
                           <h4><?php the_title(); ?></h4>
                           <p><?php the_excerpt(); ?></p>
                        </div>
                        <div class="read-btn">
                           <a href="<?php the_permalink(); ?>">
                              <?php if ($blog_post_link_title) {
                                 echo $blog_post_link_title;
                              } ?>
                              <span>
                                 <svg width="30" height="31" viewBox="0 0 30 31" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M0.30741 13.7984C-0.0393173 17.7615 1.15002 21.6954 3.61377 24.7348C6.07753 27.7741 9.61389 29.6699 13.4449 30.0051C17.2759 30.3402 21.0877 29.0873 24.0418 26.522C26.9959 23.9567 28.8503 20.289 29.1971 16.3259C29.5438 12.3628 28.3545 8.42886 25.8907 5.38952C23.4269 2.35018 19.8906 0.454397 16.0596 0.119227C12.2286 -0.215942 8.41673 1.03696 5.46263 3.6023C2.50852 6.16764 0.654137 9.83528 0.30741 13.7984ZM10.5096 19.9667C10.4185 20.0486 10.3127 20.111 10.1983 20.1503C10.0838 20.1896 9.96313 20.205 9.84316 20.1956C9.72319 20.1862 9.60638 20.1522 9.49954 20.0955C9.39269 20.0389 9.29796 19.9607 9.22087 19.8656C9.14377 19.7705 9.08586 19.6603 9.05051 19.5416C9.01516 19.4228 9.00307 19.2979 9.01496 19.1739C9.02686 19.05 9.06248 18.9297 9.11977 18.8199C9.17705 18.7101 9.25484 18.613 9.34861 18.5344L17.4137 11.5307L12.4158 11.0934C12.1764 11.0725 11.9553 10.954 11.8014 10.764C11.6474 10.5741 11.573 10.3282 11.5947 10.0805C11.6164 9.8328 11.7323 9.60357 11.9169 9.44324C12.1015 9.28291 12.3398 9.2046 12.5792 9.22555L19.7565 9.85348C19.9959 9.87443 20.217 9.99291 20.3709 10.1829C20.5249 10.3728 20.5993 10.6187 20.5776 10.8664L19.928 18.2912C19.9063 18.5389 19.7904 18.7681 19.6058 18.9284C19.4212 19.0887 19.1829 19.1671 18.9435 19.1461C18.7041 19.1252 18.483 19.0067 18.329 18.8167C18.1751 18.6268 18.1007 18.3809 18.1224 18.1332L18.5747 12.9629L10.5096 19.9667Z" fill="white"></path>
                                 </svg>
                              </span>
                           </a>
                        </div>
                     </div>
               <?php
                  endwhile;
                  wp_reset_query();
               else :
                  echo '<div style="color:white;">Please Add AI Posts To Display Here!!</div>';
               endif;
               // Reset Query
               wp_reset_query();
               ?>
            </div>
         </div>
      </div>
   </div>
</section>
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css" />
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
<script type="text/javascript">
   $(document).ready(function() {
      $('.articles-wrapper').slick({
         slidesToShow: 3,
         slidesToScroll: 1,
         autoplay: true,
         autoplaySpeed: 2000,
         arrows: true,
         responsive: [{
               breakpoint: 991,
               settings: {
                  slidesToShow: 2,
                  slidesToScroll: 1
               }
            },
            {
               breakpoint: 767,
               settings: {
                  slidesToShow: 1,
                  slidesToScroll: 1
               }
            }
         ]
      });
   });
</script>