<?php
    namespace RSS\Models;

    class RssFeedParser {

        public function scraped_posts_title_check($new_post_data) {
            $args = array(
                'post_type' => 'scraped_posts',
                'posts_per_page' => 1, // Set to 1 to check if any matching posts exist
                'post_status' => 'publish',
                's' => sanitize_text_field($new_post_data['new_post_title']),
                'meta_query' => array(
                    'relation' => 'AND',  // Match all meta conditions
                    array(
                        'key' => '_source',
                        'value' => $new_post_data['_source'],
                        'compare' => 'LIKE',
                    ),
                    array(
                        'key' => '_source_urls',
                        'value' => $new_post_data['_source_urls'],
                        'compare' => 'LIKE',
                    ),
                ),
            );

            // Perform the query
            $query = new \WP_Query($args);

            // Check if there are matching posts
            if ($query->have_posts()) {
                return true;  // Matching posts found
            } else {
                return true; // No matching posts found
            }
        }

        public function create_scraped_post($post_data, $automated = false) {

            // Prepare the post data
            $new_post_data = array(
                'post_title' => $post_data['new_post_title'],
                'post_type' => 'scraped_posts',
                'post_status' => 'publish',
                'post_content' => $post_data['new_post_description'],

            );
            // Insert the new post
            $post_id = wp_insert_post($new_post_data);

            $paywall_status = $post_data['_paywall'];

            if($paywall_status == 'paywall'){
                wp_set_object_terms($post_id, 'With Paywall', 'scraped_categories');
            }

            elseif($paywall_status == 'without_paywall'){
                wp_set_object_terms($post_id, 'Without Paywall', 'scraped_categories');
            }

            if(!$automated){
                wp_set_object_terms($post_id, 'From Manually Scraped', 'scraped_categories');
            }

            unset($post_data['_paywall']);

            foreach ($post_data as $meta_key => $meta_value) {

                update_post_meta($post_id, $meta_key, $meta_value);
            }

            // Check if the post was inserted successfully
            if ($post_id && !is_wp_error($post_id)) {
                // error_log('Post Data: ' . print_r($post_data, true));
                // $this->create_custom_post($post_id, $title, $text, $link) ;
                return $post_id; // Return the new post ID
            } else {
                return false; // Failed to insert the post
            }
        }

        public function create_custom_post($post_ids = array(), $title = array(), $text = array(), $link = array(), $extra_inputs = array()) {

            $custom_post_title = (!empty($post_ids[0])) ? get_the_title($post_ids[0]) : $title[0] ;
            $new_custom_post_data = array(
                'post_title' => $custom_post_title,
                'post_type' => 'custom_posts',
                'post_status' => 'publish',
            );

            $custom_post_id = wp_insert_post($new_custom_post_data);

            if ($custom_post_id && !is_wp_error($custom_post_id)) {

                if(is_array($post_ids)){
                    $filteredArray = array_filter($post_ids, function ($item) {
                        return $item !== null && !empty($item);
                    });
                    if (count($filteredArray) > 0){

                        update_field('scraped_post_ids', $post_ids, $custom_post_id);
                    }
                }
                $new_rows = array();

                for ($i = 0; $i < count($title); $i++) {
                    if(!empty($title[$i])){
                        $new_row = array(
                            "custom_source_title" => $title[$i],
                            "custom_source_content" => $text[$i],
                            "custom_source_url" => $link[$i]
                        );
                        $new_rows[] = $new_row;
                    }
                }
                $filteredArray2 = array_filter($title, function ($value) {
                    return $value !== null && !empty($value);
                });
                if (count($filteredArray2) > 0){

                    update_field("source_repeater", $new_rows, $custom_post_id);
                }

               if (!empty($extra_inputs)) {
        foreach ($extra_inputs as $key => $value) {
            update_field($key, $value, $custom_post_id);

        }

    }


                return $custom_post_id;
            }
            else {
                return false;
            }
        }




    }

?>