<?php
/**
* Plugin Name: Rentall
* Plugin URI: http://mypluginuri.com/
* Description: A plugin which rents stuff.
* Version: 1.0 or whatever version of the plugin (pretty self explanatory)
* Author: Mountainhouse
* Author URI: Author's website
* License: A "Slug" license name e.g. GPL12
*/

function register_cpt_products() {
 
    $labels = array(
        'name' => _x( 'Rental products', 'products' ),
        'singular_name' => _x( 'Product', 'products' ),
        'add_new' => _x( 'Add New', 'products' ),
        'add_new_item' => _x( 'Add New Product', 'products' ),
        'edit_item' => _x( 'Edit Product', 'products' ),
        'new_item' => _x( 'New Product', 'products' ),
        'view_item' => _x( 'View Product', 'products' ),
        'search_items' => _x( 'Search Products', 'products' ),
        'not_found' => _x( 'No products found', 'products' ),
        'not_found_in_trash' => _x( 'No products found in Trash', 'products' ),
        'parent_item_colon' => _x( 'Parent Product:', 'products' ),
        'menu_name' => _x( 'Rental 	', 'products' ),
    );
 
    $args = array(
        'labels' => $labels,
        'hierarchical' => true,
        'description' => 'Products filterable by products',
        'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'trackbacks', 'custom-fields', 'comments', 'revisions', 'page-attributes' ),
        'taxonomies' => array( 'type' ),
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 5,
        'menu_icon' => 'dashicons-screenoptions',
        'show_in_nav_menus' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'has_archive' => true,
        'query_var' => true,
        'can_export' => true,
        'rewrite' => true,
        'capability_type' => 'post'
    );
 
    register_post_type( 'products', $args );
}
 
add_action( 'init', 'register_cpt_products' );

function products_taxonomy() {
    register_taxonomy(
        'types',
        'products',
        array(
            'hierarchical' => true,
            'label' => 'Types',
            'query_var' => true,
            'rewrite' => array(
                'slug' => 'type',
                'with_front' => false
            )
        )
    );
}
add_action( 'init', 'products_taxonomy');
