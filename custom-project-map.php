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

function cptui_register_my_cpts_projekt() {

	/**
	 * Post Type: Projekte.
	 */

	$labels = [
		"name" => __( "Projekte", "twentytwentyone" ),
		"singular_name" => __( "Projekt", "twentytwentyone" ),
	];

	$args = [
		"label" => __( "Projekte", "twentytwentyone" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => false,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"can_export" => false,
		"rewrite" => [ "slug" => "projekt", "with_front" => true ],
		"query_var" => true,
		"menu_icon" => "dashicons-location",
		"supports" => [ "title", "editor", "thumbnail" ],
		"taxonomies" => [ "category" ],
		"show_in_graphql" => false,
	];

	register_post_type( "projekt", $args );
}

add_action( 'init', 'cptui_register_my_cpts_projekt' );

add_action('admin_menu', array( $this, 'addPluginAdminMenu' ), 9);

public function addPluginAdminMenu() {
//add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
add_menu_page(  $this->plugin_name, 'Plugin Name', 'administrator', $this->plugin_name, array( $this, 'displayPluginAdminDashboard' ), 'dashicons-chart-area', 26 );

//add_submenu_page( '$parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
add_submenu_page( $this->plugin_name, 'Plugin Name Settings', 'Settings', 'administrator', $this->plugin_name.'-settings', array( $this, 'displayPluginAdminSettings' ));
}