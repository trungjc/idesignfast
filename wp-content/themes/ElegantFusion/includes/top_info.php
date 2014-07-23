<?php
	$et_tagline = '';
	if( is_tag() ) {
		$et_page_title = esc_html__('Posts Tagged &quot;','Fusion') . single_tag_title('',false) . '&quot;';
	} elseif (is_day()) {
		$et_page_title = esc_html__('Posts made in','Fusion') . ' ' . get_the_time('F jS, Y');
	} elseif (is_month()) {
		$et_page_title = esc_html__('Posts made in','Fusion') . ' ' . get_the_time('F, Y');
	} elseif (is_year()) {
		$et_page_title = esc_html__('Posts made in','Fusion') . ' ' . get_the_time('Y');
	} elseif (is_search()) {
		$et_page_title = esc_html__('Search results for','Fusion') . ' ' . get_search_query();
	} elseif (is_category()) {
		$et_page_title = single_cat_title('',false);
		$et_tagline = category_description();
	} elseif (is_author()) {
		global $wp_query;
		$curauth = $wp_query->get_queried_object();
		$et_page_title = esc_html__('Posts by ','Fusion') . $curauth->nickname;
	} elseif ( is_page() || is_single() ) {
		$et_page_title = get_the_title();
		if ( is_page() ) $et_tagline = get_post_meta(get_the_ID(),'Description',true) ? get_post_meta(get_the_ID(),'Description',true) : '';
	} elseif ( is_tax() ){
		$et_page_title = single_term_title( '', false );
		$et_tagline = term_description();
	}
?>
<div class="page-title-area">
	<h1><?php echo wp_kses( $et_page_title, array( 'span' => array() ) ); ?></h1>
<?php if ( $et_tagline <> '' ) { ?>
	<p class="subtitle"><?php echo wp_kses( $et_tagline, array( 'span' => array() ) ); ?></p>
<?php } ?>
</div> <!-- .page-title-area -->