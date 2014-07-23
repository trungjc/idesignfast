<?php

if ( ! isset( $content_width ) ) $content_width = 624;

add_action( 'after_setup_theme', 'et_setup_theme' );
if ( ! function_exists( 'et_setup_theme' ) ){
	function et_setup_theme(){
		global $themename, $shortname, $et_store_options_in_one_row, $default_colorscheme;
		$themename = 'Fusion';
		$shortname = 'fusion';
		$et_store_options_in_one_row = true;

		$default_colorscheme = "Default";

		$template_directory = get_template_directory();

		require_once( $template_directory . '/epanel/custom_functions.php' );

		require_once( $template_directory . '/includes/functions/comments.php' );

		require_once( $template_directory . '/includes/functions/sidebars.php' );

		load_theme_textdomain( 'Fusion', $template_directory . '/lang' );

		require_once( $template_directory . '/epanel/core_functions.php' );

		require_once( $template_directory . '/epanel/post_thumbnails_fusion.php' );

		include( $template_directory . '/includes/widgets.php' );

		register_nav_menus( array(
			'primary-menu' 	=> __( 'Primary Menu', 'Fusion' ),
			'footer-menu'	=> __( 'Footer Menu', 'Fusion' )
		) );

		add_action( 'init', 'et_fusion_register_posttype', 0 );

		add_action( 'wp_enqueue_scripts', 'et_fusion_load_scripts_styles' );

		add_action( 'wp_head', 'et_add_viewport_meta' );

		add_action( 'pre_get_posts', 'et_home_posts_query' );

		add_action( 'et_epanel_changing_options', 'et_delete_featured_ids_cache' );
		add_action( 'delete_post', 'et_delete_featured_ids_cache' );
		add_action( 'save_post', 'et_delete_featured_ids_cache' );

		add_filter( 'wp_page_menu_args', 'et_add_home_link' );

		add_filter( 'et_get_additional_color_scheme', 'et_remove_additional_stylesheet' );

		add_action( 'wp_head', 'et_attach_bg_images' );

		add_action( 'et_header_menu', 'et_add_mobile_navigation' );

		add_action( 'wp_enqueue_scripts', 'et_add_responsive_shortcodes_css', 11 );

		// don't display the empty title bar if the widget title is not set
		remove_filter( 'widget_title', 'et_widget_force_title' );

		add_filter( 'body_class', 'et_add_standard_homepage_class' );
	}
}

function et_add_home_link( $args ) {
	// add Home link to the custom menu WP-Admin page
	$args['show_home'] = true;
	return $args;
}

function et_fusion_load_scripts_styles(){
	$template_dir = get_template_directory_uri();

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) wp_enqueue_script( 'comment-reply' );

	if ( 'off' !== _x( 'on', 'Open Sans font: on or off', 'Fusion' ) ) {
		$subsets = 'latin,latin-ext';

		$subset = _x( 'no-subset', 'Open Sans font: add new subset (greek, cyrillic, vietnamese)', 'Fusion' );

		if ( 'cyrillic' == $subset )
			$subsets .= ',cyrillic,cyrillic-ext';
		elseif ( 'greek' == $subset )
			$subsets .= ',greek,greek-ext';
		elseif ( 'vietnamese' == $subset )
			$subsets .= ',vietnamese';

		$protocol = is_ssl() ? 'https' : 'http';
		$query_args = array(
			'family' => 'Open+Sans:300italic,700italic,800italic,400,300,700,800',
			'subset' => $subsets
		);

		wp_enqueue_style( 'fusion-fonts', add_query_arg( $query_args, "$protocol://fonts.googleapis.com/css" ), array(), null );
	}

	wp_enqueue_script( 'superfish', $template_dir . '/js/superfish.js', array( 'jquery' ), '1.0', true );
	wp_enqueue_script( 'custom_script', $template_dir . '/js/custom.js', array( 'jquery' ), '1.0', true );
	wp_localize_script( 'custom_script', 'et_custom', array( 'mobile_nav_text' => esc_html__( 'Navigation Menu', 'Fusion' ) ) );

	$et_gf_enqueue_fonts = array();
	$et_gf_heading_font = sanitize_text_field( et_get_option( 'heading_font', 'none' ) );
	$et_gf_body_font = sanitize_text_field( et_get_option( 'body_font', 'none' ) );

	if ( 'none' != $et_gf_heading_font ) $et_gf_enqueue_fonts[] = $et_gf_heading_font;
	if ( 'none' != $et_gf_body_font ) $et_gf_enqueue_fonts[] = $et_gf_body_font;

	if ( ! empty( $et_gf_enqueue_fonts ) ) et_gf_enqueue_fonts( $et_gf_enqueue_fonts );

	/*
	 * Loads the main stylesheet.
	 */
	wp_enqueue_style( 'fusion-style', get_stylesheet_uri() );
}

