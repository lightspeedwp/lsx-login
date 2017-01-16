<?php
/* Template Name: My Account */

get_header(); ?>
	<header class="page-header col-sm-12">
		<h1 class="page-title"><?php the_title(); ?></h1>
	</header><!-- .entry-header -->
	<div id="primary" class="content-area <?php //echo lsx_main_class(); ?>">

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