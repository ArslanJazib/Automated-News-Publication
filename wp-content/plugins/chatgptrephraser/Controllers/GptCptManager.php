<?php

namespace GPT\Controllers;

class GptCptManager {
    public function __construct() {
        // Hook the ACF initialization to add field groups and fields
        add_action('acf/init', array($this, 'register_acf_field_groups'));
    }

    // Register Custom Post Type - GPT Posts
    public function register_custom_post_type() {
        $labels = array(
            'name' => 'GPT Posts',
            'singular_name' => 'GPT Post',
            'menu_name' => 'GPT Posts',
            'add_new' => 'Add New',
            'add_new_item' => 'Add New GPT Post',
            'edit_item' => 'Edit GPT Post',
            'new_item' => 'New GPT Post',
            'view_item' => 'View GPT Post',
            'view_items' => 'View GPT Posts',
            'search_items' => 'Search GPT Posts',
            'not_found' => 'No GPT posts found',
            'not_found_in_trash' => 'No GPT posts found in Trash',
            'all_items' => 'All GPT Posts',
            'archives' => 'GPT Post Archives',
            'attributes' => 'GPT Post Attributes',
            'insert_into_item' => 'Insert into GPT Post',
            'uploaded_to_this_item' => 'Uploaded to this GPT Post',
            'featured_image' => 'Featured Image',
            'set_featured_image' => 'Set featured image',
            'remove_featured_image' => 'Remove featured image',
            'use_featured_image' => 'Use as featured image',
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array('slug' => 'gpt-posts'),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 5,
            'supports'           => array('title', 'editor', 'thumbnail', 'excerpt' , 'revisions'),
            'menu_icon' => 'dashicons-admin-post',
        );

        register_post_type('gpt_posts', $args);
    }

    // Register ACF Field Groups and Fields for GPT Posts
    public function register_acf_field_groups() {
        // Check if ACF plugin is active
        if (function_exists('acf_add_local_field_group')) {
            // Define and add ACF field groups and fields for GPT Posts.
            acf_add_local_field_group(array(
                'key' => 'group_gpt_post_fields',
                'title' => 'GPT Post Fields',
                'location' => array(
                    array(
                        array(
                            'param' => 'post_type',
                            'operator' => '==',
                            'value' => 'gpt_posts',
                        ),
                    ),
                ),
            ));

            // Add ACF fields for GPT Posts
            acf_add_local_field(array(
                'parent' => 'group_gpt_post_fields',
                'key' => 'field_seo_description',
                'label' => 'SEO Description',
                'name' => 'seo_description',
                'type' => 'text',
            ));

             acf_add_local_field(array(
                'parent' => 'group_gpt_post_fields',
                'key' => 'field_subject',
                'label' => 'Subject',
                'name' => 'subject',
                'type' => 'text',
            ));

             acf_add_local_field(array(
                'parent' => 'group_gpt_post_fields',
                'key' => 'field_questions',
                'label' => 'Questions',
                'name' => 'questions',
                'type' => 'textarea',
            ));

            acf_add_local_field(array(
                'parent' => 'group_gpt_post_fields',
                'key' => 'field_keywords',
                'label' => 'Keywords (Comma-Separated)',
                'name' => 'keywords',
                'type' => 'text',
            ));

            if (is_plugin_active('rssfeedsfetcher/rssfeedsfetcher.php')) {
                acf_add_local_field(array(
                    'parent' => 'group_gpt_post_fields',
                    'key' => 'field_custom_post_id',
                    'label' => 'Custom Post (One-on-One Relationship)',
                    'name' => 'custom_post_id',
                    'type' => 'post_object',
                    'post_type' => 'custom_posts',
                    'return_format' => 'id',
                ));
            }

            acf_add_local_field(array(
                'parent' => 'group_gpt_post_fields',
                'key' => 'field_used_prompt',
                'label' => 'Used Prompt for Generation',
                'name' => 'used_prompt',
                'type' => 'textarea',
            ));

            acf_add_local_field(array(
                'parent' => 'group_gpt_post_fields',
                'key' => 'field_newsmaster_text',
                'label' => 'Newsmaster Text',
                'name' => 'newsmaster_text',
                'type' => 'text',
            ));

            acf_add_local_field(array(
                'parent' => 'group_gpt_post_fields',
                'key' => 'field_fetch_url_gpt_response',
                'label' => 'Fetch from url GPT Response',
                'name' => 'fetch_url_gpt_response',
                'type' => 'textarea',
            ));

            acf_add_local_field(array(
                'parent' => 'group_gpt_post_fields',
                'key' => 'field_wp_post_id',
                'label' => 'WordPress Default Post (One-on-One Relationship)',
                'name' => 'wp_post_id',
                'type' => 'post_object',
                'post_type' => 'post',
                'return_format' => 'id',
            ));

            acf_add_local_field(array(
                'parent' => 'group_gpt_post_fields',
                'key' => 'field_compare_results',
                'label' => 'Compared Results',
                'name' => 'compare_results',
                'type' => 'textarea',
            ));
            acf_add_local_field(array(
                'parent' => 'group_gpt_post_fields',
                'key' => 'field_similarity_results',
                'label' => 'Compared Results (Similarities)',
                'name' => 'compare_results_similarities',
                'type' => 'textarea',
            ));

        }
    }

