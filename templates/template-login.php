<?php
/* Template Name: Login */

get_header(); ?>

	<header class="page-header col-sm-12">
		<h1 class="page-title"><?php echo apply_filters('lsx_login_title', get_the_title()); ?></h1>
	</header><!-- .entry-header -->
	<div id="primary" class="content-area content-login <?php //echo lsx_main_class(); ?>">

		<?php //lsx_content_before(); ?>

		<main id="main" class="site-main" role="main">

			<?php //lsx_content_top(); ?>

			<?php
				if ( class_exists( 'woocommerce' ) ) {
					echo do_shortcode( '[woocommerce_my_account]' );
				}
				else {
					?>
					<div class="row">
						<div class="col-sm-6">
							<?php lsx_login_form(); ?>
						</div>
						<div class="col-sm-6">
							<?php lsx_password_reset_form(); ?>
						</div>
					</div>
					<?php
				}
			?>

			<?php //lsx_content_bottom(); ?>

		</main><!-- #main -->

		<?php //lsx_content_after(); ?>
		
	</div><!-- #primary -->

<?php get_footer(); ?>