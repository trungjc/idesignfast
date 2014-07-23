<?php
/*
Template Name: Search Page
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
					<div id="et-search" class="responsive">
						<div id="et-search-inner" class="clearfix">
							<p id="et-search-title"><span><?php esc_html_e('search this website','Fusion'); ?></span></p>
							<form action="<?php echo esc_url( home_url() ); ?>" method="get" id="et_search_form">
								<div id="et-search-left">
									<p id="et-search-word"><input type="text" id="et-searchinput" name="s" value="<?php esc_attr_e('search this site...','Fusion'); ?>" /></p>

									<p id="et_choose_posts"><label><input type="checkbox" id="et-inc-posts" name="et-inc-posts" /> <?php esc_html_e('Posts','Fusion'); ?></label></p>
									<p id="et_choose_pages"><label><input type="checkbox" id="et-inc-pages" name="et-inc-pages" /> <?php esc_html_e('Pages','Fusion'); ?></label></p>
									<p id="et_choose_date">
										<select id="et-month-choice" name="et-month-choice">
											<option value="no-choice"><?php esc_html_e('Select a month','Fusion'); ?></option>
											<?php
												global $wpdb, $wp_locale;

												$selected = '';
												$arcresults = $wpdb->get_results(
													$wpdb->prepare( "SELECT YEAR(post_date) AS %s, MONTH(post_date) AS %s, count(ID) as posts FROM $wpdb->posts GROUP BY YEAR(post_date), MONTH(post_date) ORDER BY post_date DESC", 'year', 'month' )
												);

												foreach ( (array) $arcresults as $arcresult ) {
													if ( isset($_POST['et-month-choice']) && ( $_POST['et-month-choice'] == ($arcresult->year . $arcresult->month) ) ) {
														$selected = ' selected="selected"';
													}
													echo "<option value='{$arcresult->year}{$arcresult->month}'{$selected}>{$wp_locale->get_month($arcresult->month)}" . ", {$arcresult->year}</option>";
													if ( $selected <> '' ) $selected = '';
												}
											?>
										</select>
									</p>

									<p id="et_choose_cat"><?php wp_dropdown_categories('show_option_all=Choose a Category&show_count=1&hierarchical=1&id=et-cat&name=et-cat'); ?></p>
								</div> <!-- #et-search-left -->

								<div id="et-search-right">
									<input type="hidden" name="et_searchform_submit" value="et_search_proccess" />
									<input class="et_search_submit" type="submit" value="<?php esc_attr_e('Submit','Fusion'); ?>" id="et_search_submit" />
								</div> <!-- #et-search-right -->
							</form>
						</div> <!-- end #et-search-inner -->
					</div> <!-- end #et-search -->

					<div class="clear"></div>
				</article> <!-- end .post-->

			<?php endwhile; ?>

		</div> <!-- end #left-area -->

		<?php if ( ! $fullwidth ) get_sidebar(); ?>
	</div> <!-- .container -->
</div> <!-- #content -->

<?php get_footer(); ?>