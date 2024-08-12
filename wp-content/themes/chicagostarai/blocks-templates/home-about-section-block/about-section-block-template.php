<?php
$home_about_title = get_field('home_about_section_title');
$home_about_heading = get_field('home_about_section_heading');
$home_about_description = get_field('home_about_section_description');
$home_about_link = get_field('home_about_section_link');
$home_about_link_icon = get_field('home_about_section_link_icon');
$home_about_side_image = get_field('home_about_section_side_image');


?>
<section class="section-about-us" id="about">
   <div class="container">
      <div class="row">
         <div class="col-sm-12 col-md-12 col-lg-5 col-xl-5">
            <div class="about-us-left-side">
               <h3><?php if($home_about_title){echo $home_about_title;} ?></h3>
               <h2><?php if($home_about_heading){echo $home_about_heading;} ?></h2>
               <p><?php if($home_about_description){echo $home_about_description;} ?></p>

               <?php if( $home_about_link ): 
                    $link_url = $home_about_link['url'];
                    $link_title = $home_about_link['title'];
                    $link_target = $home_about_link['target'] ? $home_about_link['target'] : '_self';
                ?>
                <a href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>">
                   <?php echo esc_html( $link_title ); ?>
                   <?php if($home_about_link_icon ): ?>
                         <img src="<?php echo $home_about_link_icon;?>" alt="img"/>
                   <?php endif; ?>
                </a>
                <?php endif; ?>
            </div>
         </div>
         <div class="col-sm-12 col-md-12 col-lg-7 col-xl-7">
            <div class="about-us-right-side">
                   <?php if($home_about_side_image ): ?>
                         <img src="<?php echo $home_about_side_image;?>" alt="img"/>
                   <?php endif; ?>
            </div>
         </div>
      </div>
   </div>
</section>