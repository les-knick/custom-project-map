<?php
add_action('admin_menu', array( $this, 'addPluginAdminMenu' ), 9);

public function addPluginAdminMenu() {
//add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
add_menu_page(  $this->plugin_name, 'Plugin Name', 'administrator', $this->plugin_name, array( $this, 'displayPluginAdminDashboard' ), 'dashicons-chart-area', 26 );

//add_submenu_page( '$parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
add_submenu_page( $this->plugin_name, 'Plugin Name Settings', 'Settings', 'administrator', $this->plugin_name.'-settings', array( $this, 'displayPluginAdminSettings' ));
}
?>