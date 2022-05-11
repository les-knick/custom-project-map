<?php
/*
Plugin Name:  Custom Project Map
Plugin URI:   https://knick.design
Description:  Plugin for Custom Project Maps. Extends the wp backend.
Version:      1.0
Author:       Knick Design - Lea Stocker
Author URI:   https://knick.design
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  custom-project-map
Domain Path:  /languages
*/

set_include_path ( WP_PLUGIN_DIR . "/custom-project-map/library");

//register two cpts
add_action('init', 'cpm_register_my_cpts');

function cpm_register_my_cpts()
{
require_once "init_custom_post_types.php" ;
}


// register two taxonomies to go with the post type
add_action( 'init', 'cpm_create_taxonomies', 0 );

function cpm_create_taxonomies() {
    require_once "register_taxonomy.php" ;
}


// register new column on post list view
add_filter( 'manage_map_posts_columns', 'cpm_map_columns' );

function cpm_map_columns( $columns ) {
    require_once "map_columns.php" ;
}


// show shortcode in new column on post list view
add_action( 'manage_map_posts_custom_column', 'cpm_map_column', 10, 2);

function cpm_map_column( $column, $post_id ) {
    require_once "map_column.php" ;
} 



// BACKEND METABOX (CUSTOM FIELDS)

// Metabox Projects

/**
 * Calls the class on the post edit screen.
 */
function call_metaboxProjects()
{
    require_once "class_metaboxProjects.php";
    new metaboxProjects();
}

if (is_admin()) {
    add_action('load-post.php',     'call_metaboxProjects');
    add_action('load-post-new.php', 'call_metaboxProjects');
}

// Metabox Maps

/**
 * Calls the class on the post edit screen.
 */
function call_metaboxMaps()
{
    require_once "class_metaboxMaps.php";
    new metaboxMaps();
}

if (is_admin()) {
    add_action('load-post.php',     'call_metaboxMaps');
    add_action('load-post-new.php', 'call_metaboxMaps');
}



// SHORTCODES

// function that runs when shortcode is called
function cpm_map_shortcode($attr)
{
    require_once "map_shortcode.php";
}
// register shortcode
add_shortcode('cpm_map', 'cpm_map_shortcode');

// GENERATE GEOJSON
function generatejson()
{
    require_once "generate_json.php" ;
}

// This is the action function that outputs the function above into the theme hook under the logo
add_action('save_post_project', 'generatejson', 999);
?>