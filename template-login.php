<?php
/* Template Name: Login */

get_header(); ?>
	<header class="page-header col-sm-12">
		<h1 class="page-title"><?php the_title(); ?></h1>
	</header><!-- .entry-header -->
	<div id="primary" class="content-area <?php echo lsx_main_class(); ?>">

		<?php lsx_content_before(); ?>

		<main id="main" class="site-main" role="main">

			<?php lsx_content_top(); ?>

			<?php 
			
			
			wp_login_form();
			
			?>

			<?php lsx_content_bottom(); ?>

		</main><!-- #main -->

		<?php lsx_content_after(); ?>
		
	</div><!-- #primary -->

	<section id="home-widgets">
	
		<?php if ( ! dynamic_sidebar( 'sidebar-home' ) ) : ?>
		
		
		<?php endif; // end sidebar widget area ?>
		
	</section>	


<?php get_footer(); ?>