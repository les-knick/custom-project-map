<?php
class metaboxProjects
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
        $post_types = array('project');

        if (in_array($post_type, $post_types)) {
            add_meta_box(
                'coordinates',
                __('Informationen', 'textdomain'),
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
        if (!isset($_POST['cpm_project_inner_custom_box_nonce'])) {
            return $post_id;
        }

        $nonce = $_POST['cpm_project_inner_custom_box_nonce'];

        // Verify that the nonce is valid.
        if (!wp_verify_nonce($nonce, 'cpm_project_inner_custom_box')) {
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
        $latitude = sanitize_text_field($_POST['cpm_latitude']);
        $longitude = sanitize_text_field($_POST['cpm_longitude']);
        $link = sanitize_text_field($_POST['cpm_link']);
        $kosten = sanitize_text_field($_POST['cpm_kosten']);
        $proj_time = sanitize_text_field($_POST['cpm_proj_time']);
        $status = sanitize_text_field($_POST['cpm_status']);

        // Update the meta field.
        update_post_meta($post_id, '_cpm_project_latitude', $latitude);
        update_post_meta($post_id, '_cpm_project_longitude', $longitude);
        update_post_meta($post_id, '_cpm_project_link', $link);
        update_post_meta($post_id, '_cpm_project_kosten', $kosten);
        update_post_meta($post_id, '_cpm_project_proj_time', $proj_time);
        update_post_meta($post_id, '_cpm_project_status', $status);

        $old = get_post_meta($post_id, '_cpm_project_facts', true);
        $new = array();


        $labels = $_POST['label'];
        $values = $_POST['value'];

        $count = count($labels);

        for ($i = 0; $i < $count; $i++) {
            if ($labels[$i] != '') :
                $new[$i]['label'] = stripslashes(strip_tags($labels[$i]));
                $new[$i]['value'] = stripslashes(strip_tags($values[$i]));
            endif;
        }

        if (!empty($new) && $new != $old)
            update_post_meta($post_id, '_cpm_project_facts', $new);
        elseif (empty($new) && $old)
            delete_post_meta($post_id, '_cpm_project_facts', $old);
    }


    /**
     * Render Meta Box content.
     *
     * @param WP_Post $post The post object.
     */
    public function render_meta_box_content($post)
    {

        // Add an nonce field so we can check for it later.
        wp_nonce_field('cpm_project_inner_custom_box', 'cpm_project_inner_custom_box_nonce');

        // Use get_post_meta to retrieve an existing value from the database.
        $value_latitude = get_post_meta($post->ID, '_cpm_project_latitude', true);
        $value_longitude = get_post_meta($post->ID, '_cpm_project_longitude', true);
        $value_link = get_post_meta($post->ID, '_cpm_project_link', true);
        $value_kosten = get_post_meta($post->ID, '_cpm_project_kosten', true);
        $value_proj_time = get_post_meta($post->ID, '_cpm_project_proj_time', true);
        $value_status = get_post_meta($post->ID, '_cpm_project_status', true);

        $repeatable_fields = get_post_meta($post->ID, '_cpm_project_facts', true);


        // Display the form, using the current value.
?>
        <label for="cpm_latitude">
            <?php _e('Breitengrad', 'textdomain'); ?>
        </label>
        <input type="text" id="cpm_latitude" name="cpm_latitude" value="<?php echo esc_attr($value_latitude); ?>" size="30" />
        <br><br>
        <label for="cpm_longitude">
            <?php _e('Längengrad', 'textdomain'); ?>
        </label>
        <input type="text" id="cpm_longitude" name="cpm_longitude" value="<?php echo esc_attr($value_longitude); ?>" size="30" />
        <br><br>
        <label for="cpm_link">
            <?php _e('Link zur Seite', 'textdomain'); ?>
        </label>
        <input type="text" id="cpm_link" name="cpm_link" value="<?php echo esc_attr($value_link); ?>" size="30" />
        <br><br>
        <label for="cpm_kosten">
            <?php _e('Gesamtkosten (vsl.)', 'textdomain'); ?>
        </label>
        <input type="text" id="cpm_kosten" name="cpm_kosten" value="<?php echo esc_attr($value_kosten); ?>" size="30" />
        <br><br>
        <label for="cpm_proj_time">
            <?php _e('Realisierungszeitraum', 'textdomain'); ?>
        </label>
        <input type="text" id="cpm_proj_time" name="cpm_proj_time" value="<?php echo esc_attr($value_proj_time); ?>" size="30" />
        <br><br>
        <label for="cpm_status">
            <?php _e('Status', 'textdomain'); ?>
        </label>
        <input type="text" id="cpm_status" name="cpm_status" value="<?php echo esc_attr($value_status); ?>" size="30" />

        <script>
                    jQuery(document).ready(function($) {
                        $('#js-add').on('click', function() {
                            var $row = $('.empty-row.screen-reader-text').clone(true);
                            $row.removeClass('empty-row screen-reader-text');
                            $('#js-products').append($row);
                            return false;
                        });
        
                        $('.js-remove').on('click', function() {
                            $(this).parent().remove();
                            return false;
                        });
        
                        $('#js-products').sortable({
                            opacity: 0.6,
                            revert: true,
                            cursor: 'move',
                            handle: '.js-sort'
                        });
                    });
                </script>
                <div class="experiment-metabox-container">
                    <br><br><h4>Projektinformationen</h4>
                    <ul id="js-products">
                    <li class="product empty-row screen-reader-text">
		<a class="button js-remove" title="' . esc_attr(__('Click to remove the element', 'your_text_domain')) . '">-</a>
		<input type="text" name="label[]" value="" />
        <input type="text" name="value[]" value="" />
		<a class="js-sort" title="' . esc_attr(__('Click and drag to sort', 'your_text_domain')) . '">|||</a>
	</li>
                        <?php
                        if ($repeatable_fields) {
                            foreach ($repeatable_fields as $field) {
                                echo $this->cpm_get_data_row($field['label'], $field['value'], false);
                            }
                        } else {
                            echo $this->cpm_get_data_row(null, true); // empty product (no args)
                        }
        
                        echo $this->cpm_get_data_row(null, true); // empty hidden one for jQuery
                        ?>
                    </ul>
                    <a id="js-add" class="button">Zeile hinzufügen</a>
                </div>
<?php
    }
    function cpm_get_data_row($valueLabel, $valueValue, $isHidden = false)
    {
        return '
	<li class="product ' . (!empty($isHidden) ? esc_attr('empty-row screen-reader-text') : esc_attr('')) . '">
		<a class="button js-remove" title="' . esc_attr(__('Click to remove the element', 'your_text_domain')) . '">-</a>
		<input type="text" name="label[]" value="' . (!empty($valueLabel) ? esc_attr($valueLabel) : esc_attr('')) . '" />
        <input type="text" name="value[]" value="' . (!empty($valueValue) ? esc_attr($valueValue) : esc_attr('')) . '" />
		<a class="js-sort" title="' . esc_attr(__('Click and drag to sort', 'your_text_domain')) . '">|||</a>
	</li>';
    }
}
?>