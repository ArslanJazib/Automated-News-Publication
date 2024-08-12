<?php
$hero_heading=get_field('hero_block_heading');
$hero_subheading=get_field('hero_block_sub_heading');
$hero_cta=get_field('hero_block_cta_button');
$hero_main_image=get_field('hero_block_main_image');
//$hero_background_image=get_field('hero_block_background_image');


?>
<section class="section-hero" id="home">
   <div class="container">
      <div class="row">
         <div class="col-12">
            <h1><?php if($hero_heading){echo $hero_heading;} ?></h1>
            <p><?php if($hero_subheading){echo $hero_subheading;} ?></p>

             <?php if( $hero_cta ): 
                    $link_url = $hero_cta['url'];
                    $link_title = $hero_cta['title'];
                    $link_target = $hero_cta['target'] ? $hero_cta['target'] : '_self';
            ?>
            <a class="started-btn" href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>"><?php echo esc_html( $link_title ); ?></a>
            <?php endif; ?>
            
            
            <div class="media">

                <?php if($hero_main_image ): ?>
                    <img src="<?php echo $hero_main_image;?>" alt="img"/>
                <?php endif; ?>

            </div>
         </div>
      </div>
   </div>
</section>