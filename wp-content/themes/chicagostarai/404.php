<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package chicagostarai
 */

get_header();
?>

<style>
		body:has(.fourOfour) .content-body {
			min-height: calc(100vh - 150px);
		}

		body:has(.fourOfour) header .inner {
			margin-bottom: 0 !important;
		}

		@media screen and (max-width:991px) {
			body:has(.fourOfour) .content-body {
				min-height: calc(100vh - 132px);
			}
		}

		.content-body:has(.content-404) {
			display: flex;
			align-items: center;
			justify-content: center;
			background-repeat: no-repeat;
			background-size: cover;
			background-position: center;
		}

		.content-404 {
			text-align: center;
			display: flex;
			align-items: center;
			flex-direction: column;
		}

		.content-404 h1 {
			font-size: 120px;
		}

		.content-404 h3 {
			text-transform: capitalize;
			margin: 10px 0;
			font-size: 34px;
		}

		.content-404 p {
			font-size: 24px;
			max-width: 600px;
		}

		.content-404 .back-btn {
			min-height: 65px;
			min-width: 170px;
			display: flex;
			align-items: center;
			justify-content: center;
			color: #FFF4F4;
			text-align: center;
			text-transform: uppercase;
			font-size: 16px;
			background-color: #000;
			border-radius: 5px;
			margin-top: 10px;
		}

		@media screen and (max-width:767px) {
			.content-body:has(.content-404) {
				background-position: center right;
			}

			.content-404 h1 {
				font-size: 84px;
			}

			.content-404 h3 {
				font-size: 26px;
			}

			.content-404 p {
				font-size: 16px;
			}
		}
	</style>
	<div class="container-fluid gx-xl-4 gx-2">
		<div class="row dashboard-content g-xl-5 g-3 mb-5 fourOfour">
			<div class="col-12">
				<div class="content-body"
					style="background-image:url('<?php echo get_template_directory_uri() . '/assets/images/404-bg.png' ?>');">
					<div class="content-404">
						<h1>404</h1>
						<h3>page not found</h3>
						<p>we’re sorry. the page you requested could no be found
							Please go back to the home page</p>
						<a href="<?php echo home_url();?>" class=back-btn>Home Page</a>
					</div>
				</div>
			</div>
		</div>
	</div>

<?php
get_footer();