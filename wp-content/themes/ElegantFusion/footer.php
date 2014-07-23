	<?php get_sidebar( 'footer' ); ?>

	<div id="footer-bottom">
		<div class="container clearfix">
			<?php
				$menu_class = 'bottom-nav';
				$footerNav = '';

				if ( function_exists( 'wp_nav_menu' ) ) $footerNav = wp_nav_menu( array( 'theme_location' => 'footer-menu', 'container' => '', 'fallback_cb' => '', 'menu_class' => $menu_class, 'echo' => false, 'depth' => '1' ) );
				if ( '' == $footerNav ) show_page_menu( $menu_class );
				else echo( $footerNav );
			?>

			<p id="copyright"><?php printf( __( 'Designed by %1$s | Powered by %2$s', 'Fusion' ), '<a href="http://www.elegantthemes.com" title="Premium WordPress Themes">Elegant Themes</a>', '<a href="http://www.wordpress.org">WordPress</a>' ); ?></p>
		</div> <!-- end .container -->
	</div> <!-- end #footer-bottom -->

	<?php wp_footer(); ?>
</body>
</html>