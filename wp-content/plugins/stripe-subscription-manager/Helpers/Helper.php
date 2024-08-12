<?php



function get_field_default($name, $default = '')
{
    if (!empty(get_field($name))) {
        return get_field($name);
    } else {
        return $default;
    }
}

function is_package_subscribed($priceId)
{
    global $wpdb;

    $table_name_trials = $wpdb->prefix . 'stripe_subscriptions';

    $subscription =  $wpdb->get_row("SELECT * FROM " . $table_name_trials . " WHERE status='active' AND user_id='" . get_current_user_id() . "' AND price_id = '" . $priceId . "'");
    if ($subscription != null) {
        return true;
    } else {
        return false;
    }
}

function is_subscribed()
{
    global $wpdb;

    $table_name_trials = $wpdb->prefix . 'stripe_subscriptions';
    $subscription =  $wpdb->get_row("SELECT * FROM " . $table_name_trials . " WHERE user_id='" . get_current_user_id() . "' AND status = 'active'");
    return $subscription;
}

function current_package(): array
{

    $sub_id = is_subscribed()->subscription_id;
    $price_id = is_subscribed()->price_id;
    $stripe = new \Stripe\StripeClient(get_option('stripe_secret_key', $_ENV['stripe_secret_key']));
    $sub_r = $stripe->subscriptions->retrieve($sub_id);
    // $model= new \StripeSubscriptionManager\Models\SubscriptionModel();
    // $sub_r= $model->getSubscription($sub_id);
    // End Date
    $end_data = date('Y-m-d', $sub_r->current_period_end);
    // Plan Name
    $post = get_posts(array(
        'post_type'     => 'packages',
        'meta_query'    => array(
            'relation'      => 'AND',
            array(
                'key'       => 'stripe_price_id',
                'value'     => $price_id,
                'compare'   => '=',
            ),
        ),
    ));

    $post_id = $post[0]->ID;

    $array = array(
        'end_data' => $end_data,
        'package_name' => get_post_meta($post_id, 'stripe_name', true),
        'post_left' => is_subscribed()->post_limit
    );
    return $array;
}


function post_left_limit() : int{
    if(is_subscribed()!=null){
       return current_package()['post_left'];
    }else{
    return  get_option('generic_trail_limit', $_ENV['generic_trail_limit']);
    }
}