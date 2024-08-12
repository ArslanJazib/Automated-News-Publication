<?php get_header(); ?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

<?php $scrapped_post_id = get_the_ID();?>
    <header class="entry-header">
        <section class="blog-inner-page">
            <div class="container-fluid gx-xl-4 gx-2">
                <div class="row dashboard-content g-xl-5 g-3 mb-5">
                    <div class="col-12">

                        <div class="content-body mt-5">
                            <div class="title">
                                <h2 class="text-start">
                                    <?php echo the_title(); ?>
                                </h2>
                            </div>
                            <div class="prompt">
                                <div class="col-12">

                                    <div class="generated-artical p-2">
                                        <h3 class="block-heading mb-4">Original Article</h3>
                                        
                                            <div class="given-data p-0">
                                                <h4 class="mb-2">
                                                    <?php echo the_title(); ?>
                                                </h4>
                                                <p>
                                                    <?php echo the_content(); ?>
                                                </p>

                                                <?php $source_url = get_field('_source_urls', $scrapped_post_id);

                                                if(!empty($source_url)){
                                                    ?>
                                                        <h2 class="text-start">Source Url:</h2>
                                                    <?php
                                                    echo "<a target='_blank' href='".$source_url."'>
                                                    ".$source_url."</a>";
                                                }
                                                
                                                ?>
                                               
                                            </div>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </header>
</article>