/**
 * Filters the main query on homepage
 */
function et_home_posts_query( $query = false ) {
	/* Don't proceed if it's not homepage or the main query */
	if ( ! is_home() || ! is_a( $query, 'WP_Query' ) || ! $query->is_main_query() ) return;

	/* Set the amount of posts per page on homepage */
	$query->set( 'posts_per_page', (int) et_get_option( 'fusion_homepage_posts', '3' ) );

	if ( 'false' == et_get_option( 'fusion_blog_style', 'false' ) )
		$query->set( 'ignore_sticky_posts', 1 );

	/* Exclude categories set in ePanel */
	$exclude_categories = et_get_option( 'fusion_exlcats_recent', false );
	if ( $exclude_categories ) $query->set( 'category__not_in', array_map( 'intval', et_generate_wpml_ids( $exclude_categories, 'category' ) ) );

	/* Exclude slider posts, if the slider is activated, pages are not featured and posts duplication is disabled in ePanel  */
	if ( 'on' == et_get_option( 'fusion_featured', 'on' ) && 'false' == et_get_option( 'fusion_use_pages', 'false' ) && 'false' == et_get_option( 'fusion_duplicate', 'on' ) )
		$query->set( 'post__not_in', et_get_featured_posts_ids() );
}

function et_add_mobile_navigation(){
	echo '<div id="et_mobile_nav_menu">' . '<a href="#" class="mobile_nav closed">' . esc_html__( 'Navigation Menu', 'Fusion' ) . '<span></span></a>' . '</div>';
}

function et_add_viewport_meta(){
	echo '<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />';
}

function et_remove_additional_stylesheet( $stylesheet ){
	global $default_colorscheme;
	return $default_colorscheme;
}

/**
 * Gets featured posts IDs from transient, if the transient doesn't exist - runs the query and stores IDs
 */
function et_get_featured_posts_ids(){
	if ( false === ( $et_featured_post_ids = get_transient( 'et_featured_post_ids' ) ) ) {
		$featured_query = new WP_Query( apply_filters( 'et_featured_post_args', array(
			'posts_per_page'	=> (int) et_get_option( 'fusion_featured_num' ),
			'cat'				=> (int) get_catId( et_get_option( 'fusion_feat_posts_cat' ) )
		) ) );

		if ( $featured_query->have_posts() ) {
			while ( $featured_query->have_posts() ) {
				$featured_query->the_post();

				$et_featured_post_ids[] = get_the_ID();
			}

			set_transient( 'et_featured_post_ids', $et_featured_post_ids );
		}

		wp_reset_postdata();
	}

	return $et_featured_post_ids;
}

/**
 * Deletes featured posts IDs transient, when the user saves, resets ePanel settings, creates or moves posts to trash in WP-Admin
 */
function et_delete_featured_ids_cache(){
	if ( false !== get_transient( 'et_featured_post_ids' ) ) delete_transient( 'et_featured_post_ids' );
}

// flush permalinks on theme activation
add_action( 'after_switch_theme', 'et_rewrite_flush' );
function et_rewrite_flush() {
    flush_rewrite_rules();
}

if ( ! function_exists( 'et_list_pings' ) ){
	function et_list_pings($comment, $args, $depth) {
		$GLOBALS['comment'] = $comment; ?>
		<li id="comment-<?php comment_ID(); ?>"><?php comment_author_link(); ?> - <?php comment_excerpt(); ?>
	<?php }
}

