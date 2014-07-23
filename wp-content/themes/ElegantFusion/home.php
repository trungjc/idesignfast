<?php
	if ( 'on' == et_get_option( 'fusion_blog_style', 'false' ) ) {
		get_template_part( 'index' );
		return;
	}
?>

<?php get_header(); ?>

<?php if ( 'on' == et_get_option( 'fusion_display_services', 'false' ) ) : ?>
<div id="services">
	<div class="container clearfix">
	<?php
		$blurbs_number = (int) apply_filters( 'et_blurbs_number', 3 );
		for ( $i = 1; $i <= $blurbs_number; $i++ ){
			$service_query = new WP_Query( apply_filters( 'et_service_query_args', 'page_id=' . get_pageId( et_get_option( 'fusion_home_page_' . $i ) ), $i ) );
			while ( $service_query->have_posts() ) : $service_query->the_post();
				global $more;
				$more = 0;
				$page_title = ( $blurb_custom_title = get_post_meta( get_the_ID(), 'Blurbtitle', true ) ) && '' != $blurb_custom_title ? $blurb_custom_title : apply_filters( 'the_title', get_the_title() );
				$page_permalink = ( $blurb_custom_permalink = get_post_meta( get_the_ID(), 'Blurblink', true ) ) && '' != $blurb_custom_permalink ? $blurb_custom_permalink : get_permalink();

				echo '<div class="service' . ( 1 == $i ? ' first' : '' ) . ( $blurbs_number == $i ? ' last' : '' ) . '">';
					if ( ( $page_icon = get_post_meta( get_the_ID(), 'Icon', true ) ) && '' != $page_icon )
						printf( '<img src="%1$s" alt="%2$s" class="icon" />', esc_attr( $page_icon ), esc_attr( $page_title ) );

					echo '<h3>' . $page_title . '</h3>';

					if ( has_excerpt() ) the_excerpt();
					else the_content( '' );

					printf( '<a href="%s" class="read-more">%s <span>&raquo;</span></a>',
						esc_url( $page_permalink ),
						__( 'Read More', 'Fusion' )
					);

				echo '</div> <!-- end .service -->';
			endwhile;
			wp_reset_postdata();
		}
	?>
	</div> <!-- end .container -->
</div> <!-- end #services -->
<?php endif; // 'on' == et_get_option( 'fusion_display_blurbs', 'false' ) ?>

<div id="content">
	<div class="container clearfix">
<?php
if ( 'on' == et_get_option( 'fusion_show_testimonials', 'false' ) ) {

	$args = array(
		'orderby' 			=> 'rand',
		'post_type'			=> 'testimonial',
		'posts_per_page' 	=> (int) et_get_option( 'fusion_home_testimonials_number', 3 )
	);
	$et_testimonials_query = new WP_Query( apply_filters( 'et_home_testimonials_query_args', $args ) );
	if ( $et_testimonials_query->have_posts() ) :
?>
		<div id="testimonials">
			<div id="testimonials-wrap">
			<?php while ( $et_testimonials_query->have_posts() ) : $et_testimonials_query->the_post(); ?>
				<div class="testimonial">
				<?php
					$thumb = '';
					$width = 50;
					$height = 50;
					$classtext = 'author-img';
					$titletext = get_the_title();
					$thumbnail = get_thumbnail( $width, $height, $classtext, $titletext, $titletext, false, 'Testimonial' );
					$thumb = $thumbnail["thumb"];
					$company_name = get_post_meta( get_the_ID(), '_et_testimonial_company', true );
				?>
				<?php if ( '' != $thumb ) { ?>
					<div class="testimonial-image">
						<?php print_thumbnail( $thumb, $thumbnail["use_timthumb"], $titletext, $width, $height, $classtext ); ?>
					</div> <!-- .testimonial-image -->
				<?php } ?>
					<h2 class="title"><?php the_title(); ?></h2>
				<?php if ( '' != $company_name ) { ?>
					<span class="title"><?php echo esc_html( $company_name ); ?></span>
				<?php } ?>
					<?php the_content(''); ?>
				</div> <!-- end .testimonial -->
			<?php endwhile; ?>
			</div> <!-- end #testimonials-wrap -->
		</div> <!-- end #testimonials -->
	<?php endif; ?>
	<?php wp_reset_postdata(); ?>

<?php } // 'on' == et_get_option( 'fusion_show_testimonials', 'false' ) ?>

<?php if ( 'on' == et_get_option( 'fusion_show_recent_news', 'false' ) ) { ?>
		<div id="recent-updates">
			<h2><?php esc_html_e( 'Recent News', 'Fusion' ); ?></h2>

			<ul>
			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
				<?php
					$thumb = '';
					$width = (int) apply_filters( 'et_home_blog_image_width', 49 );
					$height = (int) apply_filters( 'et_home_blog_image_height', 49 );
					$titletext = get_the_title();
					$thumbnail = get_thumbnail( $width, $height, '', $titletext, $titletext, false, 'Blogimage' );
					$thumb = $thumbnail["thumb"];
				?>
				<li class="recent-update clearfix">
				<?php if ( '' != $thumb ) { ?>
					<div class="recent-updates-image">
						<a href="<?php the_permalink(); ?>">
							<?php print_thumbnail( $thumb, $thumbnail["use_timthumb"], $titletext, $width, $height, '' ); ?>
							<span class="overlay"></span>
						</a>
					</div> <!-- .recent-updates-image -->
				<?php } ?>

					<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
				<?php
					printf ( __( '<p class="meta-info">Posted by <a href="%1$s">%2$s</a> on %3$s in %4$s</p>', 'Fusion' ),
						esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
						get_the_author(),
						esc_html( get_the_time( et_get_option( 'fusion_date_format', 'M j, Y' ) ) ),
						get_the_category_list(', ')
					);
				?>
				</li>
			<?php endwhile; endif; ?>
			</ul>

			<?php if ( ( $more_news_link = et_get_option( 'fusion_more_news_link' ) ) && '' != $more_news_link ) { ?>
				<a href="<?php echo esc_url( $more_news_link ); ?>" class="read-more"><?php esc_html_e( 'More Blog Posts ', 'Fusion' ); ?> <span>&raquo;</span></a>
			<?php } ?>
		</div> <!-- end #recent-updates -->
<?php } // 'on' == et_get_option( 'fusion_show_recent_news', 'false' ) ?>

	</div> <!-- end .container -->
</div> <!-- end #content -->

<?php if ( 'on' == et_get_option( 'fusion_show_logos', 'false' ) ) { ?>
<div id="logos">
	<div class="container">
	<?php
		$logos_number = (int) apply_filters( 'et_logos_number', 4 );
		for ( $i = 1; $i <= $logos_number; $i++ ) {
			if ( ( $logo_path = et_get_option( 'fusion_logo_path_' . $i ) ) && '' != $logo_path )
				printf( '<a href="%s"><img src="%s" alt="%s"/></a>',
					esc_url( et_get_option( 'fusion_logo_url_' . $i, '#' ) ),
					esc_attr( $logo_path ),
					esc_attr( et_get_option( 'fusion_logo_alt_' . $i, '' ) )
				);
		}
	?>
	</div> <!-- end .container -->
</div> <!-- end #logos -->
<?php } // 'on' == et_get_option( 'fusion_show_logos', 'false' ?>

<?php get_footer(); ?>