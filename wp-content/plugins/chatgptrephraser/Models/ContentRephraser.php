<?php

namespace GPT\Models;

class ContentRephraser {

    public function __construct(){

    }

    public function get_unpublished_no_paywall_scraped_posts() {
        global $wpdb;

        $query = "
            SELECT DISTINCT scraped_posts.*
            FROM {$wpdb->prefix}posts AS scraped_posts
            LEFT JOIN {$wpdb->prefix}term_relationships AS term_rel ON scraped_posts.ID = term_rel.object_id
            LEFT JOIN {$wpdb->prefix}term_taxonomy AS term_tax ON term_rel.term_taxonomy_id = term_tax.term_taxonomy_id
            LEFT JOIN {$wpdb->prefix}terms AS terms ON term_tax.term_id = terms.term_id
            WHERE scraped_posts.post_type = 'scraped_posts'
            AND scraped_posts.post_status = 'publish'
            AND terms.slug = 'without-paywall'
            AND scraped_posts.ID NOT IN (
                SELECT DISTINCT gpt_meta.post_id
                FROM {$wpdb->prefix}posts AS gpt_posts
                LEFT JOIN {$wpdb->prefix}postmeta AS gpt_meta ON gpt_posts.ID = gpt_meta.post_id
                WHERE gpt_posts.post_type = 'gpt_post'
            )
            ORDER BY scraped_posts.post_date DESC
        ";

        $results = $wpdb->get_results($query);

        return $results;
    }


    public function create_gpt_post($c_id, $content = array(), $prompt = NULL, $taxonomy ,$input_text_value,$secondary_taxonomy ){
        $new_gpt_post_data = array(
            'post_title' => strip_tags($content['Title']),
            'post_type' => 'gpt_posts',
            'post_content' => $content['Content'],
            'post_status' => 'publish',
        );
        $gpt_post_id = wp_insert_post($new_gpt_post_data);
        $taxonomy_term = $taxonomy;
    
        if ($gpt_post_id && !is_wp_error($gpt_post_id)) {
            $this->update_gpt_post_meta($gpt_post_id, $content, $c_id, $prompt , $input_text_value);
            $all_terms = array_merge((array) $taxonomy, (array) $secondary_taxonomy);
            wp_set_object_terms($gpt_post_id, $all_terms, 'gpt_categories');
            return $gpt_post_id;
        } else {
            return false;
        }
    }
    
    public function update_gpt_post($gpt_post_id, $content = array(), $prompt = NULL, $taxonomy ,$input_text_value,$secondary_taxonomy){
        $updated_gpt_post_data = array(
            'ID' => $gpt_post_id,
            'post_title' => strip_tags($content['Title']),
            'post_content' => $content['Content'],
        );
    
        $updated_gpt_post_id = wp_update_post($updated_gpt_post_data);
    
        if (is_wp_error($updated_gpt_post_id)) {
            return false;
        }
    
        $this->update_gpt_post_meta($gpt_post_id, $content, null, $prompt ,$input_text_value);
        $all_terms = array_merge((array) $taxonomy, (array) $secondary_taxonomy);
        wp_set_object_terms($gpt_post_id, $all_terms, 'gpt_categories');
        
        return $updated_gpt_post_id;
    }
    
    private function update_gpt_post_meta($gpt_post_id, $content, $c_id, $prompt , $input_text_value){
        if(array_key_exists('Seo preview', $content) && !empty($content['Seo preview'])){
            update_field('seo_description', strip_tags($content['Seo preview']), $gpt_post_id);
        }
        if(array_key_exists('Keywords', $content) && !empty($content['Keywords'])){
            update_field('keywords', strip_tags($content['Keywords']), $gpt_post_id);
        }
        if(array_key_exists('Subject', $content) && !empty($content['Subject'])){
            update_field('field_subject', strip_tags($content['Subject']), $gpt_post_id);
        }
        if(array_key_exists('Questions', $content) && !empty($content['Questions'])){
            update_field('field_questions', strip_tags($content['Questions']), $gpt_post_id);
        }
        if(!empty($c_id)){
            update_field('custom_post_id', $c_id, $gpt_post_id);
        }
        if(!empty($prompt)){
            update_field('used_prompt', $prompt, $gpt_post_id);
        }
        if(!empty($input_text_value)){
            update_field('newsmaster_text', $input_text_value, $gpt_post_id);
        }
    }
    

}
?>