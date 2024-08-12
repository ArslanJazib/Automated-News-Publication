<?php

/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package chicagostar
 */

get_header();

if(function_exists('get_field')) {
    $post_id = get_the_ID();

    // Get the GPT post IDs and content
    $gpt_data = get_gpt_data($post_id);

    $gpt_ids = $gpt_data['ids'];
    $gpt_content = $gpt_data['content'];
    $post_content = get_post_field('post_content', $post_id);

    // Check if there are GPT posts
    if(!empty($gpt_ids)) {
        // Get the compare results from the first GPT post (adjust this logic based on your requirements)
        $first_gpt_post_id = $gpt_ids[0];
        $gpt_compare_result = get_field('compare_results', $first_gpt_post_id);
        $gpt_compare_result = nl2br($gpt_compare_result);

        $gpt_similarities_result = get_field('compare_results_similarities', $first_gpt_post_id);
        $gpt_similarities_result = nl2br($gpt_similarities_result);
    } else {
        $gpt_compare_result = ''; // Set a default value if no GPT posts are found
        $gpt_similarities_result = ''; // Set a default value if no GPT posts are found
    }
    $gptSentences = breakIntoSentences(trim(strip_tags($gpt_content[0]['post_content'])));
    

    $matchingSentences = findMatchingSentences($gptSentences, $post_content);
    $sizeofmatchingSentences = sizeof($matchingSentences);
    $sizeofgptSentences = sizeof($gptSentences);
    $highlightedString = highlightSentences($gpt_content[0]['post_content'], $matchingSentences);

} else {
    echo "ACF not available or not activated";

    $gpt_ids = array();
    $gpt_content = array();
    $gpt_compare_result = '';
}

function highlightSentences($inputString, $sentencesToHighlight) {
    foreach ($sentencesToHighlight as $sentence) {
        if (stripos($inputString, $sentence) !== false) {
            $inputString = str_ireplace($sentence, '<span style="background-color: yellow;">' . $sentence . '</span>', $inputString);
        }
    }
    return $inputString;
}

function breakIntoSentences($paragraph) {
   
    return preg_split('/(?<!\w\.\w.)(?<![A-Z][a-z]\.)(?<![0-9]\.)(?<=\.|\?)\s(?!com|org|net)/', $paragraph);
}

function findMatchingSentences($articleSentences, $otherArticle) {
    
    $matchingSentences = [];

    foreach ($articleSentences as $sentence) {
       
        $trimmedSentence = trim($sentence);
        if (stripos($otherArticle, $trimmedSentence) !== false) {
            $matchingSentences[] = $trimmedSentence;
        }
    }
    return $matchingSentences;
}

