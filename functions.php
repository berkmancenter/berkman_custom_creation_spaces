<?php 
function add_fullwidth_footer() {
    $args = array(
        'name'          => 'Full Width Footer',
        'id'            => 'fullwidth-footer',
        'description'   => 'The footer sidebar that takes up the full width of the page.');
    register_sidebar($args);
}
function guest_author_name($name) {
    global $post;
    $author = get_post_meta($post->ID, 'author', true);
    if ( $author )
        $name = $author;
    return $name;
}
function cyberlaw_header_image_height() {
    return 66;
}
function read_more_link( $more_link, $more_link_text ) {
	return str_replace('Continue reading', '', $more_link );
}
wp_enqueue_script('jquerycycle', get_bloginfo('stylesheet_directory') . '/jquery.cycle.all.min.js', array('jquery'));
wp_enqueue_script('cyberlaw.js', get_bloginfo('stylesheet_directory') . '/cyberlaw.js', array('jquery'));
wp_enqueue_script('jquery-ui', get_bloginfo('stylesheet_directory') . '/jquery-ui-1.8.16.custom.min.js', array('jquery'));
wp_enqueue_script('hoverintent', get_bloginfo('stylesheet_directory') . '/jquery.hoverIntent.minified.js', array('jquery'));
wp_enqueue_script('bjqs', get_bloginfo('stylesheet_directory') . '/bjqs-1.3.min.js', array('jquery'));
add_action('init', 'add_fullwidth_footer');
add_filter( 'the_author', 'guest_author_name' );
add_filter( 'get_the_author_display_name', 'guest_author_name' );
add_filter('twentyten_header_image_height', 'cyberlaw_header_image_height');
add_filter( 'the_content_more_link', 'read_more_link', 10, 2 );
?>
