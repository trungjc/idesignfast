<?php
/*
Template Name: Full Width Page
*/
?>
<?php get_header(); ?>

<div id="content">
	<div class="container clearfix fullwidth">
		<div id="left-area">

			<?php while ( have_posts() ) : the_post(); ?>

				<?php get_template_part( 'content', get_post_format() ); ?>

				<?php
					if ( comments_open() && 'on' == et_get_option( 'fusion_show_pagescomments', 'false' ) )
						comments_template( '', true );
				?>

			<?php endwhile; ?>

		</div> <!-- end #left-area -->
	</div> <!-- .container -->
</div> <!-- #content -->

<?php get_footer(); ?>