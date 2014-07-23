<?php get_header(); ?>

<div id="content">
	<div class="container clearfix">
		<div id="left-area">
			<?php get_template_part( 'includes/no-results', '404' ); ?>
		</div> <!-- #left-area -->

		<?php get_sidebar(); ?>
	</div> <!-- .container -->
</div> <!-- #content -->

<?php get_footer(); ?>