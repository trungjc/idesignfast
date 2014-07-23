<article id="post-<?php the_ID(); ?>" <?php post_class('entry clearfix'); ?>>
	<h2 class="title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
<?php
	$index_postinfo = et_get_option( 'fusion_postinfo1' );

	$thumb = '';
	$width = (int) apply_filters( 'et_blog_image_width', 623 );
	$height = (int) apply_filters( 'et_blog_image_height', 200 );
	$classtext = '';
	$titletext = get_the_title();
	$thumbnail = get_thumbnail( $width, $height, $classtext, $titletext, $titletext, false, 'Indeximage' );
	$thumb = $thumbnail["thumb"];

	if ( $index_postinfo ) {
		echo '<p class="meta-info">';
		et_postinfo_meta( $index_postinfo, et_get_option( 'fusion_date_format', 'M j, Y' ), esc_html__( '0 comments', 'Fusion' ), esc_html__( '1 comment', 'Fusion' ), '% ' . esc_html__( 'comments', 'Fusion' ) );
		echo '</p>';
	}
?>

<?php if ( 'on' == et_get_option( 'fusion_thumbnails_index', 'on' ) && '' != $thumb ) { ?>
	<div class="entry-thumbnail">
		<a href="<?php the_permalink(); ?>">
			<?php print_thumbnail( $thumb, $thumbnail["use_timthumb"], $titletext, $width, $height, $classtext ); ?>
		</a>
	</div> 	<!-- end .entry-thumbnail -->
<?php } ?>
<?php if ( 'false' == et_get_option( 'fusion_blog_style', 'false' ) ) { ?>
	<p><?php truncate_post( 480 ); ?></p>
<?php } else { ?>
	<?php the_content(''); ?>
<?php } ?>
	<a href="<?php the_permalink(); ?>" class="read-more"><?php esc_html_e( 'Read More', 'Fusion' ); ?> <span>&raquo;</span></a>
</article> <!-- end .entry -->