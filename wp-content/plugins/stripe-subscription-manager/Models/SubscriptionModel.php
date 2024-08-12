<?php

namespace StripeSubscriptionManager\Models;

use Stripe\StripeClient;

class SubscriptionModel
{
    private $stripe;
    private $max_posts_allowed;
    private $no_of_days;
    private $current_user_id;
    public static $page_name = 'Pricing Plans';
    // Table Name
    public static $table = 'stripe_subscriptions_trials';
    public static $table_stripe = 'stripe_subscriptions';

    public function __construct()
    {
        // Initialize the Stripe client
        $this->stripe = new StripeClient(get_option('stripe_secret_key', $_ENV['stripe_secret_key']));
        // Max Posts
        $this->max_posts_allowed =  post_left_limit();
        // No Of Days
        $this->no_of_days = get_option('no_of_days', $_ENV['no_of_days']);
        // User Id
        $this->current_user_id = get_current_user_id();
        // Block
        if (function_exists('acf_register_block')) {
            // Block Initialization
            add_action('acf/init', array($this, 'my_block'));
        }
    }

    private function post_counts() {
    return count_user_posts($this->current_user_id, ['gpt_posts']);
    }

    private function get_user_role()
    {
        $user = wp_get_current_user();
        $roles = (array) $user->roles;
        return !empty($roles) ? $roles[0] : false;
    }


