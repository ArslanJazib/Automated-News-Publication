<?php

session_start();

if(is_page('login')){
	if(is_user_logged_in()) {
		wp_redirect(home_url('/'));
		exit();
	}
}
elseif(is_page('signup')){
	if(is_user_logged_in()) {
		wp_redirect(home_url('/'));
		exit();
	}
}
else{
	if(!is_user_logged_in()) {
		wp_redirect(home_url('login'));
		exit();
	}
}


/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package chicagostarai
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.css" />
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>
	<div id="page" class="site">
		<header id="masthead" class="site-header">
			<div class="backdrop">
				<img src="<?php echo get_template_directory_uri() ?>/assets/images/Background.png" alt="">
			</div>
			<div class="container-fluid gx-4">
				<div class="inner row g-5 mb-3 mb-md-5">
					<div class="col-lg-3">
						<div class="logo">
							<a href="<?php echo esc_url( home_url( '/landing-page' ) ); ?>">
								<img src="<?php echo get_template_directory_uri() ?>/assets/images/ai-logo.svg" alt="Chicago Logo">
							</a>
						</div>
					</div>

					<div class="col-lg-9 justify-content-between align-items-center">
                        <!-- desktop menu -->
                        <div class="row">
                            <div class="col d-flex justify-content-between">
                                <div class="d-flex justify-content-between align-items-center">
                                    <nav aria-label="breadcrumb" class="breadcrumb">
                                        <ol class="breadcrumb mb-0">
                                            <li class="breadcrumb-item"><a href="#">DashBoard</a></li>
                                            <li class="breadcrumb-item"><a href="#" class="fw-bold">
                                                    <?php echo get_the_title(); ?>
                                                </a></li>
                                        </ol>
                                    </nav>
                                </div>
                                <div class="d-flex gap-3 align-items-center">
                                    <?php
                                    if (shortcode_exists("user_subscription_info")) {
                                        echo do_shortcode("[user_subscription_info]");
                                    } ?>
                                    <?php if (is_user_logged_in()) { ?>
                                        <div class="btn-module">
                                            <a class="btn-transparent text-white text-decoration-underline reversed-btn" href="<?php echo wp_logout_url(home_url('login')); ?>">Sign out</a>
                                        </div>
                                    <?php } else {
                                        session_destroy();
                                    } ?>
                                </div>
                                </div>
                            </div>
                            <!-- <div class="settings-wrapper d-flex align-items-center">
                        <form class="d-flex input-group w-auto">
                            <span class="input-group-text bg-white border-0" id="search-addon">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="search" class="form-control border-0"
                                placeholder="Search"
                                aria-label="Search"
                                aria-describedby="search-addon"
                            />
                        </form>
                        <a href="" class="ms-4 d-flex align-items-center" style="color:#fff;"><i class="fa fa-cog fs-4"></i></a>
                        <a href="" class="ms-4 d-flex align-items-center" style="color:#fff;"><i class="fa-solid fa-bell fs-4"></i></a>
                    </div> -->
                        </div>
				</div>
			</div>

			<!-- mobile menu -->
			<nav id="mobile__menu" class="mob__menu d-lg-none bg-dark">
				<div class="container">
					<!-- <div class="settings-wrapper d-flex justify-content-between align-items-center">
						<form class="d-flex input-group w-auto">
							<span class="input-group-text bg-white border-0" id="search-addon">
								<i class="fas fa-search"></i>
							</span>
							<input type="search" class="form-control border-0"
								placeholder="Search"
								aria-label="Search"
								aria-describedby="search-addon"
							/>
						</form>
						<a href="" class="d-flex align-items-center" style="color:#fff;"><i class="fa fa-cog fs-4"></i></a>
						<a href="" class="d-flex align-items-center" style="color:#fff;"><i class="fa-solid fa-bell fs-4"></i></a>
					</div> -->
				</div>
			</nav>

		</header><!-- #masthead -->