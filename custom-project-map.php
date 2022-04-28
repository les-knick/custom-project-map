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

function dbi_add_settings_page() {
    add_options_page( 'Custom Project Map Settings', 'Custom Project Map', 'manage_options', 'dbi-example-plugin', 'dbi_render_plugin_settings_page' );
}

function dbi_render_plugin_settings_page() {
    ?>
    <h2>Custom Project Map Settings</h2>
    <form action="options.php" method="post">
        <?php 
        settings_fields( 'dbi_example_plugin_options' );
        do_settings_sections( 'dbi_example_plugin' ); ?>
        <input name="submit" class="button button-primary" type="submit" value="<?php esc_attr_e( 'Save' ); ?>" />
    </form>
    <?php
}

function dbi_register_settings() {
    register_setting( 'dbi_example_plugin_options', 'dbi_example_plugin_options', 'dbi_example_plugin_options_validate' );
    add_settings_section( 'map_settings', 'Map Settings', 'dbi_plugin_section_text', 'dbi_example_plugin' );

    add_settings_field( 'dbi_plugin_setting_api_key', 'API Key', 'dbi_plugin_setting_api_key', 'dbi_example_plugin', 'map_settings' );
    add_settings_field( 'dbi_plugin_style_url', 'Style URL', 'dbi_plugin_style_url', 'dbi_example_plugin', 'map_settings' );
}


function dbi_plugin_section_text() {
    echo '<p>Einstellungen für die Kartenanbindung</p>';
}

function dbi_plugin_setting_api_key() {
    $options = get_option( 'dbi_example_plugin_options' );
    echo "<input id='dbi_plugin_setting_api_key' name='dbi_example_plugin_options[api_key]' type='text' value='" . esc_attr( $options['api_key'] ) . "' />";
}

function dbi_plugin_style_url() {
    $options = get_option( 'dbi_example_plugin_options' );
    echo "<input id='dbi_plugin_style_url' name='dbi_example_plugin_options[style_url]' type='text' value='" . esc_attr( $options['style_url'] ) . "' />";
}

add_action( 'admin_init', 'dbi_register_settings' );
add_action( 'admin_menu', 'dbi_add_settings_page' );
?>