/**
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */

( function( $ ) {
	wp.customize( 'et_fusion[highlight_color]', function( value ) {
		value.bind( function( to ) {
			$( '#breadcrumbs, .read-more span, .testimonial span.title, .entry .meta-info, .entry .meta-info a, .subtitle, .comment_date, .comment-reply-link:before, .bottom-nav li.current_page_item a, #content .wp-pagenavi .nextpostslink, #content .wp-pagenavi .previouspostslink' ).css( 'color', to );

			$( '#top-menu a .menu-highlight, #mobile_menu  .menu-highlight' ).css( 'background', to );

			$( '.mobile_nav' ).css( 'border-color', to );
		} );
	} );

	wp.customize( 'et_fusion[link_color]', function( value ) {
		value.bind( function( to ) {
			$( '#main-area a' ).css( 'color', to );
		} );
	} );

	wp.customize( 'et_fusion[font_color]', function( value ) {
		value.bind( function( to ) {
			$( 'body, .footer-widget' ).css( 'color', to );
		} );
	} );

	wp.customize( 'et_fusion[headings_color]', function( value ) {
		value.bind( function( to ) {
			$( 'h1, h2, h3, h4, h5, h6, .testimonial h2, #recent-updates h2, .recent-update h3 a, .footer-widget h4.widgettitle, .widget h4.widgettitle, .entry h2.title a, h1.title, #comments, #reply-title' ).css( 'color', to );
		} );
	} );

	wp.customize( 'et_fusion[color_schemes]', function( value ) {
		value.bind( function( to ) {
			var $body = $( 'body' ),
				body_classes = $body.attr( 'class' ),
				et_customizer_color_scheme_prefix = 'et_color_scheme_',
				body_class;

			body_class = body_classes.replace( /et_color_scheme_[^\s]+/, '' );
			$body.attr( 'class', $.trim( body_class ) );

			if ( 'none' !== to  )
				$body.addClass( et_customizer_color_scheme_prefix + to );
		} );
	} );

} )( jQuery );