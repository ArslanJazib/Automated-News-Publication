<?php

namespace RSS\Controllers;

class ScrapedCptManager {
    public function __construct() { 
        // Hook the ACF initialization to add field groups and fields
        add_action('acf/init', array($this, 'register_acf_field_groups'));
    }

    // Register Custom Post Type
    public function register_custom_post_type() {
        $labels = array(
            'name' => 'Scraped Posts',
            'singular_name' => 'Scraped Post',
            'menu_name' => 'Scraped Posts',
            'add_new' => 'Add New',
            'add_new_item' => 'Add New Scraped Post',
            'edit_item' => 'Edit Scraped Post',
            'new_item' => 'New Scraped Post',
            'view_item' => 'View Scraped Post',
            'view_items' => 'View Scraped Posts',
            'search_items' => 'Search Scraped Posts',
            'not_found' => 'No Scraped posts found',
            'not_found_in_trash' => 'No Scraped posts found in Trash',
            'all_items' => 'All Scraped Posts',
            'archives' => 'Scraped Post Archives',
            'attributes' => 'Scraped Post Attributes',
            'insert_into_item' => 'Insert into Scraped Post',
            'uploaded_to_this_item' => 'Uploaded to this Scraped Post',
            'featured_image' => 'Featured Image',
            'set_featured_image' => 'Set featured image',
            'remove_featured_image' => 'Remove featured image',
            'use_featured_image' => 'Use as featured image',
        );

        $args = array(
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'scraped-posts'),
            'capability_type' => 'post',
            'has_archive' => true,
            'hierarchical' => false,
            'menu_position' => 5,
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt' , 'revisions'),
            'menu_icon' => 'dashicons-rss',
        );

        register_post_type('scraped_posts', $args);
    }

    // Register Custom Taxonomy
    public function register_custom_taxonomy() {
        // Check if ACF plugin is active
        if (function_exists('acf_add_local_field')) {
            $taxonomy_labels = array(
                'name' => 'Scraped Categories',
                'singular_name' => 'Scraped Category',
                'menu_name' => 'Scraped Categories',
            );

            register_taxonomy('scraped_categories', 'scraped_posts', array(
                'labels' => $taxonomy_labels,
                'hierarchical' => true,
                'public' => true,
                'show_ui' => true,
                'show_admin_column' => true,
                'query_var' => true,
                'rewrite' => array('slug' => 'rss-category'),
            ));

            wp_insert_term('From Automated RSS Feed', 'scraped_categories');
            wp_insert_term('From Manually Scraped', 'scraped_categories');
            wp_insert_term('With Paywall', 'scraped_categories');
            wp_insert_term('Without Paywall', 'scraped_categories');
        }
    }

    // Register ACF Field Groups and Fields
    public function register_acf_field_groups() {
        // Check if ACF plugin is active
        if (function_exists('acf_add_local_field_group')) {
            acf_add_local_field_group(array(
                'key' => 'group_scraped_post_fields',
                'title' => 'Scraped Post Fields',
                'location' => array(
                    array(
                        array(
                            'param' => 'post_type',
                            'operator' => '==',
                            'value' => 'scraped_posts',
                        ),
                    ),
                ),
            ));

            acf_add_local_field(array(
                'parent' => 'group_scraped_post_fields',
                'key' => 'field_source_platform',
                'label' => 'Source Platform',
                'name' => '_source',
                'type' => 'text',
            ));

            acf_add_local_field(array(
                'parent' => 'group_scraped_post_fields',
                'key' => 'field_source_url',
                'label' => 'Source URL',
                'name' => '_source_urls',
                'type' => 'url',
            ));

            acf_add_local_field(array(
                'parent' => 'group_scraped_post_fields',
                'key' => 'field_source_publish_date',
                'label' => 'Source Publish Date',
                'name' => '_source_publish_date',
                'type' => 'date_picker',
            ));

            // acf_add_local_field(array(
            //     'parent' => 'group_scraped_post_fields',
            //     'key' => 'field_paywall_status',
            //     'label' => 'Paywall Status',
            //     'name' => '_paywall',
            //     'type' => 'radio',
            //     'choices' => array(
            //         'paywall' => 'Paywall',
            //         'without_paywall' => 'Without Paywall',
            //     ),
            // ));
        }
    }

    // Deactivate Custom Post Type
    public function deactivate_custom_post_type() {
        unregister_post_type('scraped_posts');
    }

    // Uninstall Custom Post Type - scraped_posts
    public function uninstall_custom_post_type() {
        $post_type = 'scraped_posts';

        if (post_type_exists($post_type)) {
            $posts = get_posts(array('post_type' => $post_type, 'numberposts' => -1));

            // Delete ACF fields associated with 'scraped_posts' if ACF plugin is active
            if (function_exists('acf_delete_field_group')) {
                $field_group_key = 'group_scraped_post_fields';
                acf_delete_field_group($field_group_key);
            }

            foreach ($posts as $post) {
                // Delete post meta data
                delete_post_meta($post->ID, '_source');
                delete_post_meta($post->ID, '_source_urls');
                delete_post_meta($post->ID, '_source_publish_date');
                // delete_post_meta($post->ID, '_paywall');

                // Delete terms from the taxonomy 'scraped_categories'
                $terms = wp_get_post_terms($post->ID, 'scraped_categories');
                foreach ($terms as $term) {
                    wp_remove_object_terms($post->ID, $term, 'scraped_categories');
                }

                // Delete the post
                wp_delete_post($post->ID, true);
            }
        }

        // Delete the custom taxonomy 'scraped_categories' if it exists
        unregister_taxonomy('scraped_categories');

        flush_rewrite_rules();
    }
}