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

        // Counter Repeater
        $old = get_post_meta($post_id, '_cpm_counter_items', true);
        $new = array();


        $labels = $_POST['label'];
        $values = $_POST['value'];
        $icons = $_POST['icon'];

        $count = count($labels);

        for ($i = 0; $i < $count; $i++) {
            if ($labels[$i] != '') :
                $new[$i]['label'] = stripslashes(strip_tags($labels[$i]));
                $new[$i]['value'] = stripslashes(strip_tags($values[$i]));
                $new[$i]['icon'] = stripslashes(strip_tags($icons[$i]));
            endif;
        }

        if (!empty($new) && $new != $old)
            update_post_meta($post_id, '_cpm_counter_items', $new);
        elseif (empty($new) && $old)
            delete_post_meta($post_id, '_cpm_counter_items', $old);
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
        $repeatable_counter_items = get_post_meta($post->ID, '_cpm_counter_items', true);

        // Display the form, using the current value.
        ?>
        <script>
        jQuery(document).ready(function($) {
            $('#js-add-item').on('click', function() {
                var $row = $('.empty-row.screen-reader-text.row-items').clone(true);
                $row.removeClass('empty-row screen-reader-text');
                $('#js-items').append($row);
                return false;
            });

            $('.js-remove-item').on('click', function() {
                $(this).parent().remove();
                return false;
            });

            $('#js-facts').sortable({
                opacity: 0.6,
                revert: true,
                cursor: 'move',
                handle: '.js-sort'
            });
        });
        </script>

<div class="experiment-metabox-container">
            <h4>Counter</h4>
            <p>Der erste Wert in den Zeilen ist die Beschriftung (bsp.: Bewilligte Projekte), der Zweite der Wert (bsp.: 283), der dritte Wert beinhaltet die URL zum Bild (bsp.: https://sas-sachsen.dev.local:8890/wp-content/uploads/2022/10/2022-09-27_Revierstammtisch_Rackwitz_06.png). Die URL ist dem Datei-URL Feld des Bildes in der Mediathek zu entnehmen.</p>
            <ul id="js-items">
                <li class="product empty-row screen-reader-text row-items">
                    <a class="button js-remove-item" title="' . esc_attr(__('Click to remove the element', 'your_text_domain')) . '">-</a>
                    <input type="text" name="label[]" value="" />
                    <input type="text" name="value[]" value="" />
                    <input type="text" name="icon[]" value="" />
                    <a class="js-sort" title="' . esc_attr(__('Click and drag to sort', 'your_text_domain')) . '">|||</a>
                </li>
                <?php
                if ($repeatable_counter_items) {
                    foreach ($repeatable_counter_items as $counter_item) {
                        echo $this->cpm_get_data_row($counter_item['label'], $counter_item['value'], $counter_item['icon'], false, "label", "value", "icon", "item");
                    }
                }
                ?>
            </ul>
            <a id="js-add-item" class="button">Zeile hinzufügen</a>
        </div>
       
<?php
}
function cpm_get_data_row($valueFirst, $valueSecond, $valueThird, $isHidden = false, $fieldNameFirst, $fieldNameSecond, $fieldNameThird, $type)
{
    return '
<li class="product row-' . $type . 's ' . ($isHidden ? esc_attr('empty-row screen-reader-text') : esc_attr('')) . '">
    <a class="button js-remove-' . $type . '" title="' . esc_attr(__('Click to remove the element', 'your_text_domain')) . '">-</a>
    <input type="text" name="' . $fieldNameFirst . '[]" value="' . (!empty($valueFirst) ? esc_attr($valueFirst) : esc_attr('')) . '" />
    <input type="text" name="' . $fieldNameSecond . '[]" value="' . (!empty($valueSecond) ? esc_attr($valueSecond) : esc_attr('')) . '" />
    <input type="text" name="' . $fieldNameThird . '[]" value="' . (!empty($valueThird) ? esc_attr($valueThird) : esc_attr('')) . '" />
    <a class="js-sort" title="' . esc_attr(__('Click and drag to sort', 'your_text_domain')) . '">|||</a>
</li>';
}
}
?>
