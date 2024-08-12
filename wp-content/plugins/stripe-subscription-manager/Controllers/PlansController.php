<?php

namespace StripeSubscriptionManager\Controllers;


class PlansController
{


    public function __construct()
    {
        add_action('acf/init', array($this, 'register_acf_field_groups'));
        add_action('init', [$this, 'register_packages_cpt']);
    }

    public function register_packages_cpt()
    {
        $labels = array(
            'name'               => 'Packages',
            'singular_name'      => 'Package',
            'add_new'            => 'Add New',
            'add_new_item'       => 'Add New Package',
            'edit_item'          => 'Edit Package',
            'new_item'           => 'New Package',
            'all_items'          => 'All Packages',
            'view_item'          => 'View Package',
            'search_items'       => 'Search Packages',
            'not_found'          => 'No packages found',
            'not_found_in_trash' => 'No packages found in Trash',
            'menu_name'          => 'Packages',
        );

        $args = array(
            'labels'              => $labels,
            'public'              => true,
            'has_archive'         => true,
            'supports'            => array('title', 'editor'),
            'rewrite'             => array('slug' => 'packages'),
            'show_ui' => true,
            'menu_position' => 2,
            'menu_icon'           => 'dashicons-excerpt-view',
        );

        register_post_type('packages', $args);
    }

    public function register_acf_field_groups()
    {
        // Check if ACF plugin is active
        if (function_exists('acf_add_local_field_group')) {
            // Define and add ACF field groups and fields for Custom Posts.
            acf_add_local_field_group(array(
                'key' => 'group_packages_post_fields',
                'title' => 'Package Fields',
                'location' => array(
                    array(
                        array(
                            'param' => 'post_type',
                            'operator' => '==',
                            'value' => 'packages',
                        ),
                    ),
                ),
            ));

            // Add ACF fields for Packages Posts
            acf_add_local_field(array(
                'parent' => 'group_packages_post_fields',
                'key' => 'field_stripe_name',
                'label' => 'Stripe Name',
                'name' => 'stripe_name',
                'type' => 'text',
                'required' => 1,
                'placeholder' => 'eg: Chicago Star - Monthly subscription'
            ));

            acf_add_local_field(array(
                'parent' => 'group_packages_post_fields',
                'key' => 'field_stripe_product_id',
                'label' => 'Stripe Product ID',
                'name' => 'stripe_product_id',
                'type' => 'text',
                'required' => 1,
                'placeholder' => 'eg: prod_Q4muBlevWkjz8z'
            ));

            acf_add_local_field(array(
                'parent' => 'group_packages_post_fields',
                'key' => 'field_stripe_price_id',
                'label' => 'Stripe Price ID',
                'name' => 'stripe_price_id',
                'type' => 'text',
                'required' => 1,
                'placeholder' => 'eg: price_1PEdL0J0PI7zyc4fp7bgAWhN'
            ));

            acf_add_local_field(array(
                'parent' => 'group_packages_post_fields',
                'key' => 'field_price',
                'label' => 'Price',
                'name' => 'price',
                'type' => 'number',
                'step' => '0.01',
                'required' => 1,
                'placeholder' => 'eg: 9.99'
            ));

            acf_add_local_field(array(
                'parent' => 'group_packages_post_fields',
                'key' => 'field_abbreviation',
                'label' => 'Abbreviation',
                'name' => 'abbreviation',
                'type' => 'select',
                'choices' => array(
                    ''=>'Select Here',
                    '/month' => 'Monthly',
                    '/yearly' => 'Yearly',
                ),
                'required' => 1,
                'placeholder' => 'Select'
            ));

            acf_add_local_field(array(
                'parent' => 'group_packages_post_fields',
                'key' => 'field_post_limit',
                'label' => 'Post Limit',
                'name' => 'post_limit',
                'type' => 'number',
                'required' => 1,
                'placeholder' => 'eg: 10'
            ));
        }
    }
}
