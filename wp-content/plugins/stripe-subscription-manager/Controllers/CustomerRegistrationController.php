<?php

namespace StripeSubscriptionManager\Controllers;

use StripeSubscriptionManager\Models\SubscriptionModel;

class CustomerRegistrationController
{

    private $model;

    public function __construct()
    {
        $this->model = new SubscriptionModel();
        add_action('user_register', [$this->model, 'create_customer'], 10, 1);
        // Short Code
        add_shortcode('user_subscription_info', [$this, 'subscriptions_details']);
        // Before Save Post
        //add_action('save_post',[$this->model, 'check_user_post_limit_before_save'], 10, 3);
        add_filter('wp_insert_post_data', [$this->model, 'check_user_post_limit_before_save'], 10, 2);


    }


    public function subscriptions_details()
    {
        $subscription = $this->model->display_user_trial_info();

        // Return HTML output
        return $subscription;
    }
}
