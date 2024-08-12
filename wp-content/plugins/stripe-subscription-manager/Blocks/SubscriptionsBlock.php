<?php
// ACF Fields (get_field_default function is defined in Helpers.php)
$pricing_heading = get_field_default('pricing_heading', 'Ready To Get Started?');
$pricing_subheading = get_field_default('pricing_subheading', 'Choose a plan that suits your business needs');
$pricing_discount = get_field_default('pricing_discount', 'Save 65%');


?>
<!-- get started section here -->
<section class="section-get-started">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2><?php echo $pricing_heading; ?></h2>
                <p><?php echo $pricing_subheading; ?></p>
                <div class="pricing-plan">
                    <ul>
                        <li>Monthly</li>
                        <li>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault">
                            </div>
                        </li>
                        <li>Yearly</li>
                    </ul>
                    <p><?php echo $pricing_discount; ?></p>
                </div>
                <div class="car-wrapper">
                    <?php

                    $images = [
                        '<svg width="32" height="33" viewBox="0 0 32 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M25.0669 13.4587C23.5602 13.4587 22.2136 14.1921 21.3736 15.3121C20.5336 14.1921 19.1869 13.4587 17.6802 13.4587C15.1336 13.4587 13.0669 15.5387 13.0669 18.0987C13.0669 19.0854 13.2269 20.0054 13.4936 20.8454C14.8002 24.9921 18.8536 27.4854 20.8536 28.1654C21.1336 28.2587 21.6002 28.2587 21.8802 28.1654C23.8802 27.4854 27.9336 25.0054 29.2402 20.8454C29.5202 19.9921 29.6669 19.0854 29.6669 18.0987C29.6802 15.5387 27.6136 13.4587 25.0669 13.4587Z" fill="#005194" />
                                                <path d="M27.6665 11.3653C27.6665 11.672 27.3598 11.872 27.0665 11.792C25.2665 11.3253 23.2932 11.712 21.7998 12.7787C21.5065 12.992 21.1065 12.992 20.8265 12.7787C19.7732 12.0053 18.4932 11.5787 17.1465 11.5787C13.7065 11.5787 10.9065 14.392 10.9065 17.8587C10.9065 21.6187 12.7065 24.432 14.5198 26.312C14.6132 26.4053 14.5332 26.5653 14.4132 26.512C10.7732 25.272 2.6665 20.1253 2.6665 11.3653C2.6665 7.49866 5.77317 4.37866 9.61317 4.37866C11.8932 4.37866 13.9065 5.472 15.1732 7.16533C16.4532 5.472 18.4665 4.37866 20.7332 4.37866C24.5598 4.37866 27.6665 7.49866 27.6665 11.3653Z" fill="#005194" />
                                            </svg>',
                        '<svg width="32" height="33" viewBox="0 0 32 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                              <path d="M29.3331 7.85869V20.632C29.3331 24.312 26.3464 27.2987 22.6664 27.2987H9.3331C8.71977 27.2987 8.1331 27.2187 7.55977 27.0587C6.7331 26.832 6.46643 25.7787 7.07977 25.1654L21.2531 10.992C21.5464 10.6987 21.9864 10.632 22.3998 10.712C22.8264 10.792 23.2931 10.672 23.6264 10.352L27.0531 6.91202C28.3064 5.65869 29.3331 6.07202 29.3331 7.85869Z" fill="white"/>
                              <path d="M19.5198 10.0587L5.55984 24.0187C4.91984 24.6587 3.85317 24.4987 3.4265 23.6987C2.93317 22.7921 2.6665 21.7387 2.6665 20.6321V7.85872C2.6665 6.07205 3.69317 5.65872 4.9465 6.91205L8.3865 10.3654C8.9065 10.8721 9.75984 10.8721 10.2798 10.3654L15.0532 5.57872C15.5732 5.05872 16.4265 5.05872 16.9465 5.57872L19.5332 8.16539C20.0398 8.68539 20.0398 9.53872 19.5198 10.0587Z" fill="white"/>
                           </svg>',
                        '<svg width="32" height="33" viewBox="0 0 32 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                              <path d="M23.88 14.5387H19.76V4.93872C19.76 2.69872 18.5467 2.24539 17.0667 3.92539L16 5.13872L6.97335 15.4054C5.73335 16.8054 6.25335 17.9521 8.12002 17.9521H12.24V27.5521C12.24 29.7921 13.4533 30.2454 14.9333 28.5654L16 27.3521L25.0267 17.0854C26.2667 15.6854 25.7467 14.5387 23.88 14.5387Z" fill="#005194"/>
                           </svg>'
                    ];
                    $imageCounter = 0;

                    $args = array(
                        'post_type' => 'packages',
                        'posts_per_page' => 10
                    );
                    $the_query = new WP_Query($args); ?>

                    <?php if ($the_query->have_posts()) : ?>

                        <?php while ($the_query->have_posts()) : $the_query->the_post(); ?>
                            <!-- 1st card -->
                            <div class="pricing-card  <?php
                                                        if (get_field('abbreviation', get_the_ID()) == "/yearly") {
                                                            echo "yearly-cards d-none";
                                                        } else {
                                                            echo "month-cards";
                                                        } ?>">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <span class="">
                                            <?php echo $images[$imageCounter]; ?>
                                        </span>
                                        <?php echo the_title(); ?>
                                    </h5>
                                    <p>
                                        <?php echo the_field('stripe_name', get_the_ID()); ?>
                                    </p>
                                    <h4>
                                        <span class="price-bold updates-price d-block">$<?php echo the_field('field_price', get_the_ID()); ?></span>
                                        <?php echo the_field('abbreviation', get_the_ID()) ?>
                                    </h4>
                                    <?php the_content(); ?>
                                    <?php
                                    $is_subscribed = is_package_subscribed(get_field('stripe_price_id', get_the_ID()));
                                    if ($is_subscribed == false) {
                                    ?>
                                        <a class="btn subscribe-button" data-post-id="<?php echo get_the_ID(); ?>" href="">Subscribe</a>
                                    <?php } else { ?>
                                        <a class="btn bg-danger btn-cancel-subscription" href="">Cancel Subscription</a>

                                    <?php } ?>
                                </div>
                            </div>
                        <?php
                            $imageCounter++;
                            if ($imageCounter >= count($images)) {
                                $imageCounter = 0;
                            }
                        endwhile; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- get started section end here  -->
