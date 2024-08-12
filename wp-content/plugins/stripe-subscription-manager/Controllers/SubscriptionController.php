<?php

namespace StripeSubscriptionManager\Controllers;

use StripeSubscriptionManager\Models\SubscriptionModel;
use StripeSubscriptionManager\Views\AdminView;

class SubscriptionController
{
    private $model;
    private $view;

    public function __construct()
    {
        $this->model = new SubscriptionModel();
        $this->view = new AdminView($this);
        // Subscription Hook AJAX Hook
        add_action('wp_ajax_nopriv_subscribe_action', array($this, 'subscribe_action_callback'));
        add_action('wp_ajax_subscribe_action', array($this, 'subscribe_action_callback'));

        // Cancel subscription
        add_action('wp_ajax_nopriv_cancel_subscription', array($this, 'cancel_subscription_callback'));
        add_action('wp_ajax_cancel_subscription', array($this, 'cancel_subscription_callback'));
    }

    public  function cancel_subscription_callback()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'stripe_subscriptions';
        $query = "SELECT * FROM $table_name where user_id='" . get_current_user_id() . "' and status='active'";
        $results = $wpdb->get_row($query);

        if ($results!=null) {
            $sub_id = $results->subscription_id;
            $message = $this->model->cancelSubscription($sub_id);
            wp_send_json(['success'=>true,'message' => $message]);
        } else {
            wp_send_json(['error' => 'Error occurred']);
        }

        wp_die();
    }

    public  function subscribe_action_callback()
    {
        $post_id = $_POST['post_id'];
        $post= get_post($post_id);
        $priceId= get_field('stripe_price_id', $post->ID);

        global $wpdb;
        $table_name = $wpdb->prefix . 'stripe_subscriptions_trials';
        $query = "SELECT * FROM $table_name where user_id='".get_current_user_id()."'";
        $results = $wpdb->get_results($query);

        if ($results) {
            $cus_id=$results[0]->customer_id;
            $sessionUrl = $this->model->createSubscription($cus_id, $priceId);
            wp_send_json(['session_url' => $sessionUrl]);

        }else{
            wp_send_json(['error' => 'Error occurred']);
        }

        wp_die();
    }

    public function register()
    {
        add_action('admin_menu', [$this, 'addAdminMenu']);
        add_action('admin_post_save_stripe_settings', [$this, 'saveSettings']);
    }

    public function addAdminMenu()
    {
        add_menu_page('Stripe Subscriptions', 'Stripe Subscriptions', 'manage_options', 'stripe-subscription-manager', [$this, 'renderAdminPage']);
    }

    public function renderAdminPage()
    {
        $this->view->render('stripe_api_settings');
    }
    private function check_permissions()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        // Check for nonce security
        check_admin_referer('stripe_settings_save', 'stripe_settings_nonce');
    }
    /**
     * Validation FOr Get Options
     *
     * @return void
     */
    private function validation(array $fields)
    {
        $errors = [];

        foreach ($fields as $field) {
            $name = $field['name'];
            $type = $field['type'];

            if (!isset($_POST[$name])) {
                $errors[] = "The $name field is required.";
                continue;
            }

            $value = sanitize_text_field($_POST[$name]);

            switch ($type) {
                case 'string':
                    if (empty($value)) {
                        $errors[] = "The $name field cannot be empty.";
                    }
                    break;
                case 'integer':
                    if (!is_numeric($value) || intval($value) < 0) {
                        $errors[] = "The $name field must be a non-negative integer.";
                    }
                    break;
                default:
                    $errors[] = "The $name field has an invalid type.";
            }

            // Assign the sanitized and validated value back to the POST array
            $_POST[$name] = $value;
        }

        if (!empty($errors)) {
            throw new \Exception(implode('<br>', $errors));
        }
    }

    /**
     * Save settings handled in the admin form.
     */
    public function saveSettings()
    {
        $fields = array(
            array('name' => 'stripe_publishable_key', 'type' => 'string'),
            array('name' => 'stripe_secret_key', 'type' => 'string'),
            array('name' => 'generic_trail_limit', 'type' => 'integer'),
            array('name' => 'no_of_days', 'type' => 'integer'),
            array('name' => 'stripe_webhook_secret', 'type' => 'string')
        );

        self::check_permissions();

        try {
            $this->validation($fields);

            // Update options in the database
            foreach ($fields as $field) {
                update_option($field['name'], $_POST[$field['name']]);
            }

            // Redirect back to the settings page with a success message
            wp_redirect(add_query_arg(
                [
                    'page' => 'stripe-subscription-manager',
                    'status' => 'success',
                    'message' => 'Settings saved successfully'
                ],
                admin_url('admin.php')
            ));
        } catch (\Exception $e) {
            wp_redirect(add_query_arg(
                [
                    'page' => 'stripe-subscription-manager',
                    'status' => 'error',
                    'message' => $e->getMessage()
                ],
                admin_url('admin.php')
            ));
        }
        exit;
    }

}
