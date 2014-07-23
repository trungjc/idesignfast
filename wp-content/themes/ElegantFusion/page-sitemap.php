<?php
/*
Template Name: Sitemap Page
*/
?>
<?php
$et_ptemplate_settings = array();
$et_ptemplate_settings = maybe_unserialize( get_post_meta(get_the_ID(),'et_ptemplate_settings',true) );

$fullwidth = isset( $et_ptemplate_settings['et_fullwidthpage'] ) ? (bool) $et_ptemplate_settings['et_fullwidthpage'] : false;
?>
<?php get_header(); ?>

<div id="content">
	<div class="container clearfix<?php if ( $fullwidth ) echo ' fullwidth'; ?>">
		<div id="left-area">

			<?php while ( have_posts() ) : the_post(); ?>

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
					<div id="sitemap" class="responsive">
						<div class="sitemap-col">
							<h2><?php esc_html_e('Pages','Fusion'); ?></h2>
							<ul id="sitemap-pages"><?php wp_list_pages('title_li='); ?></ul>
						</div> <!-- end .sitemap-col -->

						<div class="sitemap-col">
							<h2><?php esc_html_e('Categories','Fusion'); ?></h2>
							<ul id="sitemap-categories"><?php wp_list_categories('title_li='); ?></ul>
						</div> <!-- end .sitemap-col -->

						<div class="sitemap-col<?php if (!$fullwidth) echo ' last'; ?>">
							<h2><?php esc_html_e('Tags','Fusion'); ?></h2>
							<ul id="sitemap-tags">
								<?php $tags = get_tags();
								if ($tags) {
									foreach ($tags as $tag) {
										echo '<li><a href="' . esc_url( get_tag_link( $tag->term_id ) ) . '">' . esc_html( $tag->name ) . '</a></li> ';
									}
								} ?>
							</ul>
						</div> <!-- end .sitemap-col -->

						<?php if (!$fullwidth) { ?>
							<div class="clear"></div>
						<?php } ?>

						<div class="sitemap-col<?php if ($fullwidth) echo ' last'; ?>">
							<h2><?php esc_html_e('Authors','Fusion'); ?></h2>
							<ul id="sitemap-authors" ><?php wp_list_authors('show_fullname=1&optioncount=1&exclude_admin=0'); ?></ul>
						</div> <!-- end .sitemap-col -->
					</div> <!-- end #sitemap -->

					<div class="clear"></div>
				</article> <!-- end .post-->

			<?php endwhile; ?>

		</div> <!-- end #left-area -->

		<?php if ( ! $fullwidth ) get_sidebar(); ?>
	</div> <!-- .container -->
</div> <!-- #content -->

<?php get_footer(); ?>