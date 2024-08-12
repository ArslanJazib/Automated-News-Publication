<?php

/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package chicagostar
 */


get_header();



$gpt_post_id = get_the_ID();
$taxonomy = 'gpt_categories';

$terms = get_the_terms($gpt_post_id, $taxonomy);

if ($terms && !is_wp_error($terms)) {
    foreach ($terms as $term) {

        // $term_id = $term->term_id;
        $term_name = $term->name;
        $term_slug = $term->slug;
    }
}
?>
<?php
// Check if ACF is available
if (function_exists('get_field')) {
    $post_id = get_the_ID(); // gpt post

    // Retrieve SEO Description
    $seo_description = get_field('seo_description', $post_id);

    // Retrieve Keywords as a comma-separated string
    $keywords_string = get_field('keywords', $post_id);

    if (!empty($post_id)) {
        // Get the compare results from the first GPT post (adjust this logic based on your requirements)
        $first_gpt_post_id = $post_id;
        $gpt_compare_result = get_field('compare_results', $first_gpt_post_id);
        $gpt_compare_result = nl2br($gpt_compare_result);
        $post_content = get_post_field('post_content', $post_id);

        $gpt_similarities_result = get_field('compare_results_similarities', $first_gpt_post_id);
        $gpt_similarities_result = nl2br($gpt_similarities_result);
    } else {
        $gpt_compare_result = ''; // Set a default value if no GPT posts are found
        $gpt_similarities_result = ''; // Set a default value if no GPT posts are found
    }


    // Check if keywords are present
    if ($keywords_string) {
        // Split the string into an array using commas as the delimiter
        $keywords_array = explode(',', $keywords_string);
    } else {
        // echo "No keywords found";
        $keywords_array = array(); // Initialize empty array to avoid errors later
    }

    // One GPT Post has One Custom Post. Retrieve Custom Post ID
    $custom_post_id = get_field('custom_post_id', $post_id);

    if ($custom_post_id) {
        $source_repeater = get_field('source_repeater', $custom_post_id);
    }

    // Retrieve Used Prompt for Generation
    $used_prompt = get_field('used_prompt', $post_id);

    // Retrieve Compared Results
    $compare_results = get_field('compare_results', $post_id);

    // Retrieve scrapped post IDs and content in a single function
    $scrapped_data = get_scrapped_data($custom_post_id);
    // Covert My Content To Bold // Human Written //
    $post_content = preg_replace("/\*\*(.+?)\*\*/is", "<strong>$1</strong>", $post_content);
    // URL To A Tag
    // $pattern = '/\bhttps?:\/\/\S+\b/';
    // $post_content = preg_replace_callback($pattern, function($matches) {
    // $url_parts = parse_url($matches[0]);
    // $domain = preg_replace('/(?:https?:\/\/)?(?:www\.)?([^\/]+)\.com.*/', '$1', $matches[0]);
    // return '<a href="' . $matches[0] . '">' . strtoupper($domain) . '</a>';
    // }, $post_content);

    // Separate scrapped post IDs and content
    $gptSentences = (trim(strip_tags($post_content)));

    $article2 = '';
    if (!empty($scrapped_data) || !empty($source_repeater)) {

        if (!empty($scrapped_data)) {

            $scrapped_ids = $scrapped_data['ids'];
            $scrapped_content = $scrapped_data['content'];

            foreach ($scrapped_content as $key => $value) {
                $article2 .= trim(strip_tags($value['post_content'])) . ' ';
            }
        }
        if (!empty($source_repeater)) {
            foreach ($source_repeater as $row) {

                $article2 .= $row['custom_source_content'] . ' ';
            }
        }
    }

    $matchingSequences = findMatchingSequences($gptSentences, $article2);
    $final_sequences = array();
    if (!empty($matchingSequences)) {
        foreach ($matchingSequences as $matchingSequence) {
            array_push($final_sequences, str_replace(" .", ".", str_replace(" ,", ",", str_replace(" ?", "?", $matchingSequence))));
        }
    }
    $sizeofmatchingSentences =  countWordsInArray($final_sequences);
    $sizeofgptSentences =  str_word_count($post_content, 0);

    $highlightedString = highlightSentences($post_content, $final_sequences, 'yellow');
} else {
    echo "ACF not available or not activated";
    // Initialize variables to avoid errors later
    $seo_description = '';
    $keywords_array = array();
    $custom_post_id = 0;
    $used_prompt = '';
    $compare_results = '';
    $scrapped_ids = array();
    $scrapped_content = array();
}