<script type="text/javascript">
    jQuery("#flexSwitchCheckDefault").change(function() {
        if (this.checked) {
            jQuery('.yearly-cards').removeClass('d-none');
            jQuery('.month-cards').addClass('d-none');
        } else {
            jQuery('.yearly-cards').addClass('d-none');
            jQuery('.month-cards').removeClass('d-none');
        }
    });


    jQuery(document).ready(function($) {
        $('.pricing-card .subscribe-button').click(function(e) {
            e.preventDefault();
            var postId = $(this).data('post-id');
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: {
                    action: 'subscribe_action',
                    post_id: postId
                },
                success: function(response) {
                    if (response.session_url) {
                        window.location.href = response.session_url;
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: response.data.message,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        title: 'Error',
                        text: error,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });
    });

    jQuery(document).ready(function($) {
        $('.btn-cancel-subscription').click(function(e) {
            e.preventDefault();

            var subscriptionId = $(this).data('subscription-id');

            Swal.fire({
                title: 'Are you sure?',
                text: 'Do you really want to cancel your subscription?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, cancel it!',
                cancelButtonText: 'No, keep it'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'cancel_subscription',
                            subscription_id: subscriptionId
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire(
                                    'Canceled!',
                                    'Your subscription has been canceled.',
                                    'success'
                                ).then(() => {
                                    location.reload(); // Reload the page to update the subscription status
                                });
                            } else {
                                Swal.fire(
                                    'Failed!',
                                    'Failed to cancel subscription: ' + response.message,
                                    'error'
                                );
                            }
                        },
                        error: function() {
                            Swal.fire(
                                'Error!',
                                'An error occurred while canceling the subscription.',
                                'error'
                            );
                        }
                    });
                }
            });
        });
    });
</script>