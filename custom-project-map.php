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

function cptui_register_my_cpts() {

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
		"publicly_queryable" => true,
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
		"rewrite" => [ "slug" => "project", "with_front" => true ],
		"query_var" => true,
		"menu_icon" => "dashicons-location",
		"supports" => [ "title", "editor", "thumbnail" ],
		"taxonomies" => [ "category" ],
		"show_in_graphql" => false,
	];

	register_post_type( "project", $args );

	/**
	 * Post Type: Karten.
	 */

	$labels = [
		"name" => __( "Karten", "twentytwentyone" ),
		"singular_name" => __( "Karte", "twentytwentyone" ),
	];

	$args = [
		"label" => __( "Karten", "twentytwentyone" ),
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
		"rewrite" => [ "slug" => "map", "with_front" => true ],
		"query_var" => true,
		"menu_icon" => "dashicons-location-alt",
		"supports" => [ "title" ],
		"show_in_graphql" => false,
	];

	register_post_type( "map", $args );
}

add_action( 'init', 'cptui_register_my_cpts' );

// SETTINGS PAGE

function add_settings_page() {
    add_options_page( 'Custom Project Map Settings', 'Custom Project Map', 'manage_options', 'cpm-example-plugin', 'render_plugin_settings_page' );
}

function render_plugin_settings_page() {
    ?>
    <h2>Custom Project Map Settings</h2>
    <form action="options.php" method="post">
        <?php 
        settings_fields( 'cpm_options' );
        do_settings_sections( 'cpm' ); ?>
        <input name="submit" class="button button-primary" type="submit" value="<?php esc_attr_e( 'Save' ); ?>" />
    </form>
    <?php
}

function register_settings() {
    register_setting( 'cpm_options', 'cpm_options', 'cpm_options_validate' );
    add_settings_section( 'map_settings', 'Map Settings', 'plugin_section_text', 'cpm' );

    add_settings_field( 'plugin_setting_access_token', 'Access Token', 'plugin_setting_access_token', 'cpm', 'map_settings' );
    add_settings_field( 'plugin_style_url', 'Style URL', 'plugin_style_url', 'cpm', 'map_settings' );
}


function plugin_section_text() {
    echo '<p>Einstellungen für die Kartenanbindung</p>';
}

function plugin_setting_access_token() {
    $options = get_option( 'cpm_options' );
    echo "<input id='plugin_setting_access_token' name='cpm_options[access_token]' type='text' value='" . esc_attr( $options['access_token'] ) . "' />";
}

function plugin_style_url() {
    $options = get_option( 'cpm_options' );
    echo "<input id='plugin_style_url' name='cpm_options[style_url]' type='text' value='" . esc_attr( $options['style_url'] ) . "' />";
}

add_action( 'admin_init', 'register_settings' );
add_action( 'admin_menu', 'add_settings_page' );


// BACKEND METABOX (CUSTOM FIELDS)

// Metabox Projects

/**
 * Calls the class on the post edit screen.
 */
function call_metaboxProjects() {
    new metaboxProjects();
}
 
if ( is_admin() ) {
    add_action( 'load-post.php',     'call_metaboxProjects' );
    add_action( 'load-post-new.php', 'call_metaboxProjects' );
}

/**
 * The Class.
 */
class metaboxProjects {
 
    /**
     * Hook into the appropriate actions when the class is constructed.
     */
    public function __construct() {
        add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
        add_action( 'save_post',      array( $this, 'save'         ) );
    }
 
    /**
     * Adds the meta box container.
     */
    public function add_meta_box( $post_type ) {
        // Limit meta box to certain post types.
        $post_types = array( 'project' );
 
        if ( in_array( $post_type, $post_types ) ) {
            add_meta_box(
                'coordinates',
                __( 'Koordinaten', 'textdomain' ),
                array( $this, 'render_meta_box_content' ),
                $post_type,
                'advanced',
                'high'
            );
        }
    }
 
    /**
     * Save the meta when the post is saved.
     *
     * @param int $post_id The ID of the post being saved.
     */
    public function save( $post_id ) {
 
        /*
         * We need to verify this came from the our screen and with proper authorization,
         * because save_post can be triggered at other times.
         */
 
        // Check if our nonce is set.
        if ( ! isset( $_POST['cpm_project_inner_custom_box_nonce'] ) ) {
            return $post_id;
        }
 
        $nonce = $_POST['cpm_project_inner_custom_box_nonce'];
 
        // Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $nonce, 'cpm_project_inner_custom_box' ) ) {
            return $post_id;
        }
 
