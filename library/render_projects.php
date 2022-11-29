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
$project_more_links = get_post_meta($project_id, '_cpm_project_links', true);

$project_kosten = get_post_meta($project_id, '_cpm_project_kosten', true);
$project_kosten = number_format($project_kosten, 0, ',', '.');
$project_proj_time = get_post_meta($project_id, '_cpm_project_proj_time', true);
$project_status = get_post_meta($project_id, '_cpm_project_status', true);
$project_projekttraeger = get_post_meta($project_id, '_cpm_project_projekttraeger', true);
$project_datenstand = get_post_meta($project_id, '_cpm_project_datenstand', true);
$project_facts = get_post_meta($project_id, '_cpm_project_facts', true);

$content_post = get_post($project_id);
$content = $content_post->post_content;
$content = apply_filters('the_content', $content);
$project_content = str_replace(']]>', ']]&gt;', $content);

$item_marker_class = 0;
$item_arrow_class = 0;

$item_marker_class = "project-list-container__body--projects__item__marker";
$item_arrow_class = "project-list-container__body--projects__item__arrow";

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
<h1 class='h3'>" . $project_title . "</h1>";
if( $project_kosten ){
    $display_posts_script .= "<p><span class='font-bold'>Gesamtkosten (vsl.): </span>" . $project_kosten . "€</p>";
    }
    if( $project_proj_time ){
    $display_posts_script .= "<p><span class='font-bold'>Realisierungszeitraum (vsl.): </span>";
        $display_posts_script .= $project_proj_time;
        $display_posts_script .= "</p>";
    }
    if( $project_status ){
    $display_posts_script .= "<p><span class='font-bold'>Status: </span>" . $project_status . "</p><br>";
    } 
    if( $project_projekttraeger ){
        $display_posts_script .= "<p><span class='font-bold'>Projektträger: </span>" . $project_projekttraeger . "</p><br>";
        } 
    if( $project_datenstand ){
        $display_posts_script .= "<p><span class='font-bold'>Datenstand: </span>" . $project_datenstand . "</p><br>";
         } 
if($project_facts){
    foreach ( $project_facts as $fact ) { 
        if ($fact['label'] && $fact['value']){
            $display_posts_script .= "<p><span class='font-bold'>" . $fact['label'] . ": </span>" . $fact['value'] . "</p><br>";
        }
    }
}
$display_posts_script .= "<p>" . $project_content . "</p>";
if( $project_link ){
$display_posts_script .= "<a class='project-content__container__more-link' href='" . $project_link . "'>Mehr Erfahren</a>";
}

if($project_more_links){
    $display_posts_script .= "<br><br><h4>Weitere Projektlinks</h4><br>";
    foreach ( $project_more_links as $more_link ) { 
        if ($more_link['cpm_proj_link_url'] && $more_link['cpm_proj_link_description']){
            $display_posts_script .= "<a href='" . $more_link['cpm_proj_link_url'] . "'>" . $more_link['cpm_proj_link_description'] . "</a><br>";
        }
    }
}

$display_posts_script .="</div>
</div>
</div>";
