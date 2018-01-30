<?php
/* Template Name: My Account */

get_header(); ?>

	<?php if ( ! function_exists( 'lsx_is_banner_disabled' ) || lsx_is_banner_disabled() ) { ?>
		<header class="page-header col-sm-12">
			<h1 class="page-title"><?php echo apply_filters('lsx_login_title', get_the_title()); ?></h1>
		</header><!-- .entry-header -->
	<?php } ?>

	<div id="primary" class="content-area content-my-account <?php //echo lsx_main_class(); ?>">

		<?php //lsx_content_before(); ?>

		<main id="main" class="site-main" role="main">

			<?php //lsx_content_top(); ?>

			<?php
				if ( class_exists( 'woocommerce' ) ) {
					echo do_shortcode( '[woocommerce_my_account]' );
				}
				else {
					lsx_my_account_tabs();
				}
			?>

			<?php //lsx_content_bottom(); ?>

		</main><!-- #main -->

		<?php //lsx_content_after(); ?>
		
	</div><!-- #primary -->

<?php get_footer(); ?>