if ( ! function_exists( 'et_get_the_author_posts_link' ) ){
	function et_get_the_author_posts_link(){
		global $authordata, $themename;

		$link = sprintf(
			'<a href="%1$s" title="%2$s" rel="author">%3$s</a>',
			esc_url( get_author_posts_url( $authordata->ID, $authordata->user_nicename ) ),
			esc_attr( sprintf( __( 'Posts by %s', $themename ), get_the_author() ) ),
			get_the_author()
		);
		return apply_filters( 'the_author_posts_link', $link );
	}
}

if ( ! function_exists( 'et_get_comments_popup_link' ) ){
	function et_get_comments_popup_link( $zero = false, $one = false, $more = false ){
		global $themename;

		$id = get_the_ID();
		$number = get_comments_number( $id );

		if ( 0 == $number && !comments_open() && !pings_open() ) return;

		if ( $number > 1 )
			$output = str_replace('%', number_format_i18n($number), ( false === $more ) ? __('% Comments', $themename) : $more);
		elseif ( $number == 0 )
			$output = ( false === $zero ) ? __('No Comments',$themename) : $zero;
		else // must be one
			$output = ( false === $one ) ? __('1 Comment', $themename) : $one;

		return '<span class="comments-number">' . '<a href="' . esc_url( get_permalink() . '#respond' ) . '">' . apply_filters('comments_number', $output, $number) . '</a>' . '</span>';
	}
}

if ( ! function_exists( 'et_postinfo_meta' ) ){
	function et_postinfo_meta( $postinfo, $date_format, $comment_zero, $comment_one, $comment_more ){
		global $themename;

		$postinfo_meta = '';

		if ( in_array( 'author', $postinfo ) ){
			$postinfo_meta .= ' ' . esc_html__('by',$themename) . ' ' . et_get_the_author_posts_link();
		}

		if ( in_array( 'date', $postinfo ) )
			$postinfo_meta .= ' ' . esc_html__('on',$themename) . ' ' . get_the_time( $date_format );

		if ( in_array( 'categories', $postinfo ) && 'post' === get_post_type() )
			$postinfo_meta .= ' ' . esc_html__('in',$themename) . ' ' . get_the_category_list(', ');

		if ( in_array( 'comments', $postinfo ) )
			$postinfo_meta .= ' | ' . et_get_comments_popup_link( $comment_zero, $comment_one, $comment_more );

		if ( '' != $postinfo_meta ) $postinfo_meta = __('Posted',$themename) . ' ' . $postinfo_meta;

		echo $postinfo_meta;
	}
}

function et_fusion_register_posttype() {
	$labels = array(
		'name' 					=> _x( 'Testimonials', 'post type general name', 'Fusion' ),
		'singular_name' 		=> _x( 'Testimonial', 'post type singular name', 'Fusion' ),
		'add_new' 				=> _x( 'Add New', 'testimonial item', 'Fusion' ),
		'add_new_item'			=> __( 'Add New Testimonial', 'Fusion' ),
		'edit_item' 			=> __( 'Edit Testimonial', 'Fusion' ),
		'new_item' 				=> __( 'New Testimonial', 'Fusion' ),
		'all_items' 			=> __( 'All Testimonials', 'Fusion' ),
		'view_item' 			=> __( 'View Testimonial', 'Fusion' ),
		'search_items' 			=> __( 'Search Testimonials', 'Fusion' ),
		'not_found' 			=> __( 'Nothing found', 'Fusion' ),
		'not_found_in_trash' 	=> __( 'Nothing found in Trash', 'Fusion' ),
		'parent_item_colon' 	=> ''
	);

	$args = array(
		'labels' 				=> $labels,
		'public' 				=> true,
		'publicly_queryable' 	=> true,
		'show_ui' 				=> true,
		'can_export'			=> true,
		'show_in_nav_menus'		=> true,
		'query_var' 			=> true,
		'has_archive' 			=> true,
		'rewrite' 				=> apply_filters( 'et_testimonial_posttype_rewrite_args', array( 'slug' => 'testimonial', 'with_front' => false ) ),
		'capability_type' 		=> 'post',
		'hierarchical' 			=> false,
		'menu_position' 		=> null,
		'supports' 				=> array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments', 'revisions', 'custom-fields' )
	);

	register_post_type( 'testimonial' , apply_filters( 'et_testimonial_posttype_args', $args ) );
}

