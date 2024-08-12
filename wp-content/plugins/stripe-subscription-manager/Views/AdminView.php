<?php

namespace StripeSubscriptionManager\Views;

class AdminView
{
    private $controller;

    public function __construct($controller)
    {
        $this->controller = $controller;
    }

    public function render($filename)
    {
        $status = isset($_GET['status']) ? $_GET['status'] : '';
        $message = isset($_GET['message']) ? $_GET['message'] : '';
        // Assume these functions retrieve stored options from the WordPress database
        $publishable_key = get_option('stripe_publishable_key', $_ENV['stripe_publishable_key']);
        $secret_key = get_option('stripe_secret_key', $_ENV['stripe_secret_key']);
        $webhook_secret = get_option('stripe_webhook_secret', $_ENV['stripe_webhook_secret']);
        $trail = get_option('generic_trail_limit', $_ENV['generic_trail_limit']);
        $no_of_days=get_option('no_of_days', $_ENV['no_of_days']);
        return include_once plugin_dir_path(__DIR__) . 'resources/views/'.$filename.'.php';
    }


}