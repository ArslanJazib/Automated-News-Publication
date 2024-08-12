<?php

/**
 * Plugin Name: Stripe Subscription Manager
 * Description: Manages Stripe subscriptions, provides a trial, and restricts post creation.
 * Version: 1.0
 * Author: Bilal Khalid
 */

defined('ABSPATH') or die('I can see you');

if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
    require_once dirname(__FILE__) . '/vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

include_once(ABSPATH . 'wp-admin/includes/plugin.php');

// Include initializer
use StripeSubscriptionManager\Initializer;

register_activation_hook(__FILE__, [Initializer::class, 'activate']);
register_deactivation_hook(__FILE__, [Initializer::class, 'deactivate']);

add_action('plugins_loaded', [Initializer::class, 'register_classes']);