//add filter to ensure the text Testimonial, or testimonial, is displayed when user updates a testimonial
add_filter( 'post_updated_messages', 'et_custom_post_type_updated_message' );
function et_custom_post_type_updated_message( $messages ) {
	global $post, $post_id;

	$messages['testimonial'] = array(
		0 => '', // Unused. Messages start at index 1.
		1 => sprintf( __( 'Testimonial updated. <a href="%s">View testimonial</a>', 'Fusion' ), esc_url( get_permalink( $post_id ) ) ),
		2 => __( 'Custom field updated.', 'Fusion' ),
		3 => __( 'Custom field deleted.', 'Fusion' ),
		4 => __( 'Testimonial updated.', 'Fusion' ),
		/* translators: %s: date and time of the revision */
		5 => isset( $_GET['revision'] ) ? sprintf( __( 'Testimonial restored to revision from %s', 'Fusion' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6 => sprintf( __( 'Testimonial published. <a href="%s">View testimonial</a>', 'Fusion' ), esc_url( get_permalink( $post_id ) ) ),
		7 => __( 'Testimonial saved.', 'Fusion' ),
		8 => sprintf( __( 'Testimonial submitted. <a target="_blank" href="%s">Preview testimonial</a>', 'Fusion' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_id ) ) ) ),
		9 => sprintf( __( 'Testimonial scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview testimonial</a>', 'Fusion' ),
		  // translators: Publish box date format, see http://php.net/date
		  date_i18n( __( 'M j, Y @ G:i', 'Fusion' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_id ) ) ),
		10 => sprintf( __( 'Testimonial draft updated. <a target="_blank" href="%s">Preview testimonial</a>', 'Fusion' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_id ) ) ) )
	);

	return $messages;
}

add_action( 'add_meta_boxes', 'et_event_posttype_meta_box' );
function et_event_posttype_meta_box() {
	add_meta_box( 'et_settings_meta_box', __( 'ET Testimonial Settings', 'Fusion' ), 'et_testimonial_settings_meta_box', 'testimonial', 'normal', 'high' );
	add_meta_box( 'et_settings_meta_box', __( 'ET Settings', 'Fusion' ), 'et_post_settings_meta_box', 'post', 'normal', 'high' );
	add_meta_box( 'et_settings_meta_box', __( 'ET Settings', 'Fusion' ), 'et_post_settings_meta_box', 'page', 'normal', 'high' );
}

function et_post_settings_meta_box() {
	$post_id = get_the_ID();
	wp_nonce_field( basename( __FILE__ ), 'et_settings_nonce' );
?>
	<div id="et_featured_entry_settings">
		<p>
			<label for="et_slide_bg"><?php esc_html_e( 'Slide Background Image', 'Fusion' ); ?>: </label>
			<input type="text" name="et_slide_bg" id="et_slide_bg" size="90" value="<?php echo esc_attr( get_post_meta( $post_id, '_et_slide_bg', true ) ); ?>" />
			<input class="upload_image_button" type="button" value="<?php esc_html_e( 'Upload Image', 'Fusion' ); ?>" /><br/>
			<small><?php esc_html_e( 'enter URL or upload an image for the 1st Product Image', 'Fusion' ); ?></small>
		</p>

		<p>
			<label for="et_slide_subtitle"><?php esc_html_e( 'Slide Subtitle', 'Fusion' ); ?>: </label>
			<input type="text" name="et_slide_subtitle" id="et_slide_subtitle" class="regular-text" value="<?php echo esc_attr( get_post_meta( $post_id, '_et_slide_subtitle', true ) ); ?>" />
		</p>

		<p>
			<label for="et_slide_more_text"><?php esc_html_e( 'Read More Button Text', 'Fusion' ); ?>: </label>
			<input type="text" name="et_slide_more_text" id="et_slide_more_text" class="regular-text" value="<?php echo esc_attr( get_post_meta( $post_id, '_et_slide_more_text', true ) ); ?>" />
		</p>

		<p>
			<label for="et_slide_more_link"><?php esc_html_e( 'Read More Custom Link', 'Fusion' ); ?>: </label>
			<input type="text" name="et_slide_more_link" id="et_slide_more_link" class="regular-text" value="<?php echo esc_attr( get_post_meta( $post_id, '_et_slide_more_link', true ) ); ?>" />

			<br/>
			<small><?php esc_html_e( 'here you can provide a custom url, that will be used for the slide', 'Fusion' ); ?></small>
		</p>
	</div>
<?php
}

