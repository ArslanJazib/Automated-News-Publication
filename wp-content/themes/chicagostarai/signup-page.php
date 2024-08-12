<?php

/**
 * Template Name: Signup Page
 */
?>
<?php

    if (isset($_POST['signup'])) {

        $username = sanitize_user($_POST['username']);
        $firstname = sanitize_user($_POST['firstname']);
        $lastname = sanitize_user($_POST['lastname']);
        $useremail = sanitize_email($_POST['useremail']);
        $password = sanitize_text_field($_POST['password']);

        $user_data = array(
            'user_login' => $username,
            'user_pass'  => $password,
            'user_email' => $useremail,
            'first_name' => $firstname,
            'last_name'  => $lastname, 
            'role'       => 'user',
        );
        
        $user_id = wp_insert_user($user_data);
        
        if (is_wp_error($user_id)) {
            $error_message = $user_id->get_error_message();
            $error_message = str_replace('Lost your password?', '', $error_message);
            wp_redirect(home_url('/signup/?err_message=' . urlencode($error_message)));
            exit;

        } else {
            $user = get_user_by('id', $user_id);
            wp_set_current_user($user_id, $user->user_login);
            wp_set_auth_cookie($user_id);
            do_action('wp_login', $user->user_login);
            wp_redirect(home_url('/'));
            exit;

        }
    }
?>
<?php get_header();  ?>

<main id="primary" class="site-main hello">
    <?php
    while (have_posts()) :
        the_post();
    ?>
        <div class="login__screen">
            <div class="login__bg" style="background-image: url(<?php echo get_template_directory_uri() . '/assets/images/Background.png' ?>)"></div>

            <div class="signup__content">
                <div class="inner">
                    <a href="#" class="site__logo">
                    <img src="<?php echo get_template_directory_uri() . '/assets/images/ai-logo.svg' ?>" alt="">
                    </a>
                    <div class="login__form">
                        <div class="form__header">
                            <h5>SignUp</h5>
                        </div>
                        <div class="form__body">
                            <h4 class="mb-5 text-center">Welcome to Chicago Star AI</h4>
                            <form method="POST" action="">
                                <div class="form__group">
                                    <input name="firstname" class="form__field" type="text" placeholder="First Name" autocomplete="off" required>
                                </div>
                                <div class="form__group">
                                    <input name="lastname" class="form__field" type="text" placeholder="Last Name" autocomplete="off" required>
                                </div>
                                <div class="form__group">
                                    <input name="username" class="form__field" type="text" placeholder="Username" autocomplete="off" required>
                                </div>
                                <div class="form__group">
                                    <input name="useremail" class="form__field" type="email" placeholder="Email" autocomplete="off" required>
                                </div>
                                <div class="form__group">
                                    <input name="password" class="form__field" type="password" placeholder="Password" autocomplete="off" required>
                                </div>
                                <button name="signup" type="submit" class="btn submit__btn">SignUp</button>
                                <?php
                                if (isset($_GET['err_message'])) {
                                    echo '<span style="color:red;font-size: 12px;">' . $_GET['err_message'] . '</span>';
                                }
                                ?>
                            </form>
                        </div>
                        <div class="text-center">
                            Already have an account?    
                            <b>
                            <a href="<?php echo home_url('/') .'login' ?>">Login</a>
                            </b>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php
    endwhile; // End of the loop.
    ?>
</main><!-- #main -->
<style>
        .signup__content .inner {
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
        padding: 0 14px 16px;
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