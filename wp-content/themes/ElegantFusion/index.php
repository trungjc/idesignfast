<?php get_header(); ?>

<div id="content">
	<div class="container clearfix">
		<div id="left-area">

		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
			<?php get_template_part('includes/entry', 'index'); ?>
		<?php
		endwhile;
			if ( function_exists( 'wp_pagenavi' ) ) wp_pagenavi();
			else get_template_part( 'includes/navigation', 'entry' );
		else:
			get_template_part( 'includes/no-results', 'entry' );
		endif; ?>

		</div> <!-- #left-area -->

		<?php get_sidebar(); ?>
	</div> <!-- .container -->
</div> <!-- #content -->

<?php get_footer(); ?>