function et_testimonial_settings_meta_box() {
	$post_id = get_the_ID();
	wp_nonce_field( basename( __FILE__ ), 'et_settings_nonce' );
?>
	<p>
		<label for="et_testimonial_company"><?php esc_html_e( 'Company Name', 'Fusion' ); ?>: </label>
		<input type="text" name="et_testimonial_company" id="et_testimonial_company" class="regular-text" value="<?php echo esc_attr( get_post_meta( $post_id, '_et_testimonial_company', true ) ); ?>" />
	</p>
<?php
}

add_action( 'save_post', 'et_metabox_settings_save_details', 10, 2 );
function et_metabox_settings_save_details( $post_id, $post ){
	global $pagenow;

	if ( 'post.php' != $pagenow ) return $post_id;

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return $post_id;

	$post_type = get_post_type_object( $post->post_type );
	if ( ! current_user_can( $post_type->cap->edit_post, $post_id ) )
		return $post_id;

	if ( !isset( $_POST['et_settings_nonce'] ) || ! wp_verify_nonce( $_POST['et_settings_nonce'], basename( __FILE__ ) ) )
        return $post_id;

	if ( in_array( $_POST['post_type'], array( 'post', 'page' ) ) ) {
		if ( isset( $_POST['et_slide_bg'] ) )
			update_post_meta( $post_id, '_et_slide_bg', esc_url_raw( $_POST['et_slide_bg'] ) );
		else
			delete_post_meta( $post_id, '_et_slide_bg' );

		if ( isset( $_POST['et_slide_subtitle'] ) )
			update_post_meta( $post_id, '_et_slide_subtitle', sanitize_text_field( $_POST['et_slide_subtitle'] ) );
		else
			delete_post_meta( $post_id, '_et_slide_subtitle' );

		if ( isset( $_POST['et_slide_more_text'] ) )
			update_post_meta( $post_id, '_et_slide_more_text', sanitize_text_field( $_POST['et_slide_more_text'] ) );
		else
			delete_post_meta( $post_id, '_et_slide_more_text' );

		if ( isset( $_POST['et_slide_more_link'] ) )
			update_post_meta( $post_id, '_et_slide_more_link', esc_url_raw( $_POST['et_slide_more_link'] ) );
		else
			delete_post_meta( $post_id, '_et_slide_more_link' );
	} else if ( 'testimonial' == $_POST['post_type'] ) {
		if ( isset( $_POST['et_testimonial_company'] ) )
			update_post_meta( $post_id, '_et_testimonial_company', sanitize_text_field( $_POST['et_testimonial_company'] ) );
		else
			delete_post_meta( $post_id, '_et_testimonial_company' );
	}
}

add_action( 'admin_enqueue_scripts', 'et_admin_scripts_styles', 10, 1 );
function et_admin_scripts_styles( $hook ) {
	global $typenow;

	$template_dir = get_template_directory_uri();

	if ( in_array( $typenow, array( 'post', 'page' ) ) ) {
		wp_enqueue_script( 'et_image_upload_custom', $template_dir . '/js/admin_custom_uploader.js', array( 'jquery' ) );
	}
}

function et_attach_bg_images() {
	$bg = et_get_option( 'fusion_bg_image' );
	if ( '' == $bg ) $bg = get_template_directory_uri() . '/images/bg_fusion.jpg';
?>
	<style>
		#top-area, #footer-bottom { background-image: url(<?php echo esc_url( $bg ); ?>); }
	</style>
<?php
}