function countWordsInArray($array)
{
    $totalWords = 0;

    // Loop through each element of the array
    foreach ($array as $string) {
        // Split the string into words
        $words = str_word_count($string, 0);
        // Increment the total word count
        $totalWords += $words;
    }

    return $totalWords;
}


function highlightSentences($inputString, $sentencesToHighlight, $color)
{

    foreach ($sentencesToHighlight as $sentence) {
        if (stripos($inputString, $sentence) !== false) {
            $inputString = str_ireplace($sentence, '<span style="background-color: ' . $color . ';">' . $sentence . '</span>', $inputString);
        }
    }
    return $inputString;
}

function splitStringIntoWords($string)
{
    $pattern = '/("[^"]*"|“[^“”]*”|‘[^‘’]*’)/s';
    $cleaned_text = preg_replace($pattern, '', $string);
    preg_match_all('/\b[\w\'-]+|\p{P}/u', $cleaned_text, $matches);
    return $matches[0];
}


function findMatchingSequences($string1, $string2)
{
    $words1 = splitStringIntoWords($string1);
    $words2 = splitStringIntoWords($string2);
    $matchingSequences = array();
    // Loop through words in string1
    for ($i = 0; $i < count($words1); $i++) {
        // Loop through words in string2
        for ($j = 0; $j < count($words2); $j++) {
            // If words match, check for matching sequence
            if ($words1[$i] == $words2[$j]) {
                $sequence = $words1[$i];
                $m = $i + 1;
                $n = $j + 1;

                // Continue checking subsequent words for matching sequence
                while ($m < count($words1) && $n < count($words2) && $words1[$m] == $words2[$n]) {
                    $sequence .= ' ' . $words1[$m];
                    $m++;
                    $n++;
                }

                // If sequence length is greater than or equal to 4, add it to matching sequences array
                if (str_word_count($sequence) >= 4) {
                    $matchingSequences[] = $sequence;
                    $i = $m - 1; // Skip the words in string1 that are part of the sequence
                    $j = $n - 1; // Skip the words in string2 that are part of the sequence
                }
            }
        }
    }


    return $matchingSequences;
}

$comparison_text = get_field('compare_results', $gpt_post_id);

