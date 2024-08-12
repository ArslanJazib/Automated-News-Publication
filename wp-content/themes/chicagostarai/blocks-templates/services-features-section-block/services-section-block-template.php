<?php
// Collect Features Services Section Block Data
$services_section_title = get_field('features_services_section_title');
$services_section_heading = get_field('features_services_section_heading');
$services_section_subheading = get_field('features_services_section_subheading');
// Tabs
$services_section_repeater = get_field('services_section_tabs');
?>
<section class="section-services">
   <div class="container">
      <div class="col-sm-12">
         <h3><?php if ($services_section_title) echo $services_section_title; ?></h3>
         <h2><?php if ($services_section_heading) echo $services_section_heading; ?></h2>
         <div class="para">
            <p><?php if ($services_section_subheading) echo $services_section_subheading; ?></p>
         </div>
         <div class="service-tabs">
            <ul class="nav nav-tabs" id="ServicesTabs" role="tablist">
               <?php
               if ($services_section_repeater) {
                  $index = 1;
                  foreach ($services_section_repeater as $tab) {
                     $service_title = $tab['services_section_tab_title'];
                     $svg_src = $tab['services_section_tab_svgicon'];
               ?>
                     <li class="nav-item" role="presentation">
                        <button class="nav-link <?php echo ($index === 1) ? 'active' : ''; ?>" id="service-tab<?php echo $index; ?>" data-bs-toggle="tab" data-bs-target="#service-tab-content<?php echo $index; ?>" type="button" role="tab" aria-controls="service-tab-content<?php echo $index; ?>" aria-selected="<?php echo ($index === 1) ? 'true' : 'false'; ?>">
                           <span class="tabs-vector">
                              <?php if ($svg_src) : ?>
                                 <img class="svg-vecctor" src="<?php echo esc_html($svg_src); ?>" alt="img" />
                              <?php endif; ?>
                           </span>
                           <?php if ($service_title) echo esc_html($service_title); ?>
                        </button>
                     </li>
               <?php
                     $index++;
                  }
               }
               ?>
            </ul>
            <!-- tabs body -->
            <!-- <div class="tab-content" id="ServicesTabsContent">
               <?php
               if ($services_section_repeater) {
                  $index = 1;
                  foreach ($services_section_repeater as $tab) {
                     $service_heading = $tab['services_section_tab_heading'];
                     $service_description = $tab['services_section_tab_description'];
                     $service_side_image = $tab['services_section_tab_side_image'];
               ?>
                     <div class="tab-pane fade <?php echo ($index === 1) ? 'show active' : ''; ?>" id="service-tab-content<?php echo $index; ?>" role="tabpanel" aria-labelledby="service-tab<?php echo $index; ?>">
                        <div class="tab-body-inner">
                           <div class="media">
                              <img src="<?php echo esc_html($service_side_image); ?>">
                           </div>
                           <div class="tabs-description">
                              <h3><?php echo ($service_heading) ? $service_heading : ''; ?></h3>
                              <p><?php echo ($service_description) ? $service_description : ''; ?></p>
                           </div>
                        </div>
                     </div>
               <?php
                     $index++;
                  }
               }
               ?>
            </div> -->
            <!-- tabs body -->
         </div>
      </div>
   </div>
</section>
<script>
   const ulElement = document.getElementById('ServicesTabs');

   function toggleExpandedClass(event) {
      if (event.target.tagName === 'BUTTON' || event.target.closest('button')) {
         ulElement.classList.toggle('expanded');
         const numOfChildren = ulElement.children.length;
         const newHeight = ulElement.classList.contains('expanded') ? `${numOfChildren * 95}px` : '95px';
         ulElement.style.height = newHeight;
      }
   }
   const mediaQuery = window.matchMedia('(max-width: 767px)');

   function handleMediaQueryChange(e) {
      if (e.matches) {
         ulElement.addEventListener('click', toggleExpandedClass);
         ulElement.style.height = '95px'; // Set initial height
      } else {
         ulElement.removeEventListener('click', toggleExpandedClass);
         ulElement.style.height = ''; // Reset height
      }
   }
   handleMediaQueryChange(mediaQuery);
   mediaQuery.addEventListener('change', handleMediaQueryChange);
</script>