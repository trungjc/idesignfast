<?php
	$featured_slider_class = '';
	if ( 'on' == et_get_option( 'fusion_slider_auto', 'false' ) ) $featured_slider_class = ' et_slider_auto et_slider_speed_' . et_get_option( 'fusion_slider_autospeed', '7000' );
?>
<div id="et-slider-wrapper"<?php if ( '' != $featured_slider_class ) printf( ' class="%s"', esc_attr( $featured_slider_class ) ); ?>>
	<div id="et-slides">
	<?php
		$featured_cat = et_get_option( 'fusion_feat_cat' );
		$featured_num = (int) et_get_option( 'fusion_featured_num' );

		if ( 'false' == et_get_option( 'fusion_use_pages','false' ) ) {
			$featured_query = new WP_Query( apply_filters( 'et_featured_post_args', array(
				'posts_per_page' 	=> intval( $featured_num ),
				'cat' 				=> (int) get_catId( et_get_option('fusion_feat_posts_cat') )
			) ) );
		} else {
			global $pages_number;

			if ( '' != et_get_option( 'fusion_feat_pages' ) ) $featured_num = count( et_get_option( 'fusion_feat_pages' ) );
			else $featured_num = $pages_number;

			$et_featured_pages_args = array(
				'post_type'			=> 'page',
				'orderby'			=> 'menu_order',
				'order' 			=> 'ASC',
				'posts_per_page' 	=> (int) $featured_num,
			);

			if ( is_array( et_get_option( 'fusion_feat_pages', '', 'page' ) ) )
				$et_featured_pages_args['post__in'] = (array) array_map( 'intval', et_get_option( 'fusion_feat_pages', '', 'page' ) );

			$featured_query = new WP_Query( apply_filters( 'et_featured_page_args', $et_featured_pages_args ) );
		}

		while ( $featured_query->have_posts() ) : $featured_query->the_post();
			$post_id 			= get_the_ID();

			$bg = et_get_option( 'fusion_bg_image' );
			if ( '' == $bg ) $bg = get_template_directory_uri() . '/images/bg_fusion.jpg';

			$slide_bg 			= ( $slide_bg_url = get_post_meta( $post_id, '_et_slide_bg', true ) ) && '' != $slide_bg_url ? $slide_bg_url : $bg;

			$slide_subtitle 	= get_post_meta( $post_id, '_et_slide_subtitle', true );
			$slide_more_text 	= get_post_meta( $post_id, '_et_slide_more_text', true );
			$slide_more_link 	= get_post_meta( $post_id, '_et_slide_more_link', true );
			$more_link 			= '' != $slide_more_link ? $slide_more_link : get_permalink();
	?>
		<div class="et-slide"<?php if ( '' != $slide_bg ) echo ' style="background-image: url(' . esc_url( $slide_bg ) . ');"'; ?>>
			<div class="container clearfix">
				<div class="description">
					<h2><a href="<?php echo esc_url( $more_link ); ?>"><?php the_title(); ?></a></h2>
				<?php if ( '' != $slide_subtitle ) { ?>
					<p class="subtitle"><?php echo esc_html( $slide_subtitle ); ?></p>
				<?php } ?>

					<p><?php truncate_post( 180 ); ?></p>

					<a href="<?php echo esc_url( $more_link ); ?>" class="more">
						<?php if ( '' != $slide_more_text ) echo esc_html( $slide_more_text ); else esc_html_e( 'Read More', 'Fusion' ); ?>
					</a>
				</div> <!-- .description -->

			<?php
				$width = (int) apply_filters( 'slider_image_width', 535 );
				$height = (int) apply_filters( 'slider_image_height', 572 );
				$title = get_the_title();
				$thumbnail = get_thumbnail( $width, $height, '', $title, $title, false, 'Featured' );
				$thumb = $thumbnail["thumb"];
			?>
				<div class="featured-image">
					<a href="<?php the_permalink(); ?>"><?php print_thumbnail( $thumb, $thumbnail["use_timthumb"], $title, $width, $height, '' ); ?></a>
				</div> <!-- .featured-image -->
			</div> <!-- .container -->
		</div> <!-- .et-slide -->
	<?php
		endwhile; wp_reset_postdata();
	?>

	</div> <!-- #et-slides -->
</div> <!-- #et-slider-wrapper -->