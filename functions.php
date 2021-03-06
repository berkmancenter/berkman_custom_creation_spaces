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
function cmsish_add_custom_post_types() {

    register_post_type('publication', array(
        'label' => 'Publications',
        'labels' => array(
            'name' => 'Publications',
            'singular_name' => 'Publication',
            'add_new_item' => 'Add New Publication',
            'edit_item' => 'Edit Publication',
            'new_item' => 'New Publication',
            'view_item' => 'View Publication',
            'search_items' => 'Search Publications'
        ),
        'public' => true,
        'supports' => array('title', 'editor', 'page-attributes', 'custom-fields')
    ));

    register_post_type('case_study', array(
        'label' => 'Case Studies',
        'labels' => array(
            'name' => 'Case Studies',
            'singular_name' => 'Case Study',
            'add_new_item' => 'Add New Case Study',
            'edit_item' => 'Edit Case Study',
            'new_item' => 'New Case Study',
            'view_item' => 'View Case Study',
            'search_items' => 'Search Case Studies'
        ),
        'public' => true,
        'supports' => array('title', 'editor', 'page-attributes', 'custom-fields')
    ));

    register_post_type('event', array(
        'label' => 'Events',
        'labels' => array(
            'name' => 'Events',
            'singular_name' => 'Event',
            'add_new_item' => 'Add New Event',
            'edit_item' => 'Edit Event',
            'new_item' => 'New Event',
            'view_item' => 'View Event',
            'search_items' => 'Search Events'
        ),
        'public' => true,
        'supports' => array('title', 'editor', 'page-attributes', 'custom-fields')
    ));

    register_post_type('bio', array(
        'label' => 'Bio',
        'labels' => array(
            'name' => 'Bios',
            'singular_name' => 'Bio',
            'add_new_item' => 'Add New Bio',
            'edit_item' => 'Edit Bio',
            'new_item' => 'New Bio',
            'view_item' => 'View Bio',
            'search_items' => 'Search Bios'
        ),
        'public' => true,
        'supports' => array('title', 'editor', 'page-attributes', 'custom-fields')
    ));
}
function custom_post_shortcode($atts) {
	extract( shortcode_atts( array(
    'type' => null,
    'id' => null,
    'count' => 'all',
    'order' => 'ASC',
    'orderby' => 'date',
    'wrap' => 'true',
    'showtitle' => 'true',
    'hrs' => 'false',
    'aslist' => 'false',
    'meta_key' => null,
    'excerpt' => 'false',
    'title' => null,
	), $atts ) );
    $args = array();
    $event_type = null;

    if (empty($type) && empty($id)) {
        $type = 'any';
    }

    if ($count == 'all') {
        $count = -1;
    }

    if ($type == 'upcoming_event' || $type == 'past_event') {
        $event_type = $type;
        $type = 'event';
    }

    $contents = array();
    if (!empty($id)) {
        $args = array('p' => $id, 'post_type' => 'any');
    }
    else {
        $args = array(
          'post_type' => $type,
          'post_status' => 'publish',
          'posts_per_page' => $count,
          'order' => $order,
          'orderby' => $orderby,
          'meta_key' => $meta_key
        );
    }
    $my_query = new WP_Query($args);
    if ( $my_query->have_posts() ) { 
        while ( $my_query->have_posts() ) { 
            $my_query->the_post();
            $new_content = null;

            // Handle event dates so we don't need both upcoming and past event custom post types
            $event_date = get_post_meta(get_the_ID(), 'event_date', true);
            if (!is_null($event_type) && !empty($event_date)) {
                switch ($event_type) {
                    case 'upcoming_event':
                    if (strtotime($event_date) > $_SERVER['REQUEST_TIME']) {
                        $new_content = get_the_content();
                    }
                    break;
                    case 'past_event':
                    if (strtotime($event_date) < $_SERVER['REQUEST_TIME']) {
                        $new_content = get_the_content();
                    }
                    break;
                }
            } elseif ($excerpt == 'true') {
                global $more; $more=0;
                $new_content = '<p class="custom-entry-excerpt">'.get_the_content('&rarr;').'</p>';
                $more=1;
            } else { 
                $new_content = get_the_content();
            }

            if (!is_null($new_content)) {
                if ($showtitle == 'true') {
                    $new_content = '<h3 class="custom-entry-title"><a href="' . get_permalink(get_the_ID()) . '">' . get_the_title() . '</a></h3>' . $new_content;
                }
                if ($aslist == 'true') {
                    $contents[] = '<li class="custom-entry ' . get_post_type(get_the_ID()) . '">' . $new_content . '</li>';
                }
                elseif ($wrap == 'true') {
                    $contents[] = '<div class="custom-entry ' . get_post_type(get_the_ID()) . '">' . $new_content . '</div>';
                }
                else {
                    $contents[] = $new_content;
                }
            }
        }
    }
    if (isset($title) && !empty($contents)) {
      $html = '<h2 class="entry-title">' . sanitize_text_field($title) . '</h2>';
    } else {
      $html = '';
    }
    if ($hrs == 'true') {
        $html .= implode('<hr />', $contents);
    } elseif ($aslist == 'true') {
        $html .= '<div id="featured-wrap"><ul class="featured bjqs">' . implode('', $contents) . '</ul></div>';
    } else {
        $html .= implode('', $contents);
    }
    wp_reset_postdata();

	return $html;
}
function featured_shortcode($atts) {
    return do_shortcode('[custom-post orderby="rand" meta_key="featured" aslist="true"]');
}
add_action('init', 'cmsish_add_custom_post_types');
add_shortcode( 'custom-post', 'custom_post_shortcode' );
add_shortcode( 'featured', 'featured_shortcode' );
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
