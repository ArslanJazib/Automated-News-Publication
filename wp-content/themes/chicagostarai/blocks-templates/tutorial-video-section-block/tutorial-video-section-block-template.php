<?php
   $video_block_title = get_field('tutorial_video_block_title');
   $video_block_heading = get_field('tutorial_video_block_heading');
   $video_block_placeholder_image = get_field('tutorial_video_block_image_placeholder');
   $video_block_video_url = get_field('tutorial_video_block_video_url');
?>

<!-- tutorial section start here -->
<section class="section-tutorial">
   <div class="container">
      <div class="row">
         <div class="col-12">
            <div class="head">
               <h3><?php if($video_block_title){echo $video_block_title;} ?></h3>
               <h2><?php if($video_block_heading){echo $video_block_heading;} ?></h2>
            </div>

            <div class="mac-video">
               <a href="" class="" data-bs-toggle="modal" data-bs-target="#exampleModal">
                  <?php if($video_block_placeholder_image ): ?>
                     <img src="<?php echo $video_block_placeholder_image;?>" alt="img"/>
                  <?php endif; ?>
               </a>

               <!-- modal -->
               <!-- Button trigger modal -->
      
               <!-- Modal -->
               <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered">
                     <div class="modal-content">
                        <div class="embed-responsive embed-responsive-16by9">
                           <?php if($video_block_video_url ): ?>
                              <iframe class="embed-responsive-item" src="<?php echo $video_block_video_url;?>" allowfullscreen></iframe>
                           <?php endif; ?>
                        </div>
                     </div>
                  </div>
               </div>
               <!-- modal -->
            </div>
         </div>
      </div>
   </div>
</section>
<!--tutorial section end here  -->