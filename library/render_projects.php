<?php
$project_title = get_the_title();
$project_types[] = get_the_terms(get_the_ID(), 'typ');
foreach ( $project_types as $project_type ) {
    $project_type_id = $project_type->ID;
}


$display_posts_script .= "<div class='project-list-container__body--projects__item'>";
$display_posts_script .= "<div class='project-list-container__body--projects__item__marker marker-type--" . $project_type_id . "'></div>";
$display_posts_script .= "<h3>" . $project_title . "</h3>";
$display_posts_script .= "<div class='project-list-container__body--projects__item__arrow'></div>";
$display_posts_script .= "</div>";
?>