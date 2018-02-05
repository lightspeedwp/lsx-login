<?php
/* Template Name: Login */

get_header(); ?>

	<?php lsx_content_wrap_before(); ?>

	<?php if ( ! function_exists( 'lsx_is_banner_disabled' ) || ( function_exists( 'lsx_is_banner_disabled' ) && lsx_is_banner_disabled() ) ) { ?>
		<header class="page-header col-sm-12">
			<h1 class="page-title"><?php echo apply_filters('lsx_login_title', get_the_title()); ?></h1>
		</header><!-- .entry-header -->
	<?php } ?>

	<div id="primary" class="content-area content-login col-sm-12 <?php //echo lsx_main_class(); ?>">

		<?php lsx_content_before(); ?>

		<main id="main" class="site-main" role="main">

			<?php if ( ! is_user_logged_in() && false !== ( $public_content = lsx_restricted_page_content() ) && '' !== ( $public_content = lsx_restricted_page_content() ) ) { ?>
				<article class="entry-content">
					<?php echo wp_kses_post( $public_content ); ?>
				</article>
			<?php } ?>

			<?php
				if ( class_exists( 'woocommerce' ) ) {
					echo do_shortcode( '[woocommerce_my_account]' );
					if ( function_exists( 'woocommerce_social_login_buttons') ) {
						woocommerce_social_login_buttons( get_permalink() );
					}
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