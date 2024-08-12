<?php 

namespace GPT\Controllers;

class CustomCptManager {
    public function __construct() {
        // Hook the ACF initialization to add field groups and fields
        add_action('acf/init', array($this, 'register_acf_field_groups'));
    }

    // Register Custom Post Type - Custom Posts
    public function register_custom_post_type() {
        $labels = array(
            'name' => 'Custom Posts',
            'singular_name' => 'Custom Post',
            'menu_name' => 'Custom Posts',
            'add_new' => 'Add New',
            'add_new_item' => 'Add New Custom Post',
            'edit_item' => 'Edit Custom Post',
            'new_item' => 'New Custom Post',
            'view_item' => 'View Custom Post',
            'view_items' => 'View Custom Posts',
            'search_items' => 'Search Custom Posts',
            'not_found' => 'No custom posts found',
            'not_found_in_trash' => 'No custom posts found in Trash',
            'all_items' => 'All Custom Posts',
            'archives' => 'Custom Post Archives',
            'attributes' => 'Custom Post Attributes',
            'insert_into_item' => 'Insert into Custom Post',
            'uploaded_to_this_item' => 'Uploaded to this Custom Post',
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
            'rewrite' => array('slug' => 'custom-posts'),
            'capability_type' => 'post',
            'has_archive' => true,
            'hierarchical' => false,
            'menu_position' => 6,
            'supports' => array('title', 'thumbnail', 'excerpt' , 'revisions'),
            'menu_icon' => 'dashicons-admin-post',
        );

        register_post_type('custom_posts', $args);
    }

    // Register ACF Field Groups and Fields for Custom Posts
    public function register_acf_field_groups() {
        // Check if ACF plugin is active
        if (function_exists('acf_add_local_field_group')) {
            // Define and add ACF field groups and fields for Custom Posts.
            acf_add_local_field_group(array(
                'key' => 'group_custom_post_fields',
                'title' => 'Custom Post Fields',
                'location' => array(
                    array(
                        array(
                            'param' => 'post_type',
                            'operator' => '==',
                            'value' => 'custom_posts',
                        ),
                    ),
                ),
            ));
            if (is_plugin_active('rssfeedsfetcher/rssfeedsfetcher.php')) {
                // Add ACF fields for Custom Posts
                acf_add_local_field(array(
                    'parent' => 'group_custom_post_fields',
                    'key' => 'field_source_repeater',
                    'label' => 'Source Repeater Field',
                    'name' => 'source_repeater',
                    'type' => 'repeater',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_custom_source_url',
                            'label' => 'Source URL',
                            'name' => 'custom_source_url',
                            'type' => 'text',
                        ),
                        array(
                            'key' => 'field_source_title',
                            'label' => 'Source Title',
                            'name' => 'custom_source_title',
                            'type' => 'text',
                        ),
                        array(
                            'key' => 'field_source_content',
                            'label' => 'Source Content',
                            'name' => 'custom_source_content',
                            'type' => 'textarea',
                        ),
                    ),
                ));

                acf_add_local_field(array(
                    'parent' => 'group_custom_post_fields',
                    'key' => 'field_scraped_post_ids',
                    'label' => 'Scraped Post IDs (One-to-Many Relationship)',
                    'name' => 'scraped_post_ids',
                    'type' => 'relationship',
                    'post_type' => array('scraped_posts'),
                    'return_format' => 'id',
                    'multiple' => true,
                ));
            }
        }
    }

    // Register Custom Taxonomy for Custom Posts
    public function register_custom_taxonomy() {
        $taxonomy_labels = array(
            'name' => 'Custom Categories',
            'singular_name' => 'Custom Category',
            'menu_name' => 'Custom Categories',
        );

        register_taxonomy('custom_categories', 'custom_posts', array(
            'labels' => $taxonomy_labels,
            'rewrite' => array('slug' => 'custom-categories'),
            'hierarchical' => true,
            'public' => true,
            'show_in_menu' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
        ));
    }

    // Deactivate Custom Post Type
    public function deactivate_custom_post_type() {
        unregister_post_type('custom_posts');
    }

    // Uninstall Custom Post Type
    public function uninstall_custom_post_type() {
        $post_type = 'custom_posts';

        if (post_type_exists($post_type)) {
            $posts = get_posts(array('post_type' => $post_type, 'numberposts' => -1));

            // Delete ACF fields associated with Custom Posts
            if (function_exists('acf_delete_field_group')) {
                $field_group_key = 'group_custom_post_fields';
                acf_delete_field_group($field_group_key);
            }

            foreach ($posts as $post) {
                // Delete the post meta data
                delete_post_meta($post->ID, 'source_repeater');
                delete_post_meta($post->ID, 'scraped_post_ids');

                // Delete the post
                wp_delete_post($post->ID, true);
            }
        }

        // Delete the custom taxonomy for Custom Posts
        unregister_taxonomy('custom_categories');

        flush_rewrite_rules();
    }

}