if ( function_exists( 'get_custom_header' ) ) {
	// compatibility with versions of WordPress prior to 3.4

	add_action( 'customize_register', 'et_fusion_customize_register' );
	function et_fusion_customize_register( $wp_customize ) {
		$google_fonts = et_get_google_fonts();

		$font_choices = array();
		$font_choices['none'] = 'Default Theme Font';
		foreach ( $google_fonts as $google_font_name => $google_font_properties ) {
			$font_choices[ $google_font_name ] = $google_font_name;
		}

		$wp_customize->remove_section( 'title_tagline' );

		$wp_customize->add_section( 'et_google_fonts' , array(
			'title'		=> __( 'Fonts', 'Fusion' ),
			'priority'	=> 50,
		) );

		$wp_customize->add_section( 'et_color_schemes' , array(
			'title'       => __( 'Schemes', 'Fusion' ),
			'priority'    => 60,
			'description' => __( 'Note: Color settings set above should be applied to the Default color scheme.', 'Fusion' ),
		) );

		$wp_customize->add_setting( 'et_fusion[highlight_color]', array(
			'default'		=> '#c3e54b',
			'type'			=> 'option',
			'capability'	=> 'edit_theme_options',
			'transport'		=> 'postMessage'
		) );

		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'et_fusion[highlight_color]', array(
			'label'		=> __( 'Highlight Color', 'Fusion' ),
			'section'	=> 'colors',
			'settings'	=> 'et_fusion[highlight_color]',
		) ) );

		$wp_customize->add_setting( 'et_fusion[link_color]', array(
			'default'		=> '#FFA300',
			'type'			=> 'option',
			'capability'	=> 'edit_theme_options',
			'transport'		=> 'postMessage'
		) );

		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'et_fusion[link_color]', array(
			'label'		=> __( 'Link Color', 'Fusion' ),
			'section'	=> 'colors',
			'settings'	=> 'et_fusion[link_color]',
		) ) );

		$wp_customize->add_setting( 'et_fusion[font_color]', array(
			'default'		=> '#454545',
			'type'			=> 'option',
			'capability'	=> 'edit_theme_options',
			'transport'		=> 'postMessage'
		) );

		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'et_fusion[font_color]', array(
			'label'		=> __( 'Main Font Color', 'Fusion' ),
			'section'	=> 'colors',
			'settings'	=> 'et_fusion[font_color]',
		) ) );

		$wp_customize->add_setting( 'et_fusion[headings_color]', array(
			'default'		=> '#454545',
			'type'			=> 'option',
			'capability'	=> 'edit_theme_options',
			'transport'		=> 'postMessage'
		) );

		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'et_fusion[headings_color]', array(
			'label'		=> __( 'Headings Color', 'Fusion' ),
			'section'	=> 'colors',
			'settings'	=> 'et_fusion[headings_color]',
		) ) );

		$wp_customize->add_setting( 'et_fusion[heading_font]', array(
			'default'		=> 'none',
			'type'			=> 'option',
			'capability'	=> 'edit_theme_options'
		) );

		$wp_customize->add_control( 'et_fusion[heading_font]', array(
			'label'		=> __( 'Header Font', 'Fusion' ),
			'section'	=> 'et_google_fonts',
			'settings'	=> 'et_fusion[heading_font]',
			'type'		=> 'select',
			'choices'	=> $font_choices
		) );

		$wp_customize->add_setting( 'et_fusion[body_font]', array(
			'default'		=> 'none',
			'type'			=> 'option',
			'capability'	=> 'edit_theme_options'
		) );

		$wp_customize->add_control( 'et_fusion[body_font]', array(
			'label'		=> __( 'Body Font', 'Fusion' ),
			'section'	=> 'et_google_fonts',
			'settings'	=> 'et_fusion[body_font]',
			'type'		=> 'select',
			'choices'	=> $font_choices
		) );

		$wp_customize->add_setting( 'et_fusion[color_schemes]', array(
			'default'		=> 'none',
			'type'			=> 'option',
			'capability'	=> 'edit_theme_options',
			'transport'		=> 'postMessage'
		) );

		$wp_customize->add_control( 'et_fusion[color_schemes]', array(
			'label'		=> __( 'Color Schemes', 'Fusion' ),
			'section'	=> 'et_color_schemes',
			'settings'	=> 'et_fusion[color_schemes]',
			'type'		=> 'select',
			'choices'	=> array(
				'none'   => __( 'Default', 'Fusion' ),
				'blue'   => __( 'Blue', 'Fusion' ),
				'green'  => __( 'Green', 'Fusion' ),
				'purple' => __( 'Purple', 'Fusion' ),
				'red'    => __( 'Red', 'Fusion' ),
			),
		) );
	}

	add_action( 'customize_preview_init', 'et_fusion_customize_preview_js' );
	function et_fusion_customize_preview_js() {
		wp_enqueue_script( 'fusion-customizer', get_template_directory_uri() . '/js/theme-customizer.js', array( 'customize-preview' ), false, true );
	}

	add_action( 'wp_head', 'et_fusion_add_customizer_css' );
	add_action( 'customize_controls_print_styles', 'et_fusion_add_customizer_css' );
	function et_fusion_add_customizer_css(){ ?>
		<style>
			#breadcrumbs, .read-more span, .testimonial span.title, .entry .meta-info, .entry .meta-info a, .entry .meta-info a:hover, .subtitle, .comment_date, .comment-reply-link:before, .bottom-nav li.current_page_item a, #content .wp-pagenavi .nextpostslink, #content .wp-pagenavi .previouspostslink { color: <?php echo esc_html( et_get_option( 'highlight_color', '#c3e54b' ) ); ?>; }
			.mobile_nav { border-color: <?php echo esc_html( et_get_option( 'highlight_color', '#c3e54b' ) ); ?>; }
			#top-menu a .menu-highlight, #mobile_menu  .menu-highlight { background-color: <?php echo esc_html( et_get_option( 'highlight_color', '#c3e54b' ) ); ?>; }
			a { color: <?php echo esc_html( et_get_option( 'link_color', '#FFA300' ) ); ?>; }
			body, .footer-widget { color: <?php echo esc_html( et_get_option( 'font_color', '#454545' ) ); ?>; }
			h1, h2, h3, h4, h5, h6, .testimonial h2, #recent-updates h2, .recent-update h3 a, .footer-widget h4.widgettitle, .widget h4.widgettitle, .entry h2.title a, h1.title, #comments, #reply-title { color: <?php echo esc_html( et_get_option( 'headings_color', '#454545' ) ); ?>; }

		<?php
			$et_gf_heading_font = sanitize_text_field( et_get_option( 'heading_font', 'none' ) );
			$et_gf_body_font = sanitize_text_field( et_get_option( 'body_font', 'none' ) );

			if ( 'none' != $et_gf_heading_font || 'none' != $et_gf_body_font ) :

				if ( 'none' != $et_gf_heading_font )
					et_gf_attach_font( $et_gf_heading_font, 'h1, h2, h3, h4, h5, h6, .read-more, .testimonial .title, .entry .meta-info, .subtitle, .wp-pagenavi, .comment_postinfo, .comment-reply-link, .form-submit #submit' );

				if ( 'none' != $et_gf_body_font )
					et_gf_attach_font( $et_gf_body_font, 'body' );

			endif;
		?>
		</style>
	<?php }

	/*
	 * Adds color scheme class to the body tag
	 */
	add_filter( 'body_class', 'et_customizer_color_scheme_class' );
	function et_customizer_color_scheme_class( $body_class ) {
		$color_scheme        = et_get_option( 'color_schemes', 'none' );
		$color_scheme_prefix = 'et_color_scheme_';

		if ( 'none' !== $color_scheme ) $body_class[] = $color_scheme_prefix . $color_scheme;

		return $body_class;
	}

	add_action( 'customize_controls_print_footer_scripts', 'et_load_google_fonts_scripts' );
	function et_load_google_fonts_scripts() {
		wp_enqueue_script( 'et_google_fonts', get_template_directory_uri() . '/epanel/google-fonts/et_google_fonts.js', array( 'jquery' ), '1.0', true );
	}

	add_action( 'customize_controls_print_styles', 'et_load_google_fonts_styles' );
	function et_load_google_fonts_styles() {
		wp_enqueue_style( 'et_google_fonts_style', get_template_directory_uri() . '/epanel/google-fonts/et_google_fonts.css', array(), null );
	}
}

function et_add_standard_homepage_class( $classes ) {
	if ( is_home() && ! is_front_page() ) $classes[] = 'et_default_homepage';

	return $classes;
}