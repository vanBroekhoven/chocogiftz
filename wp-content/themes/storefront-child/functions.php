<?php

// FUNCTIONS:
function storefront_child_scripts() {
  wp_enqueue_script( 'extra js', get_stylesheet_directory_uri() . '/js/extra.js');
}

function my_theme_enqueue_styles() {
    $parent_style = 'parent-style';
    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-sass', get_stylesheet_directory_uri() . '/build/css/child-styles.css', array(), '1.0', 'all' );
}

// HOOKS:
add_action( 'wp_enqueue_scripts', 'storefront_child_scripts' );

add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles', PHP_INT_MAX);

// Random Shit voor GIT
