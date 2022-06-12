    <?php
    
    /**
     * Post Type: Projekte.
     */

    $labels = [
        "name" => __("Projekte", "twentytwentyone"),
        "singular_name" => __("Projekt", "twentytwentyone"),
    ];

    $args = [
        "label" => __("Projekte", "twentytwentyone"),
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
        "rewrite" => ["slug" => "project", "with_front" => true],
        "query_var" => true,
        "menu_icon" => "dashicons-location",
        "supports" => ["title", "editor", "thumbnail"],
        "taxonomies" => [],
        "show_in_graphql" => false,
    ];

    register_post_type("project", $args);

    /**
     * Post Type: Karten.
     */

    $labels = [
        "name" => __("Karten", "twentytwentyone"),
        "singular_name" => __("Karte", "twentytwentyone"),
    ];

    $args = [
        "label" => __("Karten", "twentytwentyone"),
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
        "rewrite" => ["slug" => "map", "with_front" => true],
        "query_var" => true,
        "menu_icon" => "dashicons-location-alt",
        "supports" => ["title"],
        "show_in_graphql" => false,
    ];

    register_post_type("map", $args);

        /**
     * Post Type: Counter.
     */

    $labels = [
        "name" => __("Counter", "twentytwentyone"),
        "singular_name" => __("Counter", "twentytwentyone"),
    ];

    $args = [
        "label" => __("Counter", "twentytwentyone"),
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
        "rewrite" => ["slug" => "counter", "with_front" => true],
        "query_var" => true,
        "menu_icon" => "dashicons-chart-bar",
        "supports" => ["title"],
        "taxonomies" => [],
        "show_in_graphql" => false,
    ];

    register_post_type("counter", $args);
    ?>