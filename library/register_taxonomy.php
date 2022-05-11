<?php
 // typ taxonomy
 $labels = array(
    'name'              => _x( 'Typen', 'taxonomy general name' ),
    'singular_name'     => _x( 'Typ', 'taxonomy singular name' ),
    'menu_name'         => __( 'Typen' ),
);
register_taxonomy(
    'typ',
    'project',
    array(
        'hierarchical' => true,
        'labels' => $labels,
        'query_var' => true,
        'rewrite' => true,
        'show_admin_column' => true
    )
);
// thema taxonomy
$labels = array(
    'name'              => _x( 'Themen', 'taxonomy general name' ),
    'singular_name'     => _x( 'Thema', 'taxonomy singular name' ),
    'menu_name'         => __( 'Themen' ),
);
register_taxonomy(
    'thema',
    'project',
    array(
        'hierarchical' => true,
        'labels' => $labels,
        'query_var' => true,
        'rewrite' => true,
        'show_admin_column' => true
    )
);
?>