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