    /**
     * Create the database table for storing subscription data.
     */
    public static function createSubscriptionTable()
    {

        global $wpdb;

        $tableName = $wpdb->prefix . self::$table;
        $tableName_stripe = $wpdb->prefix . self::$table_stripe;

        $charsetCollate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $tableName (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            customer_id VARCHAR(255) NOT NULL,
            trial_end_date DATETIME NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) $charsetCollate;";


        $sql = "CREATE TABLE IF NOT EXISTS $tableName_stripe (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            customer_id VARCHAR(255) NOT NULL,
            subscription_id varchar(255) NULL,
            price_id varchar(255) NOT NULL,
            session_id varchar(255) NULL,
            post_limit varchar(255) NULL,
            status varchar(50) DEFAULT 'pending' NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) $charsetCollate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
        self::create_packages_page();
    }
    public static function create_packages_page()
    {
        // Check if the page already exists
        $page_exists = get_page_by_title(self::$page_name, OBJECT, 'page');

        if ($page_exists === null) {
            $post_details = array(
                'post_title'    => self::$page_name,
                'post_content'  => '<!-- wp:acf/pricing-plans-block /-->',
                'post_status'   => 'publish',
                'post_author'   => get_current_user_id(),
                'post_type'     => 'page',
                'meta_input'    => array(
                    '_wp_page_template' => 'landingPageTemplate.php'
                )
            );
            wp_insert_post($post_details);
        }
    }

    public function my_block()
    {
        acf_register_block(
            array(
                'name' => 'pricing-plans-block',
                'title' => __('Pricing Plans Block'),
                'description' => __('Chicago Star Add Pricing'),
                'render_callback'   => array($this, 'render_acf_block'),
                'category' => 'blocks',
                'icon' => 'welcome-add-page',
                'keywords' => array('PricingBlock', 'pricing', 'PricingTable'),
                'multiple' => true,
                'mode' => 'edit',
            )
        );


        acf_add_local_field_group(array(
            'key' => 'group_pricing_plans',
            'title' => 'Pricing Plans',
            'fields' => array(
                array(
                    'key' => 'pricing_heading',
                    'label' => 'Heading',
                    'name' => 'pricing_heading',
                    'type' => 'text',
                ),
                array(
                    'key' => 'pricing_subheading',
                    'label' => 'Sub Heading',
                    'name' => 'pricing_subheading',
                    'type' => 'text',
                ),
                array(
                    'key' => 'pricing_discount',
                    'label' => 'Save Amount On Yearly',
                    'name' => 'pricing_discount',
                    'type' => 'text',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'block',
                        'operator' => '==',
                        'value' => 'acf/pricing-plans-block',
                    ),
                ),
            ),
        ));
    }
    public function render_acf_block($block, $content = '', $is_preview = false)
    {
        // Load template file
        $template_path = plugin_dir_path(__FILE__) . '../Blocks/SubscriptionsBlock.php';
        if (file_exists($template_path)) {
            include $template_path;
        }
    }


    public  function create_customer($userid)
    {
        $data = $_POST;
        if (!isset($data['role']) ||  $data['role'] == 'user'  || $data['role'] == '') {
            $name = $data['firstname'] . $data['lastname'];
            $email = $data['email'];

            $customer = $this->stripe->customers->create([
                'name' => $name,
                'description' => 'New Customer',
                'email' => $email,
                'payment_method' => 'pm_card_visa',
            ]);

            // Store the customer ID and trial end date in your database
            $customer_id = $customer->id;
            $trial_end_date = date('Y-m-d H:i:s', strtotime('+' . $this->no_of_days . ' days'));

            global $wpdb;
            $table_name = $wpdb->prefix . self::$table;

            $wpdb->insert(
                $table_name,
                array(
                    'user_id' => $userid,
                    'customer_id' => $customer_id,
                    'trial_end_date' => $trial_end_date,
                )
            );

            // Optionally, you can add the customer ID and trial end date to the user's meta data as well
            update_user_meta($userid, 'stripe_customer_id', $customer_id);
            update_user_meta($userid, 'stripe_trial_end_date', $trial_end_date);
        }
    }

    /**
     * Create a new subscription for a customer.
     *
     * @param string $customerId The Stripe Customer ID
     * @param string $priceId The Stripe Price ID
     * @param array $trialData Optional data for trial period
     * @return \Stripe\Subscription
     */
    public function createSubscription($customerId, $priceId, $trialData = [])
    {
        global $wpdb;
        $tableName = $wpdb->prefix . self::$table_stripe;
        $subscription =  $wpdb->get_row("SELECT * FROM " . $tableName . "
        WHERE user_id='" . $this->current_user_id . "' AND status = 'active'");

        if ($subscription != null) {
            return wp_send_json_error(['message' => 'You Have Already Subscribed To A Plan Please Cancel It First']);
        }

        try {
            $existingSession = $this->get_existing_session($customerId, $priceId);

            if ($existingSession) {
                return $existingSession->url;
            }

            $session = $this->stripe->checkout->sessions->create([
                'success_url' => get_site_url().'/subscription-success',
                'cancel_url' => get_site_url(). '/subscription-cancel',
                'payment_method_types' => ['card', 'paypal'],
                'line_items' => [
                    [
                        'price' => $priceId,
                        'quantity' => 1,
                    ],
                ],
                'mode' => 'subscription',
                'customer' => $customerId
            ]);

            // Post Price
            $post = get_posts(array(
                'post_type'     => 'packages',
                'meta_query'    => array(
                    'relation'      => 'AND',
                    array(
                        'key'       => 'stripe_price_id',
                        'value'     => $priceId,
                        'compare'   => '=',
                    ),
                ),
            ));

            $post_id = $post[0]->ID;


            $this->insert_subscription_details($customerId, $priceId, $session->id, 'pending', get_post_meta($post_id, 'post_limit', true));

            $checkoutUrl = $session->url;

            return $checkoutUrl;
        } catch (\Exception $e) {
            return wp_send_json_error(['message' => $e->getMessage()]);
        }
    }

    /**
     * Retrieve a subscription from Stripe.
     *
     * @param string $subscriptionId
     * @return \Stripe\Subscription
     */
    public function getSubscription($subscriptionId)
    {
        try {
            return $this->stripe->subscriptions->retrieve($subscriptionId);
        } catch (\Exception $e) {
            // Handle exceptions
            return null;
        }
    }

    /**
     * Cancel a subscription.
     *
     * @param string $subscriptionId
     * @return \Stripe\Subscription
     */
    public function cancelSubscription($subscriptionId)
    {
        try {
            $this->stripe->subscriptions->cancel($subscriptionId);
            return 'Subscription Has Been Canceled';

        } catch (\Exception $e) {
            // Handle exceptions
            return $e->getMessage();
        }
    }

    public static function cancelSubscriptionDatabase($customer_id,$subscriptionId){
    global $wpdb;
    $table_name = $wpdb->prefix . self::$table_stripe;

    $wpdb->update(
        $table_name,
        ['status' => 'canceled'],
        ['subscription_id' => $subscriptionId, 'customer_id' => $customer_id],
    );

    return 'Success';
    }

    public function display_user_trial_info()
    {
        $user_id = get_current_user_id();

        $html = "";

        if ($this->get_user_role() == 'user') {

            $left_post = $this->max_posts_allowed - $this->post_counts();
            global $wpdb;
            $table_name = $wpdb->prefix . self::$table;

            // Retrieve user's login trail and trial end date from the database
            $query = $wpdb->prepare("SELECT * FROM $table_name WHERE user_id = %d", $user_id);
            $result = $wpdb->get_row($query);

            $remaining_days = $this->no_of_days($result->trial_end_date);


            $page_pricing = get_page_by_title(self::$page_name, OBJECT, 'page');
            $page_url = get_permalink($page_pricing->ID);

            // if (is_subscribed() == null && $remaining_days <= 0) {
            //      $html .= '<script>location.replace("'. $page_url.'?msg="Your Trail Has Been Ended"")</script>';
            //     // wp_redirect($page_url);
            //     // exit;
            // }

            if (is_subscribed() == null) {
            if ($user_id) {
                if ($result) {
                    // Display remaining trial days
                    if ($remaining_days > 0) {
                        $html .= "<div class='d-flex badge bg-warning text-dark h-100 align-items-center'>" . $left_post . " Post Left</div>";
                        $html .= '<div  class="d-flex badge bg-danger h-100 align-items-center">' . $remaining_days . ' Days left in Trial </div>';
                    } else {
                        $html .= '<div  class="d-flex badge bg-danger h-100 align-items-center">Trial expired</div>';
                    }
                } else {
                    $html .= '<div  class="d-flex badge bg-danger h-100 align-items-center">User data not found</div>';
                }
            } else {
                $html .= '<div  class="d-flex badge bg-danger h-100 align-items-center">User not logged in</div>';
            }

                $html .= '<a href="' . esc_url($page_url) . '" class="btn btn-default bg-success">Subscribe</a>';

        }else{

                $html .= '<div class="d-flex badge bg-success">Current Package: '. current_package()['package_name'].'</div>';
                $html .= "<div class='d-flex badge bg-warning text-dark h-100 align-items-center'>" . $left_post . " Post Left</div>";
                $html .= '<div  class="d-flex badge bg-danger h-100 align-items-center">' . $this->no_of_days(current_package()['end_data']) . ' Days left</div>';
        }

        } else {
            $html .= '';
        }

        return $html;
    }

    public function no_of_days($date){
        $date_timestamp = strtotime($date);
        $current_timestamp = current_time('timestamp');

        $diff_in_seconds = $date_timestamp - $current_timestamp;
        $diff_in_days = floor($diff_in_seconds / (60 * 60 * 24));


        return $diff_in_days;
    }

    public function check_user_post_limit_before_save($data, $postarr)
    {
        if ($this->get_user_role() == 'user') {
            global $wpdb;
            $table_name = $wpdb->prefix . self::$table;
            $query = $wpdb->prepare("SELECT * FROM $table_name WHERE user_id = %d", $this->current_user_id);
            $result = $wpdb->get_row($query);
            $remaining_days = $this->no_of_days($result->trial_end_date);

            if (is_subscribed() == null && $remaining_days <= 0) {
                wp_die('Trail Expired');

            }
            if ($this->post_counts() >= $this->max_posts_allowed) {
                wp_die('You have reached the maximum limit of posts allowed.');
            }
        }
        return $data;
    }

    public function get_existing_session($customer_id, $priceId)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . self::$table_stripe;

        $result = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $table_name WHERE customer_id = %s AND price_id = %s AND status='active' LIMIT 1",
                $customer_id,
                $priceId
            )
        );
        if ($result != null) {
            $checkoutUrl = $this->stripe->checkout->sessions->retrieve(
                $result->session_id,
            );
            return $checkoutUrl;
        }
    }

    public function insert_subscription_details($customer_id, $priceId, $session_id, $status,$post_limit)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . self::$table_stripe;

        $data = [
            'user_id' => $this->current_user_id,
            'customer_id' => $customer_id,
            'price_id' => $priceId,
            'post_limit' => $post_limit+$this->post_counts(),
            'session_id' => $session_id,
            'status' => $status,
            'created_at' => current_time('mysql')
        ];

        $wpdb->insert(
            $table_name,
            $data
        );

        return true;
    }


    public function update_subscription_status($session_id, $customer_id, $subscription_id, $status)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . self::$table_stripe;
        $wpdb->update(
            $table_name,
            ['status' => $status, 'subscription_id' => $subscription_id],
            ['session_id' => $session_id, 'customer_id' => $customer_id]
        );
    }

    public function update_invoice_status($subscription_id, $status)
    {
    }

    public function get_user_email_by_stripe_customer_id($customer_id)
    {
        global $wpdb;
        $table_name_trials = $wpdb->prefix . self::$table;
        $table_name_users = $wpdb->prefix . 'users';

        $email = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT u.user_email
                 FROM $table_name_trials sst
                 JOIN $table_name_users u ON sst.user_id = u.ID
                 WHERE sst.customer_id = %s",
                $customer_id
            )
        );
        return $email;
    }
}
