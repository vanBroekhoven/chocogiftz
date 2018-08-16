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

// Move the search bar inline with the main navigation and cart menu
// add_action( 'init', 'jk_remove_storefront_header_search' );
// function jk_remove_storefront_header_search() {
// remove_action( 'storefront_header', 'storefront_product_search', 40 );
// add_action( 'storefront_header', 'storefront_product_search', 55 );
// }

// add_action('init', 'replace_storefront_primary_navigation' );
// function replace_storefront_primary_navigation(){
//     remove_action( 'storefront_header', 'storefront_primary_navigation', 50 );
//     add_action('storefront_header', 'jk_storefront_header_content', 50);
// }
//
// function jk_storefront_header_content(){
//     // your custom navigation code goes here
//     echo '<span style="display:inline-block; padding:10px; border:solid 1px grey;">My custom mega menu goes Here</span>';
// }

add_action( 'init', 'jk_remove_storefront_header_search' );
function jk_remove_storefront_header_search() {
  remove_action( 'storefront_header', 'storefront_product_search', 	40 );
  add_action( 'storefront_header', 'storefront_product_search', 	55 );
}

/**
 * Change Quick View text in WooCommerce via ..
 *
 */
//  function custom_quick_view()
//  {
//   //$output = 'MyOutput';
//    return __( 'Yes!', 'woo-quick-view');
//  }
// add_filter('woocommerce_loop_quick_view_button','custom_quick_view', 10);