        /*
         * If this is an autosave, our form has not been submitted,
         * so we don't want to do anything.
         */
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return $post_id;
        }
 
        // Check the user's permissions.
        if ( 'page' == $_POST['post_type'] ) {
            if ( ! current_user_can( 'edit_page', $post_id ) ) {
                return $post_id;
            }
        } else {
            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return $post_id;
            }
        }
 
        /* OK, it's safe for us to save the data now. */
 
        // Sanitize the user input.
        $latitude = sanitize_text_field( $_POST['cpm_latitue'] );
		$longitude = sanitize_text_field( $_POST['cpm_longitude'] );
 
        // Update the meta field.
        update_post_meta( $post_id, '_cpm_project_latitude', $latitude );
		update_post_meta( $post_id, '_cpm_project_longitude', $longitude );
    }
 
 
    /**
     * Render Meta Box content.
     *
     * @param WP_Post $post The post object.
     */
    public function render_meta_box_content( $post ) {
 
        // Add an nonce field so we can check for it later.
        wp_nonce_field( 'cpm_project_inner_custom_box', 'cpm_project_inner_custom_box_nonce' );
 
        // Use get_post_meta to retrieve an existing value from the database.
        $value_latitude = get_post_meta( $post->ID, '_cpm_latitude', true );
		$value_longitude = get_post_meta( $post->ID, '_cpm_longitude', true );
 
        // Display the form, using the current value.
        ?>
        <label for="cpm_latitude">
            <?php _e( 'Breitengrad', 'textdomain' ); ?>
        </label>
        <input type="text" id="cpm_latitude" name="cpm_latitude" value="<?php echo esc_attr( $value_latitude ); ?>" size="30" />
		<br><br>
		<label for="cpm_longitude">
            <?php _e( 'Längengrad', 'textdomain' ); ?>
        </label>
        <input type="text" id="cpm_longitude" name="cpm_longitude" value="<?php echo esc_attr( $value_longitude ); ?>" size="30" />
        <?php
    }
}

// Metabox Maps

/**
 * Calls the class on the post edit screen.
 */
function call_metaboxMaps() {
    new metaboxMaps();
}
 
if ( is_admin() ) {
    add_action( 'load-post.php',     'call_metaboxMaps' );
    add_action( 'load-post-new.php', 'call_metaboxMaps' );
}

/**
 * The Class.
 */
class metaboxMaps {
 
    /**
     * Hook into the appropriate actions when the class is constructed.
     */
    public function __construct() {
        add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
        add_action( 'save_post',      array( $this, 'save'         ) );
    }
 
    /**
     * Adds the meta box container.
     */
    public function add_meta_box( $post_type ) {
        // Limit meta box to certain post types.
        $post_types = array( 'map' );
 
        if ( in_array( $post_type, $post_types ) ) {
            add_meta_box(
                'map_settings',
                __( 'Karteneinstellungen', 'textdomain' ),
                array( $this, 'render_meta_box_content' ),
                $post_type,
                'advanced',
                'high'
            );
        }
    }
 
    /**
     * Save the meta when the post is saved.
     *
     * @param int $post_id The ID of the post being saved.
     */
    public function save( $post_id ) {
 
        /*
         * We need to verify this came from the our screen and with proper authorization,
         * because save_post can be triggered at other times.
         */
 
        // Check if our nonce is set.
        if ( ! isset( $_POST['cpm_map_inner_custom_box_nonce'] ) ) {
            return $post_id;
        }
 
        $nonce = $_POST['cpm_map_inner_custom_box_nonce'];
 
        // Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $nonce, 'cpm_map_inner_custom_box' ) ) {
            return $post_id;
        }
 
        /*
         * If this is an autosave, our form has not been submitted,
         * so we don't want to do anything.
         */
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return $post_id;
        }
 
        // Check the user's permissions.
        if ( 'page' == $_POST['post_type'] ) {
            if ( ! current_user_can( 'edit_page', $post_id ) ) {
                return $post_id;
            }
        } else {
            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return $post_id;
            }
        }
 
        /* OK, it's safe for us to save the data now. */
 
        // Sanitize the user input.
        $styleUrl = sanitize_text_field( $_POST['cpm_style-url'] );
		$accessToken = sanitize_text_field( $_POST['cpm_access-token'] );
 
        // Update the meta field.
        update_post_meta( $post_id, '_cpm_map_style-url', $styleUrl );
		update_post_meta( $post_id, '_cpm_map_access-token', $accessToken );
    }
 
 
    /**
     * Render Meta Box content.
     *
     * @param WP_Post $post The post object.
     */
    public function render_meta_box_content( $post ) {
 
        // Add an nonce field so we can check for it later.
        wp_nonce_field( 'cpm_map_inner_custom_box', 'cpm_map_inner_custom_box_nonce' );
 
        // Use get_post_meta to retrieve an existing value from the database.
        $value_styleUrl = get_post_meta( $post->ID, '_cpm_map_style-url', true );
		$value_accessToken = get_post_meta( $post->ID, '_cpm_map_access-token', true );
 
        // Display the form, using the current value.
        ?>
        <label for="cpm_access-token">
            <?php _e( 'Access Token', 'textdomain' ); ?>
        </label>
        <input type="text" id="cpm_access-token" name="cpm_access-token" value="<?php echo esc_attr( $value_accessToken ); ?>" size="30" />
		<br><br>
		<label for="cpm_style-url">
            <?php _e( 'Style URL', 'textdomain' ); ?>
        </label>
        <input type="text" id="cpm_style-url" name="cpm_style-url" value="<?php echo esc_attr( $value_styleUrl ); ?>" size="30" />
        <?php
    }
}

// SHORTCODES

// function that runs when shortcode is called
function wpb_demo_shortcode() { 
  
	// Things that you want to do.
	$message = 'Hello world!'; 
	  
	// Output needs to be return
	return $message;
	}
	// register shortcode
	add_shortcode('map', 'wpb_demo_shortcode');
?>