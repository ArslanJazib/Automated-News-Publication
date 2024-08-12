<?php 

namespace GMAIL\Controllers;

class GmailCptManager {
    public function __construct() {
        // Hook the ACF initialization to add field groups and fields
        add_action('acf/init', array($this, 'register_acf_field_groups'));
    }

    // Register Custom Post Type - Gmail Posts
    public function register_custom_post_type() {
        $labels = array(
            'name' => 'Gmail Posts',
            'singular_name' => 'Gmail Post',
            'menu_name' => 'Gmail Posts',
            'add_new' => 'Add New',
            'add_new_item' => 'Add New Gmail Post',
            'edit_item' => 'Edit Gmail Post',
            'new_item' => 'New Gmail Post',
            'view_item' => 'View Gmail Post',
            'view_items' => 'View Gmail Posts',
            'search_items' => 'Search Gmail Posts',
            'not_found' => 'No GPT posts found',
            'not_found_in_trash' => 'No GPT posts found in Trash',
            'all_items' => 'All Gmail Posts',
            'archives' => 'Gmail Post Archives',
            'attributes' => 'Gmail Post Attributes',
            'insert_into_item' => 'Insert into Gmail Post',
            'uploaded_to_this_item' => 'Uploaded to this Gmail Post',
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
            'rewrite'            => array('slug' => 'gmail-posts'),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 5,
            'supports'           => array('title', 'editor', 'thumbnail', 'excerpt'),
            'menu_icon' => 'dashicons-google',
        );

        register_post_type('gmail_posts', $args);
    }

    // Register ACF Field Groups and Fields for Gmail Posts
    public function register_acf_field_groups() {
        // Check if ACF plugin is active
        if (function_exists('acf_add_local_field_group')) {
            // Define and add ACF field groups and fields for Gmail Posts.
            acf_add_local_field_group(array(
                'key' => 'group_gmail_post_fields',
                'title' => 'Gmail Post Fields',
                'location' => array(
                    array(
                        array(
                            'param' => 'post_type',
                            'operator' => '==',
                            'value' => 'gmail_posts',
                        ),
                    ),
                ),
            ));
            acf_add_local_field(array(
                'parent' => 'group_gmail_post_fields',
                'key' => 'field_gpt_post_ids',
                'label' => 'GPT Post ID (One-to-One Relationship)',
                'name' => 'gpt_post_ids',
                'type' => 'relationship',
                'post_type' => array('gpt_posts'),
                'return_format' => 'id',
                'multiple' => false,
            ));
            
            // Add ACF fields for Gmail Posts
            
        }
    }

    // Register Custom Taxonomy for Gmail Posts
    public function register_custom_taxonomy() {
        $taxonomy_labels = array(
            'name' => 'Gmail Categories',
            'singular_name' => 'Gmail Category',
            'menu_name' => 'Gmail Categories',
        );

        register_taxonomy('gmail_categories', 'gmail_posts', array(
            'labels' => $taxonomy_labels,
            'rewrite' => array('slug' => 'gpt-categories'),
            'hierarchical' => true,
            'public' => true,
            'show_in_menu' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
        ));

        wp_insert_term('From Automated Gmail Inbox Extraction', 'gmail_categories');
        wp_insert_term('From Manual Gmail Inbox Extraction', 'gmail_categories');
    }

    // Deactivate Custom Post Type - Gmail Posts
    public function deactivate_gmail_post_type() {
        unregister_post_type('gmail_posts');
    }

    // Uninstall Custom Post Type - Gmail Posts
    public function uninstall_gmail_post_type() {
        // Define the custom post type
        $post_type = 'gmail_posts';

        // Check if the custom post type exists
        if (post_type_exists($post_type)) {
            $posts = get_posts(array('post_type' => $post_type, 'numberposts' => -1));

            // Delete ACF fields associated with Gmail Posts
            if (function_exists('acf_delete_field_group')) {
                $field_group_key = 'group_gmail_post_fields';
                acf_delete_field_group($field_group_key);
            }

            foreach ($posts as $post) {
                // Delete post meta data

                // Delete terms from the taxonomy if used
                $terms = wp_get_post_terms($post->ID, 'gmail_categories');
                foreach ($terms as $term) {
                    wp_remove_object_terms($post->ID, $term, 'gmail_categories');
                }

                // Delete the post
                wp_delete_post($post->ID, true);
            }
        }

        // Delete the custom taxonomy "Gmail Categories"
        unregister_taxonomy('gmail_categories');

        flush_rewrite_rules();
    }

}