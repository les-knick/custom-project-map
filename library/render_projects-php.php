<?php
$project_title = get_the_title();
$project_id = get_the_ID();
$project_link = get_post_meta(get_the_ID(), '_cpm_project_link', true);
$project_types[] = get_the_terms(get_the_ID(), 'typ');
foreach ( $project_types as $project_type ) {
    $project_type_id = $project_type->ID;
}


$display_posts_script .= "<div data-id='" . $project_id . "'class='project-list-item project-list-container__body--projects__item'>";
$display_posts_script .= "<div class='project-list-container__body--projects__item__marker'></div>";
$display_posts_script .= "<h3>" . $project_title . "</h3>";
$display_posts_script .= "<div class='project-list-container__body--projects__item__arrow'></div>
<div class='project-content project-content--disabled project-list-container__body--projects__item__content'>
<div class='project-list-container__head side-padding-40'>
<a class='project-list-container__head__logo'><img src='" . $logo_url . "'></a>
</div>
<div class='project-content__img-wrapper'>";
if ( has_post_thumbnail( $project_id ) ) {
    $display_posts_script .= get_the_post_thumbnail( $project_id, 'thumbnail' );
}
$display_posts_script .= "</div>
<div class='project-content__container'>
<h1 class='cmp-h1'>" . $project_title . "</h1>
<p>" . get_post_field('post_content', $project_id) . "</p>
<a href='" . $project_link . "'>Mehr Erfahren</a>
</div>
</div>
</div>";

?>