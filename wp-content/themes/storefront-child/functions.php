<?php

// FUNCTIONS:////

// Enqueue child-theme javascript
function storefront_child_scripts() {
  wp_enqueue_script( 'extra js', get_stylesheet_directory_uri() . '/../src/js/extra.js');
}

// Enqueue child-theme stylesheet
function my_theme_enqueue_styles() {
    $parent_style = 'parent-style';
    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-sass', get_stylesheet_directory_uri() . '/build/css/child-styles.css', array(), '1.0', 'all' );
}

// Remove credit in footer
function custom_remove_footer_credit () {
    remove_action( 'storefront_footer', 'storefront_credit', 20 );
    //add_action( 'storefront_footer', 'custom_storefront_credit', 20 );
}

// Put Copyright 'current-year' in footer
// function custom_storefront_credit() --> Replaced by javascript

// Remove the link "my-account" in handheld device 'Big Icon Menu'
// function jk_remove_handheld_footer_links( $links ) {
// 	unset( $links['my-account'] );
// 	return $links;
// }
//
// // Function to link home
// function jk_home_link() {
// 	echo '<a href="' . esc_url( home_url( '/' ) ) . '">' . __( 'Home' ) . '</a>';
// }

add_action( 'init', 'jk_remove_storefront_handheld_footer_bar' );

function jk_remove_storefront_handheld_footer_bar() {
  remove_action( 'storefront_footer', 'storefront_handheld_footer_bar', 999 );
}

// HOOKS:
add_action( 'wp_enqueue_scripts', 'storefront_child_scripts' );

add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles', PHP_INT_MAX);

add_action( 'init', 'custom_remove_footer_credit', 10 );

add_filter( 'storefront_handheld_footer_bar_links', 'jk_remove_handheld_footer_links' );

add_filter( 'storefront_handheld_footer_bar_links', 'jk_add_home_link' );
function jk_add_home_link( $links ) {
	$new_links = array(
		'home' => array(
			'priority' => 10,
			'callback' => 'jk_home_link',
		),
	);

	$links = array_merge( $new_links, $links );

	return $links;
}

/* Reposition the search bar */
add_action( 'init', 'jk_remove_storefront_header_search' );
function jk_remove_storefront_header_search() {
  remove_action( 'storefront_header', 'storefront_product_search', 	40 );
  add_action( 'storefront_header', 'storefront_product_search', 	55 );
}

/* Custom shortcode for Lorem Ipsum */

function lorem_func($attr) {

  $txt = "<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do
  eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim
  veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
  consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum
  dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident,
  sunt in culpa qui officia deserunt mollit anim id est laborum.Lorem ipsum dolor
  sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut
  labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud
  exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute
  irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat
  nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa
  qui officia deserunt mollit anim id est laborum.</p>";

  shortcode_atts(
    array(
      'repeat' => 1
    ), $attr
  );

  return str_repeat($txt, $attr['repeat']);

}

add_shortcode('lorem','lorem_func');

/* Functions for slider */
add_action('init', 'register_my_scripts');

// Registers JS file
function register_my_scripts() {
	wp_register_script( 'flexslider', get_stylesheet_directory_uri() . '/flexslider/jquery.flexslider-min.js', array('jquery'), '1.0.0', true );
}

add_action('wp_footer', 'print_my_script', 99);

// Output (print) all the other code (incl. the code for Flexslider) into the footer of your theme
function print_my_script() {
	global $add_my_script, $ss_atts;
	if ( $add_my_script ) {
		$speed = $ss_atts['slideshowspeed']*1000;
		echo "<script type=\"text/javascript\">
jQuery(document).ready(function($) {
	$('head').prepend($('<link>').attr({
		rel: 'stylesheet',
		type: 'text/css',
		media: 'screen',
		href: '" . get_stylesheet_directory_uri() . "/flexslider/flexslider.css'
	}));
	$('.flexslider').flexslider({
		animation: '".$ss_atts['animation']."',
		slideshowSpeed: ".$speed.",
		controlNav: false
	});
});
</script>";
		wp_print_scripts('flexslider');
	} else {
		return;
	}
}

// Create custom post type for all the different sliders I will later create for my website.
add_action( 'init', 'create_slider_posttype' );
function create_slider_posttype() {
    $args = array(
      'public' => false,
      'show_ui' => true,
      'menu_icon' => 'dashicons-images-alt',
      'capability_type' => 'page',
      'rewrite' => array( 'slider-loc', 'post_tag' ),
      'label'  => 'Simple slides',
      'supports' => array( 'title', 'editor', 'custom-fields', 'thumbnail', 'page-attributes')
    );
    register_post_type( 'slider', $args );
}

// Create custom taxonomy to bundle these slides (posts) into slideshow.
add_action( 'init', 'create_slider_location_tax' );
function create_slider_location_tax() {
	register_taxonomy(
		'slider-loc',
		'slider',
		array(
			'label' => 'Slider location',
			'public' => false,
			'show_ui' => true,
			'show_admin_column' => true,
			'rewrite' => false
		)
	);
}

 // Create a post meta value for each slide.
 // This value is used as a default URL value for the custom field.
add_action('wp_insert_post', 'set_default_slidermeta');

function set_default_slidermeta($post_ID){
    add_post_meta($post_ID, 'slider-url', 'http://', true);
    return $post_ID;
}

/* WordPress image slider shortcode */
add_shortcode( 'simpleslider', 'simple_slider_shortcode' );

function simple_slider_shortcode($atts = null) {
	global $add_my_script, $ss_atts;
	$add_my_script = true;
	$ss_atts = shortcode_atts(
		array(
			'location' => '',
			'limit' => -1,
			'ulid' => 'flexid',
			'animation' => 'slide',
			'slideshowspeed' => 5
		), $atts, 'simpleslider'
	);
	$args = array(
		'post_type' => 'slider',
		'posts_per_page' => $ss_atts['limit'],
		'orderby' => 'menu_order',
		'order' => 'ASC'
	);
	if ($ss_atts['location'] != '') {
		$args['tax_query'] = array(
			array( 'taxonomy' => 'slider-loc', 'field' => 'slug', 'terms' => $ss_atts['location'] )
		);
	}
	$the_query = new WP_Query( $args );
	$slides = array();
	if ( $the_query->have_posts() ) {
		while ( $the_query->have_posts() ) {
			$the_query->the_post();
			$imghtml = get_the_post_thumbnail(get_the_ID(), 'full');
			$url = get_post_meta(get_the_ID(), 'slider-url', true);
			if ($url != '' && $url != 'http://') {
				$imghtml = '<a href="'.$url.'">'.$imghtml.'</a>';
			}
			$slides[] = '
				<li>
					<div class="slide-media">'.$imghtml.'</div>
					<div class="slide-content">
						<h3 class="slide-title">'.get_the_title().'</h3>
						<div class="slide-text">'.get_the_content().'</div>
					</div>
				</li>';
		}
	}
	wp_reset_query();
	return '
	<div class="flexslider" id="'.$ss_atts['ulid'].'">
		<ul class="slides">
			'.implode('', $slides).'
		</ul>
	</div>';
}
