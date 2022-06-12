<?php
class metaboxCounter
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
        $post_types = array('counter');

        if (in_array($post_type, $post_types)) {
            add_meta_box(
                'numbers',
                __('Zahlen', 'textdomain'),
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
        if (!isset($_POST['cpm_counter_inner_custom_box_nonce'])) {
            return $post_id;
        }

        $nonce = $_POST['cpm_counter_inner_custom_box_nonce'];

        // Verify that the nonce is valid.
        if (!wp_verify_nonce($nonce, 'cpm_counter_inner_custom_box')) {
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
        $projekte = sanitize_text_field($_POST['cpm_projekt']);
        $zuwendungen = sanitize_text_field($_POST['cpm_zuwendungen']);
        $einwohnerinnen = sanitize_text_field($_POST['cpm_einwohnerinnen']);
        $mittel = sanitize_text_field($_POST['cpm_mittel']);

        // Update the meta field.
        update_post_meta($post_id, '_cpm_counter_projekt', $projekte);
        update_post_meta($post_id, '_cpm_counter_zuwendungen', $zuwendungen);
        update_post_meta($post_id, '_cpm_counter_einwohnerinnen', $einwohnerinnen);
        update_post_meta($post_id, '_cpm_counter_mittel', $mittel);
    }


    /**
     * Render Meta Box content.
     *
     * @param WP_Post $post The post object.
     */
    public function render_meta_box_content($post)
    {

        // Add an nonce field so we can check for it later.
        wp_nonce_field('cpm_counter_inner_custom_box', 'cpm_counter_inner_custom_box_nonce');

        // Use get_post_meta to retrieve an existing value from the database.
        $value_projekte = get_post_meta($post->ID, '_cpm_counter_projekt', true);
        $value_zuwendungen = get_post_meta($post->ID, '_cpm_counter_zuwendungen', true);
        $value_einwohnerinnen = get_post_meta($post->ID, '_cpm_counter_einwohnerinnen', true);
        $value_mittel = get_post_meta($post->ID, '_cpm_counter_mittel', true);

        // Display the form, using the current value.
?>
        <label for="cpm_projekt">
            <?php _e('Bewilligte Projekte', 'textdomain'); ?>
        </label>
        <input type="text" id="cpm_projekt" name="cpm_projekt" value="<?php echo esc_attr($value_projekte); ?>" size="30" />
        <br><br>
        <label for="cpm_zuwendungen">
            <?php _e('Bewilligte Zuwendungen (in T€)', 'textdomain'); ?>
        </label>
        <input type="text" id="cpm_zuwendungen" name="cpm_zuwendungen" value="<?php echo esc_attr($value_zuwendungen); ?>" size="30" />
        <br><br>
        <label for="cpm_einwohnerinnen">
            <?php _e('Einwohnerinnen und Einwohner', 'textdomain'); ?>
        </label>
        <input type="text" id="cpm_einwohnerinnen" name="cpm_einwohnerinnen" value="<?php echo esc_attr($value_einwohnerinnen); ?>" size="30" />
        <br><br>
        <label for="cpm_mittel">
            <?php _e('Bereitgestellte Mittel Für den Strukturwandel (in T€)', 'textdomain'); ?>
        </label>
        <input type="text" id="cpm_mittel" name="cpm_mittel" value="<?php echo esc_attr($value_mittel); ?>" size="30" />
<?php
    }
}
?>