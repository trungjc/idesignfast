<?php
/**
 * The template for displaying posts on single pages
 *
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'clearfix entry entry-content' ); ?>>
<?php
	$thumb = '';
	$width = (int) apply_filters( 'et_blog_image_width', 623 );
	$height = (int) apply_filters( 'et_blog_image_height', 200 );
	$classtext = '';
	$titletext = get_the_title();
	$thumbnail = get_thumbnail( $width, $height, '', $titletext, $titletext, false, 'Indeximage' );
	$thumb = $thumbnail["thumb"];

	$postinfo = et_get_option( 'fusion_postinfo2' );
	$show_thumb = is_page() ? et_get_option( 'fusion_page_thumbnails', 'false' ) : et_get_option( 'fusion_thumbnails', 'on' );
?>
	<h1 class="title"><?php the_title(); ?></h1>
<?php
	if ( $postinfo && ! is_page() ) {
		echo '<p class="meta-info">';
		et_postinfo_meta( $postinfo, et_get_option( 'fusion_date_format', 'M j, Y' ), esc_html__( '0 comments', 'Fusion' ), esc_html__( '1 comment', 'Fusion' ), '% ' . esc_html__( 'comments', 'Fusion' ) );
		echo '</p>';
	}
?>

<?php if ( '' != $thumb && 'false' != $show_thumb ) { ?>
	<div class="entry-thumbnail">
		<?php print_thumbnail( $thumb, $thumbnail["use_timthumb"], $titletext, $width, $height, $classtext ); ?>
	</div> 	<!-- end .entry-thumbnail -->
<?php } ?>

<?php
	the_content();
	wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', 'Fusion' ), 'after' => '</div>' ) );
?>
</article> <!-- end .post-->