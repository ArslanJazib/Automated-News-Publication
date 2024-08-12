<?php

namespace StripeSubscriptionManager\Controllers;

use StripeSubscriptionManager\Models\SubscriptionModel;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

class StripeWebhookHandler
{
    private $model;

    public function __construct()
    {
        $this->model = new SubscriptionModel();

        // Create Subscription Webhook
        add_action('rest_api_init', function () {
            register_rest_route('stripe', '/webhook', array(
                'methods' => 'POST',
                'callback' => [$this, 'handle_stripe_webhook'],
            ));
        });
    }

    public function handle_stripe_webhook(WP_REST_Request $request)
    {
        $payload = $request->get_body();
        $sig_header = $request->get_header('stripe-signature');
        $endpoint_secret = get_option('stripe_webhook_secret', $_ENV['stripe_webhook_secret']);

        // Verify the event by checking the signature
        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sig_header,
                $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return new WP_Error('invalid_payload', 'Invalid payload', array('status' => 400));
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            return new WP_Error('invalid_signature', 'Invalid signature', array('status' => 400));
        }

        // Handle the event
        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object;
                $this->handle_checkout_session_completed($session);
                break;
            case 'invoice.payment_succeeded':
                $invoice = $event->data->object;
                $this->handle_invoice_payment_succeeded($invoice);
                break;
            case 'customer.subscription.deleted':
                $subscription = $event->data->object;
                $this->handle_subscription_canceled($subscription);
                break;
            default:
                return new WP_Error('event_not_handled', 'Event type not handled', array('status' => 400));
        }

        return new WP_REST_Response('Webhook handled', 200);
    }

    private function handle_checkout_session_completed($session)
    {
        $customer_id = $session->customer;
        $subscription_id = $session->subscription;

        // Log Weebhook Data
        // $log_file = plugin_dir_path(__FILE__) . 'session_log.txt';
        // file_put_contents($log_file, print_r($session, true), FILE_APPEND);


        $this->model->update_subscription_status($session->id,$customer_id,$subscription_id, 'active');
        $user_email = $this->model->get_user_email_by_stripe_customer_id($customer_id);
        wp_mail($user_email, 'Subscription Successful', 'Your subscription has been successfully created.');
    }

    private function handle_invoice_payment_succeeded($invoice)
    {

    }

    private function handle_subscription_canceled($subscription)
    {
        $subscription_id = $subscription->id;
        $customer_id = $subscription->customer;

        $this->model->cancelSubscriptionDatabase($customer_id, $subscription_id, 'canceled');
        $user_email = $this->model->get_user_email_by_stripe_customer_id($customer_id);
        wp_mail($user_email, 'Subscription Canceled', 'Your subscription has been canceled.');
    }
}
