<?php
$project_title = get_the_title();
$project_id = get_the_ID();
$project_link = get_post_meta($project_id, '_cpm_project_link', true);
$project_types = 0;
$project_types = get_the_terms($project_id, 'typ');
$project_type = $project_types[0];
$project_type = $project_type->name;
$project_themes = 0;
$project_themes = get_the_terms($project_id, 'thema');
$project_theme = $project_themes[0];
$project_theme = $project_theme->name;
$custom_logo_id = get_theme_mod( 'custom_logo' );
$logo = wp_get_attachment_image_src( $custom_logo_id , 'full' );
$logo_url = esc_url($logo[0]);
$home_url = get_home_url();

$item_marker_class = 0;
$item_arrow_class = 0;

$item_marker_class = "project-list-container__body--projects__item__marker";
$item_arrow_class = "project-list-container__body--projects__item__arrow";

if($project_type == 'Landesprojekt'){
    $item_marker_class .= '--blau';
    $item_arrow_class .= '--blau';
}
else if($project_type == 'Kommunalprojekt'){
    $item_marker_class .= '--pink';
    $item_arrow_class .= '--pink';
}
if($project_theme == 'Digitalisierung, Breitband- und Mobilfunkinfrastruktur'){
    $item_marker_class .= '--digitalisierung';
}
else if($project_theme == 'Infrastrukturen für Forschung, Innovation, Technologietransfer'){
    $item_marker_class .= '--forschung';
}
else if($project_theme == 'Klima- und Umweltschutz'){
    $item_marker_class .= '--klimaschutz';
}
else if($project_theme == 'Naturschutz und Landschaftspflege'){
    $item_marker_class .= '--landschaftspflege';
}
else if($project_theme == 'Öffentliche Fürsorge'){
    $item_marker_class .= '--fuersorge';
}
else if($project_theme == 'Städtebau, Stadt- und Regionalentwicklung'){
    $item_marker_class .= '--staedtebau';
}
else if($project_theme == 'Touristische Infrastruktur'){
    $item_marker_class .= '--tourismus';
}
else if($project_theme == 'Verkehr'){
    $item_marker_class .= '--verkehr';
}
else if($project_theme == 'Wirtschaftsnahe Infrastruktur'){
    $item_marker_class .= '--wirtschaft';
}

$project_theme = 0;
$project_type = 0;

$display_posts_script .= "<div data-id='" . $project_id . "'class='project-list-item project-list-container__body--projects__item flex'>";
$display_posts_script .= "<div class='list-item-txt-wrapper flex' onclick='toggleActiveState(this.parentNode)'><div class='project-list-container__body--projects__item__marker " . $item_marker_class . "'></div>";
$display_posts_script .= "<h4>" . $project_title . "</h4></div>";
$display_posts_script .= "<div class='project-list-container__body--projects__item__arrow " . $item_arrow_class . "'></div>
<div class='project-content project-content--disabled project-list-container__body--projects__item__content'>
<div class='project-list-container__head side-padding-40'>
<a class='project-list-container__head__logo' href='" . home_url() . "'><img src='" . $logo_url . "'>
    <p>zurück zur Startseite</p></a>
</div>
<div class='project-content__img-wrapper'>";
if ( has_post_thumbnail( $project_id ) ) {
    $display_posts_script .= get_the_post_thumbnail( $project_id, 'thumbnail' );
}
$display_posts_script .= "<div class='cmp__go-back' onclick='toggleActiveStateFromContent(this.parentNode)'></div>
</div>
<div class='project-content__container'>
<h1 class='h2'>" . $project_title . "</h1>
<p>" . get_post_field('post_content', $project_id) . "</p>
<a class='project-content__container__more-link' href='" . $project_link . "'>Mehr Erfahren</a>
</div>
</div>
</div>";
?>