?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <header class="entry-header">
        <section class="blog-inner-page">
            <div class="container-fluid gx-xl-4 gx-2">
                <div class="row dashboard-content mb-5">
                    <div class="col-12">
                        <h2 class="text-center text-white p-0 m-0">Story Detail</h2>
                        <div class="content-body mt-5">
                            <div class="container">
                                <div class="d-flex justify-content-between align-items-center">
                                    <p class="fw-bold mb-0">Generated Date: <span style="text-wrap: nowrap; margin-right: 11px;"><?php echo get_the_date('Y-m-d', $post_id); ?>
                                        </span></p>
                                        <?php if (empty($comparison_text)) {
                                        ?>
                                            <?php if ($term_slug === 'from-newsmaster') { ?>
                                                <button style="max-height: 37px;" type="button" class="btn btn-primary" style="display: none;" id="comparison-button" onclick="compare_orginal_gpt_content(<?php echo $gpt_post_id; ?>)">Compare</button>
                                            <?php } else if ($term_slug === 'save-article-as-sample') { ?>
                                                <button style="max-height: 37px;" type="button" class="btn btn-primary" id="comparison-button" onclick="compare_orginal_gpt_content(<?php echo $gpt_post_id; ?>)" disabled>Compare</button>
                                            <?php } else { ?>
                                                <button style="max-height: 37px;" type="button" class="btn btn-primary" id="comparison-button" onclick="compare_orginal_gpt_content(<?php echo $gpt_post_id; ?>)">Compare</button>
                                            <?php } ?>
                                        <?php
                                        }
                                        ?>
                                </div>

                                <div class="title d-flex align-items-center justify-content-between">

                                    <h2 class="text-start text-uppercase">
                                        <?php echo the_title(); ?>
                                    </h2>
                                </div>
                                <?php
                                // Check if the current user has the "administrator" role
                                if (current_user_can('administrator')) { ?>
                                    <div class="prompt">
                                        <h4 class="block-heading">Given Prompt</h4>
                                        <div class="given-data" style="height: 330px; overflow-y: auto">
                                            <?php echo "<p>{$used_prompt}</p>"; ?>
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="row">
                                    <?php
                                    if (!empty($gpt_compare_result) || !empty($gpt_similarities_result)) {
                                    ?>
                                        <div class="row pb-3">
                                            <h4 class="d-flex justify-content-center">Comparison Results</h4>
                                        </div>
                                    <?php
                                    }
                                    ?>

                                    <?php
                                    if (!empty($gpt_compare_result)) { ?>
                                        <div class="prompt col-12">
                                            <h4 class="block-heading">Differences </h4>
                                            <div class="given-data" style="height: 330px; overflow-y: auto">
                                                <!-- Display compared result from the first GPT post -->
                                                <p><?php echo $gpt_compare_result; ?></p>
                                            </div>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                    <?php
                                    if (!empty($gpt_similarities_result)) { ?>
                                        <!-- <div class="prompt col-12 col-md-6">
                                            <h4 class="block-heading">Similarities </h4>
                                            <div class="given-data" style="height: 330px; overflow-y: auto">
                                                <p><?php echo $gpt_similarities_result; ?></p>
                                            </div>
                                        </div> -->
                                    <?php
                                    }
                                    ?>
                                </div>

                                <h3 class="block-heading mb-2 mt-3">Instructions</h3>
                                <div class="row dashboard-content mb-4">
                                    <div class="col" style="padding: 15px 0 0 10px;
                                        border-radius: 10px;
                                        border: 1px solid #bed5f3;
                                        margin-bottom: 20px;
                                        margin: 0px 10px;">
                                        <p style="color: #50575e;
                                            font-size: 16px;
                                            font-weight: 500;
                                            line-height: 30px;">Similar text is highlighted. Review and modify text to make sure your story is original and to avoid plagiarisim.</p>
                                    </div>
                                </div>



                                <div class="content-row pt-0 row gx-0 gy-3 g-lg-3">
                                    <div class="col-lg-6">
                                        <div class="heading__wrapper">
                                            <h3 class="block-heading"><?php if ($term_slug === 'from-newsmaster') { ?>
                                                    Output Text
                                                <?php } else { ?>
                                                    Generated Article
                                                <?php }
                                                ?></h3>
                                        </div>
                                        <div class="generated-artical-wrapper">
                                            <div class="generated-artical">
                                                <div class="generated-artical-desc">
                                                    <h4>
                                                        <?php echo the_title(); ?>
                                                    </h4>
                                                    <p>
                                                        <strong>Subject:</strong> <?php echo get_field('field_subject', $post_id); ?>
                                                        <br />
                                                        <br />
                                                        <strong>Questions:</strong> <br /><?php echo get_field('field_questions', $post_id); ?>
                                                        <br />
                                                        <br />
                                                        <?php if ($term_name != "From Newsmaster") {
                                                            echo $highlightedString;
                                                        } else {
                                                            echo $post_content;
                                                        }  ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Start Edit Box -->


                                        <?php
                                        if ($term_name != "From Newsmaster") {
                                            $editable_content = $highlightedString;
                                        } else {
                                            $editable_content = $post_content;
                                        }

                                        ?>



                                    </div>
                                    <div class="col-lg-6">
                                        <div class="recent-articals ps-0 recent-articles-origional">
                                            <div class="heading__wrapper">
                                                <h3 class="block-heading"><?php if ($term_slug === 'from-newsmaster') { ?>
                                                        Input Text
                                                    <?php } else { ?>
                                                        Original Article
                                                    <?php }
                                                    ?></h3>
                                            </div>


                                            <div class="origional-artical recent">
                                                <?php if ($term_slug === 'from-newsmaster') { ?>
                                                    <?php echo "<p>{$used_prompt}</p>"; ?>
                                                <?php } else { ?>
                                                    <?php if (isset($scrapped_content)) { ?>
                                                        <?php foreach ($scrapped_content as $index => $scrapped_post_data) : ?>


                                                            <div class="generated-artical-desc">
                                                                <h2>News Feed # <?php echo $index + 1; ?>:</h2>
                                                                <a target="_blank" href="<?php echo get_permalink($scrapped_post_data['ID']); ?>" class="d-block text-dark">
                                                                    <h4> <?php echo $scrapped_post_data['post_title']; ?> </h4>
                                                                </a>
                                                                <p> <?php
                                                                    $originalhighlightedText = highlightSentences($scrapped_post_data['post_content'], $final_sequences, 'orange');
                                                                    echo trim($originalhighlightedText);
                                                                    ?>
                                                                </p>
                                                                <br>
                                                                <b>This article is fetched from:</b>
                                                                <br><a target="_blank" style="word-break: break-all;" href="<?php echo $scrapped_post_data['source_url']; ?>"> <?php echo $scrapped_post_data['source_url']; ?> </a>

                                                                <?php

                                                                $published_date = $scrapped_post_data['_source_publish_date'];
                                                                $published_date_format = new DateTime($published_date);

                                                                ?>
                                                                <p class="fw-bold mt-3">Article Published Date: <?php echo  $published_date_format->format('Y-m-d'); ?></p>
                                                            </div>
                                                            <hr>
                                                    <?php endforeach;
                                                    } ?>
                                                <?php } ?>

                                                <div class="generated-artical mt-3 recent">
                                                    <?php
                                                    $repeater_data = get_field('source_repeater', $custom_post_id);

                                                    // Check if $repeater_data is not empty and at least one source has a URL
                                                    if ($repeater_data) {
                                                    ?>


                                                        <?php
                                                        foreach ($repeater_data as $repeater_item) {
                                                            $source_title = $repeater_item['custom_source_title'];
                                                            $source_content = $repeater_item['custom_source_content'];
                                                            $custom_source_url = $repeater_item['custom_source_url'];
                                                        ?>
                                                            <div class="generated-artical-desc">
                                                                <h2>Custom Source</h2>
                                                                <div class="source-block">
                                                                    <h4><?php echo $source_title; ?></h4>
                                                                    <p>
                                                                        <?php
                                                                        $source_highlightedText = highlightSentences($source_content, $final_sequences, 'orange');
                                                                        echo $source_highlightedText;
                                                                        ?>
                                                                    </p>
                                                                </div>

                                                                <br>
                                                                <?php if (!empty($custom_source_url)) : ?>
                                                                    <b>This article is copied from URL:</b>
                                                                    <br>
                                                                    <a style="word-break: break-all;" href="<?php echo $custom_source_url; ?>" target="_blank">
                                                                        <?php echo $custom_source_url; ?>
                                                                    </a>
                                                                <?php endif; ?>
                                                            </div>
                                                            <hr>
                                                    <?php
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between mt-3">
                                        <div id="edit-content-link">
                                            <?php if ($term_slug === 'save-article-as-sample') { ?>
                                                <a href="#edit-content-form" class="btn btn-primary mt-3" disabled>Edit Content</a>
                                            <?php } else { ?>
                                                <a href="#edit-content-form" class="btn btn-primary mt-3">Edit Content</a>
                                            <?php } ?>
                                        </div>

                                        <?php if ($term_slug === 'save-article-as-sample') { ?>
                                            <button type="button" class="btn btn-success mt-3" onClick="copyToClipboard()" disabled>
                                                <i class="fa-solid fa-copy"></i>
                                            </button>
                                        <?php } else { ?>
                                            <button type="button" class="btn btn-success mt-3" onClick="copyToClipboard()">
                                                <i class="fa-solid fa-copy"></i>
                                            </button>
                                        <?php } ?>
                                    </div>

                                    <?php if ($term_slug === 'save-article-as-sample') { ?>
                                        <style>
                                            #edit-content-link {
                                                pointer-events: none;
                                            }
                                        </style>
                                    <?php } ?>

                                    <form id="edit-content-form" style="display: none;">
                                        <?php wp_nonce_field('edit_content_nonce', 'edit_content_nonce'); ?>
                                        <h3 class="mt-4">Edit Content</h3>
                                        <div class="mb-3 mt-0" id="edit-content-form-link">

                                            <?php
                                            // Editor settings
                                            $editor_id2 = 'editable_content';
                                            $settings2 = array(
                                                'media_buttons' => false,
                                                'textarea_name' => 'editable_content',
                                                'editor_height' => 300,
                                            );

                                            // Display the editor
                                            wp_editor($editable_content, $editor_id2, $settings2); ?>

                                            <div class="mt-3">
                                                <button type="button" id="update-content-btn" class="btn btn-success">Update Content</button>
                                            </div>

                                    </form>


                                    <!-- End Edit Box -->

                                </div>

                                <div class="col-12">
                                    <?php if ($term_name != "From Newsmaster") : ?>
                                        <div class="tags">
                                            <h5>Tags:</h5>
                                            <div class="tags-items">
                                                <?php
                                                foreach ($keywords_array as $keyword) { ?>
                                                    <p>
                                                        <?php echo $keyword ?>
                                                    </p>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="tags">
                                            <h5>SEO Description:</h5>
                                            <div class="seo-desc">
                                                <p>
                                                    <?php echo $seo_description ?>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="tags">
                                            <h5 class="mb-3">Source Urls:</h5>
                                            <div class="seo-desc">
                                                <ul>
                                                    <?php
                                                    foreach ($scrapped_content as $index => $scrapped_post_data) {
                                                        if (!empty($scrapped_post_data['source_url'])) {
                                                            echo "<li style='word-break:break-all;'>" . $scrapped_post_data['source_url'] . "</li>";
                                                        }
                                                    }

                                                    $repeater_data = get_field('source_repeater', $custom_post_id);
                                                    if ($repeater_data) {
                                                        foreach ($repeater_data as $repeater_item) {
                                                            $custom_source_url = $repeater_item['custom_source_url'];
                                                            if (!empty($custom_source_url)) {
                                                                echo "<li style='word-break:break-all;'>" . $custom_source_url . "</li>";
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                </ul>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <!-- pie chart -->
                                    <?php if ($term_name != "From Newsmaster") : ?>

                                        <div class="px-0 py-3" id="plagirism_div">
                                            <h3 class="mb-3 text-center">STATISTICS:</h3>
                                            <div class="row justify-content-evenly gy-4">
                                                <div class="card col-12 col-md-4 p-2">
                                                    <div class="d-flex my-4">
                                                        <?php if ($term_slug === 'save-article-as-sample') { ?>
                                                            <button id="rephrase_button" class="btn btn-success" disabled>Rephrase</button>
                                                        <?php } else { ?>
                                                            <button id="rephrase_button" class="btn btn-success">Rephrase</button>
                                                        <?php } ?>
                                                    </div>
                                                    <div class="row">
                                                        <canvas class="p-2" id="myDoughnutChart" width="200" height="200"></canvas>
                                                    </div>

                                                    <div class="d-flex justify-content-evenly">
                                                        <div class="card shadow-sm p-2 ms-1">
                                                            <p class="text-center mb-1"><b><?php echo (!empty($sizeofgptSentences)) ? $sizeofgptSentences : '0' ?></b></p>
                                                            <p class="text-center mb-1" style="font-size:11px">Total Words</p>
                                                        </div>
                                                        <div class="card shadow-sm p-2 ms-1">
                                                            <p class="text-center mb-1"><b><?php echo (!empty($sizeofmatchingSentences)) ? $sizeofmatchingSentences : '0' ?></b></p>
                                                            <p class="text-center mb-1" style="font-size:11px">Similar Words in Sequence</p>
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="col-12 col-md-7" style="height:400px; overflow-y:auto;">
                                                    <?php
                                                    if (sizeof($matchingSequences) > 1) {
                                                        for ($i = 0; $i < sizeof($matchingSequences); $i++) {
                                                            if (!empty($matchingSequences[$i])) {
                                                    ?>
                                                                <div class="card mb-2 shadow-sm">
                                                                    <div class="card-body">
                                                                        <div class="row">
                                                                            <div class="col-12">
                                                                                <p class="mb-0" style="font-size:11px"><?php echo $matchingSequences[$i]; ?></p>
                                                                            </div>

                                                                        </div>
                                                                    </div>
                                                                </div>
                                                        <?php
                                                            }
                                                        }
                                                    } else {
                                                        ?>
                                                        <p>No Similar sentences found.</p>
                                                    <?php
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </section>
    </header>
</article>

<?php
get_footer();
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    function compare_orginal_gpt_content(gpt_post_id) {
        var status = 0;
        jQuery('#comparison-button').css('background-color', 'green').html('Generating Comparison...');
        jQuery.ajax({
            type: "POST",
            url: ajaxurl,
            data: {
                action: "output_comparison",
                gpt_post_id: gpt_post_id,
            },
            beforeSend: function() {
                showPreloader();
            },
            success: function(data) {
                status = data.status_Code;
            },
            complete: function() {
                if (status == 200) {
                    jQuery('#comparison-button').html('Comparison Successful');
                    Swal.fire({
                        title: 'Success',
                        text: 'Comparison Successful.',
                        icon: 'success', // success, error, warning, info, question
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload(true);
                        }
                    });
                }
                hidePreloader();
            }
        });
    }





    var term_name = <?php echo json_encode($term_name); ?>;
    if (term_name != "From Newsmaster") {
        var sizeofgptSentences = <?php echo json_encode($sizeofgptSentences); ?>;
        var sizeofmatchingSentences = <?php echo json_encode($sizeofmatchingSentences); ?>;
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

                ctx.fillText(Math.floor((sizeofmatchingSentences / sizeofgptSentences) * 100) + '%', (centerX), (centerY + 20));
            }
        });

        var myDoughnutChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Matching Sentences', 'Distinct Sentences'],
                datasets: [{
                    data: [sizeofmatchingSentences, sizeofgptSentences], // Adjust these values based on your data
                    backgroundColor: ['red', 'Blue'],
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
    }


    // Edit Box
    document.getElementById('edit-content-link').addEventListener('click', function(event) {
        event.preventDefault();
        var editContentForm = document.getElementById('edit-content-form');

        editContentForm.style.display = 'block';

        editContentForm.scrollIntoView({
            behavior: 'smooth'
        });
    });



    document.getElementById('update-content-btn').addEventListener('click', function(event) {
        event.preventDefault();
        //  Text Editor
        var editorId = 'editable_content';
        var editor = tinymce.get(editorId);
        var content = editor.getContent();
        // AJAX request to update content
        jQuery.ajax({
            type: 'POST',
            url: '<?php echo admin_url('admin-ajax.php') ?>',
            data: {
                action: 'update_editable_content',
                post_id: <?php echo get_the_ID(); ?>,
                editable_content: content
            },
            success: function(response) {
                Swal.fire({
                    title: 'Success',
                    text: 'Article Updated Successfully.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    // Reload the page when the user clicks "OK"
                    if (result.isConfirmed) {
                        location.reload();
                    }
                });
            },
            error: function(error) {
                Swal.fire({
                    title: 'Failed',
                    text: 'Failed to Update.',
                    icon: 'error', // success, error, warning, info, question
                    confirmButtonText: 'OK'
                });
            }
        });
    });

    document.getElementById('rephrase_button').addEventListener('click', function(event) {
        var editorId = 'editable_content';
        var editor = tinymce.get(editorId);
        var content = editor.getContent();
        jQuery.ajax({
            type: 'POST',
            url: '<?php echo admin_url('admin-ajax.php') ?>',
            data: {
                action: 'rephrase_gpt_content',
                post_id: <?php echo get_the_ID(); ?>,
                editable_content: content
            },
            beforeSend: function() {
                showPreloader();
            },
            success: function(response) {
                status = response.status_Code;
                if (status == 200) {
                    Swal.fire({
                        title: 'Success',
                        text: 'Article Rephrased Successfully.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                }

            },
            error: function(error) {
                Swal.fire({
                    title: 'Failed',
                    text: 'Failed to Rephrase.',
                    icon: 'error', // success, error, warning, info, question
                    confirmButtonText: 'OK'
                });
            },
            complete: function(response) {
                hidePreloader();
            }
        });

    });

    // Copy To Clipboard Function
    function copyToClipboard() {
        var copyText = jQuery(".generated-artical-desc").text().trim().replace(/\s+/g, ' ');
        var tempInput = jQuery("<textarea>");
        jQuery("body").append(tempInput);
        tempInput.val(copyText).select();
        document.execCommand("copy");
        tempInput.remove();

        Swal.fire({
            title: 'Success',
            text: 'Copied To Clipboard.',
            icon: 'success',
            confirmButtonText: 'OK'
        });
    }

    function showPreloader() {
        var loader = jQuery('#preloader');
        loader.css('display', 'block');

    }

    function hidePreloader() {
        var loader = jQuery('#preloader');
        loader.css('display', 'none');
    }
</script>

<div id="preloader"></div>
<style>
    #preloader {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 9999;
        overflow: hidden;
        background: rgba(255, 255, 255, 0.8);
        display: none;
    }

    #preloader:before {
        content: "";
        position: fixed;
        top: calc(50% - 30px);
        left: calc(50% - 30px);
        border: 6px solid #37517e;
        border-top-color: #fff;
        border-bottom-color: #fff;
        border-radius: 50%;
        width: 60px;
        height: 60px;
        animation: animate-preloader 1s linear infinite;
    }

    @keyframes animate-preloader {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>