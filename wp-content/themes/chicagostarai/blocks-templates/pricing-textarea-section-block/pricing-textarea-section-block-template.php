<?php
$pricing_text_heading = get_field('pricing_section_cta_heading');
$pricing_text_desc = get_field('pricing_section_cta_subheading');
$pricing_section_link = get_field('pricing_section_cta_button_link');
?>
<!-- pricing section start here -->
<section class="section-plan" id="contact">
   <div class="container">
      <div class="row">
         <div class="col-12">
            <div class="inner">
               <h2><?php if($pricing_text_heading){echo $pricing_text_heading;} ?></h2>
               <p><?php if($pricing_text_desc){echo $pricing_text_desc;} ?></p>

               <?php if( $pricing_section_link ): 
                    $link_url = $pricing_section_link['url'];
                    $link_title = $pricing_section_link['title'];
                    $link_target = $pricing_section_link['target'] ? $pricing_section_link['target'] : '_self';
                ?>
                <a class="started-btn" href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>"><?php echo esc_html( $link_title ); ?></a>
                <?php endif; ?>
            </div>
         </div>
      </div>
   </div>
</section>
<!-- pricing section end here -->