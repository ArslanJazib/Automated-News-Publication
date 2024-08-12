<?php

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
		<!-- header section -->
		<header id="masthead" class="landing-site-header">
			<nav class="navbar navbar-expand-lg">
				<div class="container-lg header-container">
					<a class="navbar-brand header-logo" href="#">
						<img src="<?php echo get_template_directory_uri() . '/assets/images/ai-logo.svg' ?>" alt="img" /></a>
					<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
						<span class="navbar-toggler-icon"></span>
						<span class="navbar-toggler-icon"></span>
						<span class="navbar-toggler-icon"></span>
					</button>

					<div class="collapse navbar-collapse toggle-menu" id="navbarSupportedContent">
						<ul class="navbar-nav mx-auto" id="menu">
							<li class="nav-item">
								<a class="nav-link" aria-current="page" href="#home">Home </a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="#howitworks"> How it Works</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="#services"> Services </a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="#about">About</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="#contact">Contact</a>
							</li>
						</ul>
						<div class="header-btn">
							<a href="https://ai.chicagostarmedia.com/login/"> login</a>
							<a class="started-btn" href="https://ai.chicagostarmedia.com/signup/">get started</a>
						</div>
					</div>
				</div>
			</nav>
		</header>
		<!-- header section -->