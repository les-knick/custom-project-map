<?php
 $args = shortcode_atts( array(
     
        'id' => '0'

    ), $attr );
    // Things that you want to do.
    $message = "<p>hier kommt die id: " . $args['id'];

    // Output needs to be return
    return $message;
    ?>