<?php

/*
@package storefront-child
*/

/*** FUNCTIONS ***/

// Enqueue child-theme javascript
function storefront_child_scripts() {
  wp_enqueue_script( 'extra js', get_template_directory_uri() . '/../src/js/extra.js');
  wp_enqueue_script( 'bootstrap', get_template_directory_uri() . '/../src/js/bootstrap.min.js', array('jquery'), '4.1.3', true);
}

// Enqueue child-theme stylesheet and Bootstrap 4
function my_theme_enqueue_styles() {
    $parent_style = 'parent-style';
    //wp_enqueue_style( 'bootstrap_css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css' );
    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-sass', get_stylesheet_directory_uri() . '/build/css/child-styles.css', array(), '1.0', 'all' );
}

// add_action( 'wp_enqueue_scripts', 'custom_load_font_awesome' );
// /**
//  * Enqueue Font Awesome.
//  */
// function custom_load_font_awesome() {
//
//     wp_enqueue_style( 'font-awesome-free', '//use.fontawesome.com/releases/v5.2.0/css/all.css' );
//
// }

function custom_storefront_credit() {
  ?>
  <div class="site-info">
    <?php echo esc_html( apply_filters( 'storefront_copyright_text', $content = '&copy; ' . get_bloginfo( 'name' ) . ' ' . date( 'Y' ) ) ); ?>
    <?php if ( apply_filters( 'storefront_credit_link', true ) ) { ?>
    <br /> <br> Ontwikkeld door <a href="https://epicwebapps.com" title="epicwebapps.com">epicwebapps.com</a>.
    <?php } ?>
  </div><!-- .site-info -->
  <?php
}

// Remove credit in footer
function custom_remove_footer_credit () {
    remove_action( 'storefront_footer', 'storefront_credit', 20 );
    add_action( 'storefront_footer', 'custom_storefront_credit', 80 );
}



// Remove handheld footer bar
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
  remove_action( 'storefront_header', 'storefront_header_cart', 60 );

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

/*
  Woocommerce pages full-width.
  ----------------------------------------------------------------
*/
add_action( 'get_header', 'remove_storefront_sidebar' );
function remove_storefront_sidebar() {
	if ( is_woocommerce() ) {
		remove_action( 'storefront_sidebar', 'storefront_get_sidebar', 10 );
	}
}

add_action( 'wp', 'woa_remove_sidebar_shop_page' );
function woa_remove_sidebar_shop_page() {

if ( is_shop() || is_tax( 'product_cat' ) || get_post_type() == 'product' ) {

remove_action( 'storefront_sidebar', 'storefront_get_sidebar', 10 );
add_filter( 'body_class', 'woa_remove_sidebar_class_body', 10 );
}
}

function woa_remove_sidebar_class_body( $wp_classes ) {

$wp_classes[] = 'page-template-template-fullwidth-php';
return $wp_classes;
}

/**
 * Adds a top bar to Storefront, before the header.
 */
function storefront_add_topbar() {
    ?>
    <div id="topbar">
        <div class="col-full">
            <p>Your text here</p>
        </div>
    </div>
    <?php
}
add_action( 'storefront_before_header', 'storefront_add_topbar' );

