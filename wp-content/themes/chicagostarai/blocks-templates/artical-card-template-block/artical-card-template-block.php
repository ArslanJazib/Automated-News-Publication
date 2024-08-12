
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
        border: 6px solid #37517E;
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


<div id="preloader" class=""></div>


<?php


$history_post_type = get_field("history_post_type");


$history_post_cat_category = get_field("history_post_cat_category");


$history_taxonomy = get_field("history_taxonomy");
$history_taxonomy_field = get_field("history_taxonomy_field");

$history_order_by = get_field("history_order_by");
if (is_array($history_order_by) && isset($history_order_by['value'])) {
    $order_by_value = $history_order_by['value'];
    $order_by_label = $choices[$order_by_value];
} else {
    $order_by_value = '';
    $order_by_label = '';
}

$history_order = get_field("history_order");
if (is_array($history_order) && isset($history_order['value'])) {
    $order_value = $history_order['value'];
    $order_label = $choices[$order_value];
} else {
    $order_value = '';
    $order_label = '';
}

echo '<script>';
echo 'var historyPostCatCategory = ' . json_encode($history_post_cat_category) . ';';
echo 'var historyPostType = ' . json_encode($history_post_type) . ';';
// echo 'var articleHistoryID = ' . json_encode($article_history_id) . ';';
echo 'var historyTaxanomy = ' . json_encode($history_taxonomy) . ';';
echo 'var historyOrderBy = ' . json_encode($history_order_by) . ';';
echo 'var historyOrder = ' . json_encode($history_order) . ';';
echo '</script>';
?>
<table class="table" id="articledatatable" post-cat="<?php echo ($history_post_cat_category) ?>" post-type="<?php echo ($history_post_type) ?>" class="display" style="width:100%">

</table>
<script>
    jQuery(document).ready(function($) {
        var post_cat = historyPostCatCategory;
        var post_type = historyPostType;
        var taxanomy = historyTaxanomy;
        var Order_By = historyOrderBy;
        var order = historyOrder;

        jQuery('#articledatatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: ajax_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'my_custom_table_data', // Create a custom AJAX action hook
                    post_cat: post_cat,
                    post_type: post_type,
                    taxanomy: taxanomy,
                    Order_By: Order_By,
                    order: order,
                },
            },
            dom: 'Qlfrtip',
            oLanguage: {
                "sSearch": '<i class="datatablesearch fas fa-search"></i>',
            },
            language: {
                searchPlaceholder: "Search..."
            },
            columns: [{
                data: 'content'
            }, ],
        });
    });


    function send_gmail_data_to_gpt(gmail_post_id) {
        
        var status = 0;
        jQuery.ajax({
            type: "POST",
            url: ajaxurl,
            data: {
                action: "send_gmail_content_to_gpt",
                gmail_post_id: gmail_post_id,
            },
            beforeSend: function () {
           
                showPreloader();
            },
            success: function(data) {
                status = data.status_Code;
                create_gpt_post_gmail(data.content_array, gmail_post_id)
            }
        });
    }


    function create_gpt_post_gmail(content, gmail_post_id) {

        var content_array = content;
        var status = 0;

        jQuery.ajax({
            type: "POST",
            url: ajaxurl,
            data: {
                action: "save_gpt_post",
                content_array: content_array,
                taxanomy: "From Gmail API Connector",
                gmail_post_id: gmail_post_id,
                from_gmail: "from_gmail",
            },
            success: function(data) {
                status = data.status_Code
                jQuery("#send-to-gpt"+gmail_post_id).hide();
                jQuery("#Comparison_button"+gmail_post_id).on("click", function() {
                    compare_orginal_gpt_content_gmail(data.gpt_post_id, gmail_post_id)
                });
                jQuery("#sndtoblox-button"+gmail_post_id).on("click", function() {
                    create_wp_post_blox(data.gpt_post_id, gmail_post_id);
                });
            },
            complete: function () {
                if (status == 200) {
                    document.getElementById("Comparison_button"+gmail_post_id).style.display = 'block';
                    document.getElementById("sndtoblox-button"+gmail_post_id).style.display = 'block';
                    Swal.fire({
                        title: 'Success',
                        text: 'Post Saved Successfully.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    });
                }
                hidePreloader();
                // You can hide loading spinner, update UI, etc.
            }
        });
        
    }

    function create_wp_post_blox(gpt_post_id,gmail_post_id) {
        var status = 0;
        jQuery.ajax({
            type: "POST",
            url: ajaxurl,
            data: {
                action: "create_wp_post_for_blox",
                gpt_post_id: gpt_post_id,
            },
            async: false,
            beforeSend: function () {
                showPreloader();
            },
            success: function(data) {
                status = data.status_Code;
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log("AJAX Error:", textStatus, errorThrown);
                // Handle error here, e.g., display an error message
                Swal.fire({
                    title: 'Error',
                    text: 'An error occurred during the AJAX request.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            },
            complete: function () {
                if (status == 200) {
                    document.getElementById("sndtoblox-button"+gmail_post_id).style.display = 'none';
                    Swal.fire({
                        title: 'Success',
                        text: 'Post Created Successfully.',
                        icon: 'success', 
                        confirmButtonText: 'OK'
                    });
                } else {
                    
                    Swal.fire({
                        title: 'Failed',
                        text: 'Failed to create post.',
                        icon: 'error', // success, error, warning, info, question
                        confirmButtonText: 'OK'
                    });
                }
                hidePreloader();
            }
        });
    }

    function compare_orginal_gpt_content(gpt_post_id) {
        var status = 0;
        var permalink = '';
        jQuery('#Comparison_button'+gpt_post_id).css('background-color', 'green').html('Generating Comparison...');
        jQuery.ajax({
            type: "POST",
            url: ajaxurl,
            data: {
                action: "output_comparison",
                gpt_post_id: gpt_post_id,
            },
            beforeSend: function () {
                showPreloader();
            },
            success: function(data) {
                status = data.status_Code;
                permalink = data.post_permalink;
            },
            complete: function () {
                if (status == 200) {
                    jQuery('#Comparison_button'+gpt_post_id).html('Comparison Successful');
                    document.getElementById("Comparison_button"+gpt_post_id).style.display = 'none';
                    Swal.fire({
                        title: 'Success',
                        text: 'Comparison Successful.',
                        icon: 'success', // success, error, warning, info, question
                        confirmButtonText: 'OK'
                    }).then(function() {
                        window.open(permalink, "_blank");
                    });
                }
                hidePreloader();
            }
        });
        

    }

    function compare_orginal_gpt_content_gmail(gpt_post_id, gmail_post_id) {
        var status = 0;
        var permalink = '';
        jQuery('#Comparison_button'+gmail_post_id).html('Generating Comparison...');
        jQuery.ajax({
            type: "POST",
            url: ajaxurl,
            data: {
                action: "output_comparison",
                gpt_post_id: gpt_post_id,
                gmail_flag: "gmail_flag",
                gmail_post_id: gmail_post_id,
            },
            beforeSend: function () {
                showPreloader();
            },
            success: function(data) {
                status = data.status_Code;
                permalink = data.post_permalink;
            },
            complete: function () {
                
                if (status == 200) {
                    jQuery('#Comparison_button'+gmail_post_id).html('Comparison Successful');
                    document.getElementById("Comparison_button"+gmail_post_id).style.display = 'none';
                    Swal.fire({
                        title: 'Success',
                        text: 'Comparison Successful.',
                        icon: 'success', // success, error, warning, info, question
                        confirmButtonText: 'OK'
                    }).then(function() {
                        window.open(permalink, "_blank");
                    });
                }
                hidePreloader();
            }
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