function get_gpt_data($gpt_post_id) {
    $gpt_data = array(
        'ids' => array(),
        'content' => array(),
    );

    $gpt_ids = get_post_meta($gpt_post_id, 'gpt_post_ids', true);

    if($gpt_ids) {
        if(!is_array($gpt_ids)) {
            $gpt_ids = explode(',', $gpt_ids);
            $gpt_ids = array_filter($gpt_ids, 'strlen');
        }

        $gpt_data['ids'] = $gpt_ids;
    }

    foreach($gpt_data['ids'] as $gpt_id) {
        $gpt_post = get_post($gpt_id);

        if($gpt_post) {
            $post_data = array(
                'ID' => $gpt_post->ID,
                'post_title' => $gpt_post->post_title,
                'post_content' => $gpt_post->post_content,
            );

            $gpt_data['content'][] = $post_data;
        }
    }

    return $gpt_data;
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <header class="entry-header">
        <section class="blog-inner-page">
            <div class="container-fluid gx-xl-4 gx-2">
                <div class="row dashboard-content g-xl-5 g-3 mb-5">
                    <div class="col-12">
                        <div class="content-body mt-5">
                            <div class="container">
                                <div class="content-row pt-0 row gx-0 gy-3 g-lg-5">
                                    <div class="row mt-5">

                                        <?php if(!empty($gpt_compare_result) || !empty($gpt_similarities_result)){
                                            ?>
                                            <div class="row pb-3">
                                                <h4 class="d-flex justify-content-center">Comparison Results</h4>
                                            </div>
                                            <?php
                                        }?>
                                        <?php if(!empty($gpt_compare_result)){?>
                                        <div class="prompt col-12 col-md-6">
                                            <h4 class="block-heading">Differences</h4>
                                            <div class="given-data">
                                                <p>
                                                    <?php echo $gpt_compare_result; ?>
                                                </p>
                                            </div>
                                        </div>
                                        <?php } ?>
                                        <?php if(!empty($gpt_similarities_result)){?>
                                        <div class="prompt col-12 col-md-6">
                                            <h4 class="block-heading">Similarities</h4>
                                            <div class="given-data">
                                                <p>
                                                    <?php echo $gpt_similarities_result; ?>
                                                </p>
                                            </div>
                                        </div>
                                        <?php } ?>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="generated-artical">
                                            <h3 class="block-heading">GPT Post Articles</h3>
                                            <?php foreach($gpt_content as $index => $gpt_post_data): ?>
                                                <a href="<?php echo get_permalink($gpt_post_data['ID']); ?>"
                                                    class="d-block text-dark">
                                                    <div class="generated-artical-desc">
                                                        <!-- <h2>News Feed #  // echo $index + 1; ?>:</h2> -->
                                                        <h4>
                                                            <?php echo $gpt_post_data['post_title']; ?>
                                                        </h4>
                                                        <p>
                                                            <?php echo (!empty($highlightedString)) ? $highlightedString : ''; ?>
                                                        </p>
                                                    </div>
                                                </a>
                                            <?php endforeach; ?>
                                        </div>
                                           <!-- pie chart -->
                                    <div class="px-0 py-3" id="plagirism_div">
                                        <h3 class="mb-3 text-center">STATISTICS:</h3>
                                        <div class="row justify-content-evenly gy-4">
                                            <div class="card col-12 col-md-4 p-2">
                                                <div class="row">
                                                    <canvas class="p-2" id="myDoughnutChart" width="200" height="200"></canvas>
                                                </div>
                                                
                                                <div class="d-flex justify-content-evenly">
                                                    <div class="card shadow-sm p-2 ms-1">
                                                        <p class="text-center"><b><?php echo (!empty($sizeofgptSentences)) ? $sizeofgptSentences : '0' ?></b></p>
                                                        <p class="text-center" style="font-size:11px">Total Sentences</p>
                                                    </div>
                                                    <div class="card shadow-sm p-2 ms-1">
                                                        <p class="text-center"><b><?php echo (!empty($sizeofmatchingSentences)) ? $sizeofmatchingSentences : '0' ?></b></p>
                                                        <p class="text-center" style="font-size:11px">Similar Sentences</p>
                                                    </div>
                                                </div>
                                                
                                            </div>
                                            <div class="col-12 col-md-7" style="height:400px; overflow-y:auto;">
                                                <?php 
                                                if(sizeof($matchingSentences) > 1){
                                                    for ($i = 0 ; $i < sizeof($matchingSentences); $i++) {
                                                        if(!empty($matchingSentences[$i])){
                                                            ?>
                                                            <div class="card mb-2 shadow-sm">
                                                                <div class="card-body">
                                                                    <div class="row">
                                                                        <div class="col-12">
                                                                            <p class="mb-0" style="font-size:11px"><?php echo $matchingSentences[$i]; ?></p>
                                                                        </div>
                                                                        
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <?php
                                                        }
                                                    }
                                                }
                                                else{
                                                    ?>
                                                    <p>No Similar sentences found.</p>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="generated-artical recent">
                                            <h3 class="block-heading">Gmail Post</h3>
                                            <h4 class="mb-2 mb-lg-4">
                                                <?php echo the_title(); ?>
                                            </h4>
                                            <div class="generated-artical-desc">
                                                <p>
                                                    <?php echo get_the_content() ?>
                                                </p>
                                            </div>
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


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    var sizeofgptSentences = <?php echo json_encode($sizeofgptSentences) ;?>;
    var sizeofmatchingSentences = <?php echo json_encode($sizeofmatchingSentences) ;?>;
    var ctx = document.getElementById('myDoughnutChart').getContext('2d');
        Chart.register({
            id: 'customLabel1',
            afterDraw: function(chart, args, options) {
                var centerX = (chart.chartArea.left + chart.chartArea.right) / 2;
                var centerY = (chart.chartArea.top + chart.chartArea.bottom) / 2;

                var ctx = chart.ctx;
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.font = '16px Arial'; // Adjust font size and style

                ctx.fillStyle = 'Red'; // Adjust the text color
                ctx.fillText('Similarity', (centerX), (centerY));

                ctx.fillText(Math.floor((sizeofmatchingSentences/sizeofgptSentences)*100)+'%', (centerX), (centerY+20));
            }
        });
        
        var myDoughnutChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Matching Sentences', 'Distinct Sentences'],
                datasets: [{
                    data: [sizeofmatchingSentences, sizeofgptSentences], // Adjust these values based on your data
                    backgroundColor: ['red','Blue'],
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false, // Set to false to hide the legend
                    },
                    customLabel1: {} 
                },
            }
        }); 
    
</script>