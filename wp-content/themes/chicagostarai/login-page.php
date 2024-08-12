<?php

/**
 * Template Name: Login Page
 */
?>
<?php
if (isset($_POST['signin'])) {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = sanitize_user($_POST['username']);
        $password = $_POST['password'];
        $credentials = array(
            'user_login'    => $username,
            'user_password' => $password,
            'remember'      => true,
        );
        $user = wp_signon($credentials, false);
        if (!is_wp_error($user)) {
            // Login successful
            wp_redirect(home_url('/')); 
            exit;
        } else {
            // Login failed
            $error_message = $user->get_error_message();
            $error_message = str_replace('Lost your password?', '', $error_message);
            wp_redirect(home_url('/login/?err_message=' . urlencode($error_message))); // Redirect back to the login page with an error message
            exit;
        }
    }
}
?>
<?php

function custom_page_template($template)
{
    //echo '<pre>';print_r($template);exit;
    if (is_page('login')) {
        $template = get_template_directory() . '/login.php';
    }
    return $template;
}

add_filter('page_template', 'custom_page_template');

?>
<?php get_header();  ?>

<main id="primary" class="site-main hello">
    <?php
    while (have_posts()) :
        the_post();
    ?>
        <div class="login__screen">
            <div class="login__bg" style="background-image: url(<?php echo get_template_directory_uri() . '/assets/images/Background.png' ?>)"></div>

            <div class="login__content">
                <div class="inner">
                    <a href="#" class="site__logo">
                        <img src="<?php echo get_template_directory_uri() . '/assets/images/ai-logo.svg' ?>" alt="">
                    </a>
                    <div class="login__form">
                        <div class="form__header">
                            <h5>Login</h5>
                        </div>
                        <div class="form__body">
                            <h4 class="mb-5 text-center">Welcome to Chicago Star AI</h4>
                            <form method="POST" action="">

                                <div class="form__group">
                                    <input name="username" class="form__field" type="text" placeholder="Username/Email" required>
                                </div>
                                <div class="form__group">
                                    <input name="password" class="form__field" type="password" placeholder="Password" required>
                                </div>
                                <button name="signin" type="submit" class="btn submit__btn">Login</button>
                                <?php
                                // Display the custom login error message if it exists
                                if (isset($_GET['err_message'])) {
                                    echo '<span style="color:red;font-size: 12px;">' . $_GET['err_message'] . '</span>';
                                }
                                ?>
                            </form>
                        </div>
                        <div class="text-center"><b>
                            <a href="<?php echo home_url('/') .'signup' ?>">Create a new Account</a>
                            </b></div>
                    </div>
                </div>
            </div>
        </div>
    <?php
    endwhile; // End of the loop.
    ?>
</main><!-- #main -->
<style>
        .login__content .inner {
        width: 100%;
    }
    .login__form {
        background-color: rgba(255, 255, 255, 1);
        max-width: 568px;
        width: 100%;
        max-height: 100vh;
        border-radius: 12px;
        padding: 24px 0 28px;
        backdrop-filter: blur(20px);
        margin: 0 auto;
    }

    .form__header {
        border-bottom: 1px solid rgba(239, 236, 243, 1);
        text-align: center;
        padding: 0 24px 16px;
    }

    .form__body {
        padding: 20px;
    }

    .form__body .form__group {
        margin-bottom: 15px;
    }

    .form__body .form__field {
        height: 50px;
        border: 1px solid rgba(0, 0, 0, 0.2);
        padding: 20px;
        width: 100%;
        background-color: transparent;
        outline: none;
    }

    .form__body .submit__btn {
        width: 100%;
    }

    body:has(.login__form) header {
        display: none;
    }
</style>




<?php get_footer();
?>