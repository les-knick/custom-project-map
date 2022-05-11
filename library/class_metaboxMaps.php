<?php
class metaboxMaps
{

    /**
     * Hook into the appropriate actions when the class is constructed.
     */
    public function __construct()
    {
        add_action('add_meta_boxes', array($this, 'add_meta_box'));
        add_action('save_post',      array($this, 'save'));
    }

    /**
     * Adds the meta box container.
     */
    public function add_meta_box($post_type)
    {
        // Limit meta box to certain post types.
        $post_types = array('map');

        if (in_array($post_type, $post_types)) {
            add_meta_box(
                'map_settings',
                __('Karteneinstellungen', 'textdomain'),
                array($this, 'render_meta_box_content'),
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
    public function save($post_id)
    {

        /*
         * We need to verify this came from the our screen and with proper authorization,
         * because save_post can be triggered at other times.
         */

        // Check if our nonce is set.
        if (!isset($_POST['cpm_map_inner_custom_box_nonce'])) {
            return $post_id;
        }

        $nonce = $_POST['cpm_map_inner_custom_box_nonce'];

        // Verify that the nonce is valid.
        if (!wp_verify_nonce($nonce, 'cpm_map_inner_custom_box')) {
            return $post_id;
        }

        /*
         * If this is an autosave, our form has not been submitted,
         * so we don't want to do anything.
         */
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }

        // Check the user's permissions.
        if ('page' == $_POST['post_type']) {
            if (!current_user_can('edit_page', $post_id)) {
                return $post_id;
            }
        } else {
            if (!current_user_can('edit_post', $post_id)) {
                return $post_id;
            }
        }

        /* OK, it's safe for us to save the data now. */

        // Sanitize the user input.
        $styleUrl = sanitize_text_field($_POST['cpm_style-url']);
        $accessToken = sanitize_text_field($_POST['cpm_access-token']);

        // Update the meta field.
        update_post_meta($post_id, '_cpm_map_style-url', $styleUrl);
        update_post_meta($post_id, '_cpm_map_access-token', $accessToken);
    }


    /**
     * Render Meta Box content.
     *
     * @param WP_Post $post The post object.
     */
    public function render_meta_box_content($post)
    {

        // Add an nonce field so we can check for it later.
        wp_nonce_field('cpm_map_inner_custom_box', 'cpm_map_inner_custom_box_nonce');

        // Use get_post_meta to retrieve an existing value from the database.
        $value_styleUrl = get_post_meta($post->ID, '_cpm_map_style-url', true);
        $value_accessToken = get_post_meta($post->ID, '_cpm_map_access-token', true);

        // Display the form, using the current value.
    ?>
        <label for="cpm_access-token">
            <?php _e('Access Token', 'textdomain'); ?>
        </label>
        <input type="text" id="cpm_access-token" name="cpm_access-token" value="<?php echo esc_attr($value_accessToken); ?>" size="30" />
        <br><br>
        <label for="cpm_style-url">
            <?php _e('Style URL', 'textdomain'); ?>
        </label>
        <input type="text" id="cpm_style-url" name="cpm_style-url" value="<?php echo esc_attr($value_styleUrl); ?>" size="30" />
<?php
    }
}
?>