    // Register Custom Taxonomy for GPT Posts
    public function register_custom_taxonomy() {
        $taxonomy_labels = array(
            'name' => 'GPT Categories',
            'singular_name' => 'GPT Category',
            'menu_name' => 'GPT Categories',
        );

        register_taxonomy('gpt_categories', 'gpt_posts', array(
            'labels' => $taxonomy_labels,
            'rewrite' => array('slug' => 'gpt-categories'),
            'hierarchical' => true,
            'public' => true,
            'show_in_menu' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
        ));

        // Check if the RSS Feed Fetcher plugin is active
        if (is_plugin_active('rssfeedsfetcher/rssfeedsfetcher.php')) {
            wp_insert_term('From Fetch from URL', 'gpt_categories');
        }

        // If the Gmail Content Extractor plugin is active, add the term for Gmail
        elseif (is_plugin_active('gmailapiconnector/gmailapiconnector.php')) {
            wp_insert_term('From Gmail API Connector', 'gpt_categories');
        }

        // Associated with this plugin
        wp_insert_term('From Custom Post', 'gpt_categories');
        wp_insert_term('From Newsmaster', 'gpt_categories');
    }

    // Deactivate Custom Post Type - GPT Posts
    public function deactivate_custom_post_type() {
        unregister_post_type('gpt_posts');
    }

    // Uninstall Custom Post Type - GPT Posts
    public function uninstall_custom_post_type() {
        // Define the custom post type
        $post_type = 'gpt_posts';

        // Check if the custom post type exists
        if (post_type_exists($post_type)) {
            $posts = get_posts(array('post_type' => $post_type, 'numberposts' => -1));

            // Delete ACF fields associated with GPT Posts
            if (function_exists('acf_delete_field_group')) {
                $field_group_key = 'group_gpt_post_fields';
                acf_delete_field_group($field_group_key);
            }

            foreach ($posts as $post) {
                // Delete post meta data
                delete_post_meta($post->ID, 'seo_description');
                delete_post_meta($post->ID, 'keywords');
                delete_post_meta($post->ID, 'custom_post_id');
                delete_post_meta($post->ID, 'used_prompt');
                delete_post_meta($post->ID, 'compare_results');

                // Delete terms from the taxonomy if used
                $terms = wp_get_post_terms($post->ID, 'gpt_categories');
                foreach ($terms as $term) {
                    wp_remove_object_terms($post->ID, $term, 'gpt_categories');
                }

                // Delete the post
                wp_delete_post($post->ID, true);
            }
        }

        // Delete the custom taxonomy "GPT Categories"
        unregister_taxonomy('gpt_categories');

        flush_rewrite_rules();
    }

}