<?php
// SETTINGS PAGE

function add_settings_page()
{
    add_options_page('Custom Project Map Settings', 'Custom Project Map', 'manage_options', 'cpm-example-plugin', 'render_plugin_settings_page');
}

function render_plugin_settings_page()
{
?>
    <h2>Custom Project Map Settings</h2>
    <form action="options.php" method="post">
        <?php
        settings_fields('cpm_options');
        do_settings_sections('cpm'); ?>
        <input name="submit" class="button button-primary" type="submit" value="<?php esc_attr_e('Save'); ?>" />
    </form>
    <?php
}

function register_settings()
{
    register_setting('cpm_options', 'cpm_options', 'cpm_options_validate');
    add_settings_section('map_settings', 'Map Settings', 'plugin_section_text', 'cpm');

    add_settings_field('plugin_setting_access_token', 'Access Token', 'plugin_setting_access_token', 'cpm', 'map_settings');
    add_settings_field('plugin_style_url', 'Style URL', 'plugin_style_url', 'cpm', 'map_settings');
}


function plugin_section_text()
{
    echo '<p>Einstellungen für die Kartenanbindung</p>';
}

function plugin_setting_access_token()
{
    $options = get_option('cpm_options');
    echo "<input id='plugin_setting_access_token' name='cpm_options[access_token]' type='text' value='" . esc_attr($options['access_token']) . "' />";
}

function plugin_style_url()
{
    $options = get_option('cpm_options');
    echo "<input id='plugin_style_url' name='cpm_options[style_url]' type='text' value='" . esc_attr($options['style_url']) . "' />";
}

add_action('admin_init', 'register_settings');
add_action('admin_menu', 'add_settings_page');
?>