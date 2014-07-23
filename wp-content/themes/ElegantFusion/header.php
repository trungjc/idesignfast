<!DOCTYPE html>
<!--[if IE 6]>
<html id="ie6" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 7]>
<html id="ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html id="ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 6) | !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<title><?php elegant_titles(); ?></title>
	<?php elegant_description(); ?>
	<?php elegant_keywords(); ?>
	<?php elegant_canonical(); ?>

	<?php do_action( 'et_head_meta' ); ?>

	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

	<?php $template_directory_uri = get_template_directory_uri(); ?>
	<!--[if lt IE 9]>
		<script src="<?php echo esc_attr( $template_directory_uri . '/js/html5.js"' ); ?>" type="text/javascript"></script>
	<![endif]-->

	<script type="text/javascript">
		document.documentElement.className = 'js';
	</script>

	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
	<?php do_action( 'et_header_top' ); ?>
	<header id="main-header">
		<div id="top-area">
			<div class="top-content container clearfix">
				<?php $logo = ( $user_logo = et_get_option( 'fusion_logo' ) ) && '' != $user_logo ? $user_logo : $template_directory_uri . '/images/logo.png'; ?>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="<?php echo esc_attr( $logo ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" id="logo"/></a>

				<?php do_action( 'et_header_menu' ); ?>

				<div id="menu-wrap">
					<nav id="top-menu">
					<?php
						$menuClass = 'nav';
						if ( 'on' == et_get_option( 'fusion_disable_toptier' ) ) $menuClass .= ' et_disable_top_tier';
						$primaryNav = '';
						if ( function_exists( 'wp_nav_menu' ) ) {
							$primaryNav = wp_nav_menu( array( 'theme_location' => 'primary-menu', 'container' => '', 'fallback_cb' => '', 'menu_class' => $menuClass, 'echo' => false ) );
						}
						if ( '' == $primaryNav ) { ?>
							<ul class="<?php echo esc_attr( $menuClass ); ?>">
								<?php if ( 'on' == et_get_option( 'fusion_home_link' ) ) { ?>
									<li <?php if ( is_home() ) echo( 'class="current_page_item"' ); ?>><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home','Fusion' ); ?></a></li>
								<?php }; ?>

								<?php show_page_menu( $menuClass, false, false ); ?>
								<?php show_categories_menu( $menuClass, false ); ?>
							</ul>
						<?php }
						else echo( $primaryNav );
					?>
					</nav> <!-- #top-menu -->

					<ul id="social-icons">
					<?php
						$et_rss_url = '' != et_get_option( 'fusion_rss_url' ) ? et_get_option( 'fusion_rss_url' ) : get_bloginfo( 'rss2_url' );
						if ( 'on' == et_get_option( 'fusion_show_twitter_icon', 'on' ) ) $social_icons['twitter'] = array( 'image' => $template_directory_uri . '/images/twitter.png', 'url' => et_get_option( 'fusion_twitter_url' ), 'alt' => __( 'Twitter', 'Fusion' ) );
						if ( 'on' == et_get_option( 'fusion_show_rss_icon', 'on' ) ) $social_icons['rss'] = array( 'image' => $template_directory_uri . '/images/rss.png', 'url' => $et_rss_url, 'alt' => __( 'Rss', 'Fusion' ) );
						if ( 'on' == et_get_option( 'fusion_show_facebook_icon','on' ) ) $social_icons['facebook'] = array( 'image' => $template_directory_uri . '/images/facebook.png', 'url' => et_get_option( 'fusion_facebook_url' ), 'alt' => __( 'Facebook', 'Fusion' ) );

						$social_icons = apply_filters( 'et_social_icons', $social_icons );

						if ( ! empty( $social_icons ) ) {
							foreach ( $social_icons as $icon ) {
								if ( $icon['url'] )
									printf( '<li><a href="%s" target="_blank"><img src="%s" alt="%s" /></a></li>', esc_url( $icon['url'] ), esc_attr( $icon['image'] ), esc_attr( $icon['alt'] ) );
							}
						}
					?>
					</ul> <!-- #social-icons -->
				</div> <!-- #menu-wrap -->
			<?php if ( ! is_home() ) get_template_part( 'includes/top_info' ); ?>
			</div> <!-- .container -->
		</div> <!-- #top-area -->

		<?php if ( 'on' == et_get_option( 'fusion_featured', 'on' ) && is_home() ) get_template_part( 'includes/featured' ); ?>

	<?php if ( ! is_home() ) { ?>
		<div id="breadcrumbs-wrapper">
			<div class="container clearfix">
				<div id="et-search-form">
					<form method="get" class="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>/">
						<input type="text" value="<?php esc_attr_e('Search This Site...', 'Fusion'); ?>" name="s" id="search_input" />
						<input type="image" alt="<?php echo esc_attr( 'Submit', 'Fusion' ); ?>" src="<?php echo esc_attr( get_template_directory_uri() . '/images/search-icon.png' ); ?>" id="search_submit" />
					</form>
				</div> <!-- #et-search-form -->

				<?php get_template_part( 'includes/breadcrumbs' ); ?>
			</div> <!-- .container -->
		</div> <!-- #breadcrumbs-wrapper -->
	<?php } ?>
	</header> <!-- #main-header -->