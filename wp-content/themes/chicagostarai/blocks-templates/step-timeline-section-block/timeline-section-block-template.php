<?php
$timeline_subheading = get_field('timeline_section_subheading');
$timeline_heading = get_field('timeline_section_heading');
$timeline_rows = get_field('timeline_section_rows');
$timeline_image = get_field('timeline_section_side_image');
?>
<section class="section-step" id="howitworks">
   <div class="container">
      <div class="row">
         <div class="col-sm-12 col-md-12 col-lg-5 col-xl-5">
            <div class="main-media">
            <?php if($timeline_image ): ?>
                    <img src="<?php echo $timeline_image;?>" alt="img"/>
                <?php endif; ?>
            </div>
         </div>
         <div class="col-sm-12 col-md-12 col-lg-7 col-xl-7">
            <div class="step-right-side">
               <h3><?php if($timeline_subheading){echo $timeline_subheading;} ?></h3>
               <h2><?php if($timeline_heading){echo $timeline_heading;} ?></h2>
               <div class="timeline-content">
                  <ul class="timeline">

                     <?php 
                     if( $timeline_rows ) {
                        $index = 1;
                        foreach( $timeline_rows as $row ) {
                        $row_heading = $row['timeline_row_heading'];
                        $row_desc = $row['timeline_row_description'];       
                        ?>
                           <li class="timeline-item">
                           <div class="timeline-item-circle"><?php echo $index; ?></div>
                           <div class="timeline-item-content">
                              <h4><?php if($row_heading){echo esc_html( $row_heading );} ?></h4>
                              <p><?php if($row_desc){echo esc_html( $row_desc );} ?></p>
                           </div>
                        </li>
                        <?php
                        $index++; // Increment index counter for the next iteration
                        }
                        
                  }
                     ?>
      
                  </ul>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>