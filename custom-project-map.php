<?php
/*
Plugin Name:  Custom Project Map
Plugin URI:   https://knick.design
Description:  Plugin for Custom Project Maps. Extends the wp backend.
Version:      1.0
Author:       Knick Design - Lea Stocker
Author URI:   https://knick.design
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  custom-project-map
Domain Path:  /languages
*/

set_include_path(WP_PLUGIN_DIR . "/custom-project-map/library");

function cpm_enqueue_styles()
{
    wp_enqueue_style('lea_css',  plugins_url('/assets/css/style.css', __FILE__));
}

add_action('wp_enqueue_scripts', 'cpm_enqueue_styles');

function cpm_enqueue_scripts()
{
    wp_enqueue_script('lea_events',  plugins_url('/assets/js/events.js', __FILE__));
}

add_action('wp_enqueue_scripts', 'cpm_enqueue_scripts');

//register two cpts
add_action('init', 'cpm_register_my_cpts');

function cpm_register_my_cpts()
{
    require_once "init_custom_post_types.php";
}


// register two taxonomies to go with the post type
add_action('init', 'cpm_create_taxonomies', 0);

function cpm_create_taxonomies()
{
    require_once "register_taxonomy.php";
}


// register new column on post list view
add_filter('manage_map_posts_columns', 'cpm_map_columns');

function cpm_map_columns($columns)
{
    $columns = array(
        'cb' => $columns['cb'],
        'title' => __('Title'),
        'shortcode' => __('Shortcode', 'cpm'),
        'date' => __('Date')
    );

    return $columns;
}


// show shortcode in new column on post list view
add_action('manage_map_posts_custom_column', 'cpm_map_column', 10, 2);

function cpm_map_column($column, $post_id)
{
    if ($column == 'shortcode') {
        $map_shortcode = "[cpm_map id=&#34;" . $post_id . "&#34;]";
        echo $map_shortcode;
    }
}



// BACKEND METABOX (CUSTOM FIELDS)

// Metabox Projects

/**
 * Calls the class on the post edit screen.
 */
function call_metaboxProjects()
{
    require_once "class_metaboxProjects.php";
    new metaboxProjects();
}

if (is_admin()) {
    add_action('load-post.php',     'call_metaboxProjects');
    add_action('load-post-new.php', 'call_metaboxProjects');
}

// Metabox Maps

/**
 * Calls the class on the post edit screen.
 */
function call_metaboxMaps()
{
    require_once "class_metaboxMaps.php";
    new metaboxMaps();
}

if (is_admin()) {
    add_action('load-post.php',     'call_metaboxMaps');
    add_action('load-post-new.php', 'call_metaboxMaps');
}



// SHORTCODES

// function that runs when shortcode is called
function cpm_map_shortcode($attr)
{
    // require_once "map_shortcode.php";
    $args = shortcode_atts(array(

        'id' => '0'

    ), $attr);

    $map_styleUrl = get_post_meta($args['id'], '_cpm_map_style-url', true);
    $map_accessToken = get_post_meta($args['id'], '_cpm_map_access-token', true);

    $script_open = " <script> ";
    $script_close = " </script> ";

    $custom_logo_id = get_theme_mod( 'custom_logo' );
    $logo = wp_get_attachment_image_src( $custom_logo_id , 'full' );
    $logo_url = esc_url($logo[0]);
    $home_url = get_home_url();

    $map_libs = "<script src='https://api.mapbox.com/mapbox-gl-js/v1.7.0/mapbox-gl.js'></script>
    <link href='https://api.mapbox.com/mapbox-gl-js/v1.7.0/mapbox-gl.css' rel='stylesheet' />";

    $map_script = "mapboxgl.accessToken = '" . $map_accessToken . "'; ";
    $map_script .= "var map = new mapboxgl.Map({ container: 'map-container', style: '" . $map_styleUrl . "', ";
    $map_script .= "center: [12.467, 52.281], zoom: 6 });";
    $map_script .= "var nav = new mapboxgl.NavigationControl({ showCompass: false }); map.addControl(nav, 'bottom-right'); ";

    $getJson_script = "var jsonPath = '" . site_url() . "/wp-content/plugins/custom-project-map/assets/data.geojson'; ";
    $getJson_script .= "fetch (jsonPath)
    .then (function (response) {
        return response.json();
    }).then (function (data) {
        geojsonTest = data;
        addMarkersToMap(geojsonTest);
    }).catch (function (error) {
        console.log ('error: ' + error);
    }); ";

    $addMarkerToMap_script = "function addMarkersToMap(geojson) {";
    $addMarkerToMap_script .= "geojson.features.forEach(function (marker) {
        var el = document.createElement('div');
        var element_class = 'marker marker';
        var element_id = marker.properties.id;
        var element_type = marker.properties.typ[0][0].name;
        var element_theme = marker.properties.thema[0][0].name;
        el.dataset.id = element_id;
        if(element_type == 'Landesprojekt'){
            element_class += '--blau';
        }
        else{
            element_class += '--pink';
        }
        if(element_theme == 'Digitalisierung, Breitband- und Mobilfunkinfrastruktur'){
            element_class += '--digitalisierung';
        }
        else if(element_theme == 'Infrastrukturen für Forschung, Innovation, Technologietransfer'){
            element_class += '--forschung';
        }
        else if(element_theme == 'Klima- und Umweltschutz'){
            element_class += '--klimaschutz';
        }
        else if(element_theme == 'Naturschutz und Landschaftspflege'){
            element_class += '--landschaftspflege';
        }
        else if(element_theme == 'Öffentliche Fürsorge'){
            element_class += '--fuersorge';
        }
        else if(element_theme == 'Städtebau, Stadt- und Regionalentwicklung'){
            element_class += '--staedtebau';
        }
        else if(element_theme == 'Touristische Infrastruktur'){
            element_class += '--tourismus';
        }
        else if(element_theme == 'Verkehr'){
            element_class += '--verkehr';
        }
        else if(element_theme == 'Wirtschaftsnahe Infrastruktur'){
            element_class += '--wirtschaft';
        }
        el.className = element_class;
        el.addEventListener('click', function(){
            console.log('element event listener');
            toggleActiveStateList(this);
            })
         ";

    $addMarkerToMap_script .= "new mapboxgl.Marker(el)
        .setLngLat(marker.geometry.coordinates)
        .addTo(map);
        ";
    $addMarkerToMap_script .= "}); } ";

    $get_terms_types = get_terms([
        'taxonomy' => 'typ',
        'hide_empty' => true,
    ]);

    $display_types_script = '';
    $display_types_script .= "<label><input type='radio' name='typefilter' value='all'><p>Alle</p></label>";
    foreach($get_terms_types as $terms_type){
        $display_types_script .= "<label><input type='radio' name='typefilter' value='" . $terms_type->term_id . "'><p>" . $terms_type->name . "</p></label>";
    }

    $get_terms_themes = get_terms([
        'taxonomy' => 'thema',
        'hide_empty' => true,
    ]);

    $display_themes_script = '';

    foreach($get_terms_themes as $terms_theme){
        $term_name_icon = $terms_theme->name;
        $legend_icon_class = '';
        $legend_icon_class = 'legend__icon';
        if($term_name_icon == 'Digitalisierung, Breitband- und Mobilfunkinfrastruktur'){
            $legend_icon_class .= '--digitalisierung';
        }
        else if($term_name_icon == 'Infrastrukturen für Forschung, Innovation, Technologietransfer'){
            $legend_icon_class .= '--forschung';
        }
        else if($term_name_icon == 'Klima- und Umweltschutz'){
            $legend_icon_class .= '--klimaschutz';
        }
        else if($term_name_icon == 'Naturschutz und Landschaftspflege'){
            $legend_icon_class .= '--landschaftspflege';
        }
        else if($term_name_icon == 'Öffentliche Fürsorge'){
            $legend_icon_class .= '--fuersorge';
        }
        else if($term_name_icon == 'Städtebau, Stadt- und Regionalentwicklung'){
            $legend_icon_class .= '--staedtebau';
        }
        else if($term_name_icon == 'Touristische Infrastruktur'){
            $legend_icon_class .= '--tourismus';
        }
        else if($term_name_icon == 'Verkehr'){
            $legend_icon_class .= '--verkehr';
        }
        else if($term_name_icon == 'Wirtschaftsnahe Infrastruktur'){
            $legend_icon_class .= '--wirtschaft';
        }

        $display_themes_script .= "<label class='check-group'><div class='filter-cat-wrapper flex'><input type='checkbox' name='themefilter[]' value='" . $terms_theme->term_id . "'><p>" . $terms_theme->name . "</p>
        </div><div class='legend flex'>
        <div class='dotted-line'></div><div class='legend__icon " . $legend_icon_class . "'></div></div></label>";
    }

    $display_posts_script = '';

    $filter_script = "const list_items_duplicate = 0;
    jQuery(function($) {
        $('input[type=";
        $filter_script .= '"radio"]';
        $filter_script .= "').click(function() {
                $('#filter').submit();
        });";

        $filter_script .= "$('input[type=";
            $filter_script .= '"checkbox"]';
            $filter_script .= "').click(function() {
                    $('#filter').submit();
            });";

        $filter_script .= "$('#filter').submit(function() {
            var form = $('#filter');
            $.ajax({
                url: form.attr('action'),
                data: form.serialize(), // form data
                type: form.attr('method'), // POST
                beforeSend: function(xhr) {
                },
                success: function(data) { // changing the button label back
                    $('#response').html(data); // insert data
                }
            });
            return false;
        });
    });";

$args = array(
 'post_type' => 'project',
 'orderby' => 'title',
 'order' => 'ASC'
);

$custom_query = new WP_Query($args); 

if ($custom_query->have_posts()) : while($custom_query->have_posts()) : $custom_query->the_post();  
require "render_projects.php";
endwhile;
else :
$display_posts_script .= "<p>Keine Beiträge</p>";
endif;
 wp_reset_postdata(); 

    $list_script = "<div id='project-list-container' class='project-list-container'>
    <div class='project-list-container__head'>
    <a class='project-list-container__head__logo' href='" . home_url() . "'><img src='" . $logo_url . "'>
    <p>zurück zur Startseite</p></a>
    </div>
    <div class='project-list-container__body'>
    <h1 class='project-list-container__body__headline'>Interaktive Projektkarte</h1>
    <div class='project-list-container__body--filter'>
    <h2 class='project-list-container__body__headline project-list-container__body__headline--with-line'>Filter</h2>
    <form action='" . $home_url . "/wp-admin/admin-ajax.php' method='POST' id='filter' class='project-list-container__body--filter__form'>
    <h3 class='project-list-container__body__headline'>Förderungen</h3>" . $display_types_script . 
    "<h3 class='project-list-container__body__headline'>Themen</h3>" . $display_themes_script . 
    "<input type='hidden' name='action' value='myfilter'>
    <button style='display: none;'>Filter</button>
    </form>
    </div>
    <div class='project-list-container__body--projects'>
    <h2 class='project-list-container__body__headline project-list-container__body__headline--with-line'>Projekte</h2><div id='response'>" . $display_posts_script . 
    "</div>
    </div>
    </div>
    </div>
    <div class='mobile-toggle' onclick='toggleProjectList()'><div class='mobile-toggle__handle'></div></div>";

    $events_script = "const list_items = document.querySelectorAll('.project-list-item');
    list_items.forEach(function(list_item){
        list_item.addEventListener('click', toggleActiveState);
      });
      ";


    // Things that you want to do.

    $message = $map_libs;
    $message .= "<div id='map-container'></div>";
    $message .= $list_script;
    $message .= $script_open;
    $message .= $map_script;
    $message .= $getJson_script;
    $message .= $addMarkerToMap_script;
    $message .= $script_close;
    $message .= $script_open;
    $message .= $events_script;
    $message .= $filter_script;
    $message .= $script_close;

    // Output needs to be return
    return $message;
}
// register shortcode
add_shortcode('cpm_map', 'cpm_map_shortcode');

// GENERATE GEOJSON
function generatejson()
{
    require_once "generate_json.php";
}

// This is the action function that outputs the function above into the theme hook under the logo
add_action('save_post_project', 'generatejson', 999);

//FILTER FUNCTION
add_action('wp_ajax_myfilter', 'misha_filter_function'); // wp_ajax_{ACTION HERE} 
add_action('wp_ajax_nopriv_myfilter', 'misha_filter_function');

function misha_filter_function()
{
    $result = array();

    // for categories
   
        $filter_args =  array(
            'post_type'   => 'project',
            'tax_query' => array()
            );

               if (isset($_POST['typefilter'])) {
                $result['type'] = $_POST['typefilter'];
                if($result['type'] != 'all'){
                $filter_args['tax_query'][0] = array(
                            'taxonomy' => 'typ',   // taxonomy name
                            'field' => 'term_id',           // term_id, slug or name
                            'terms' => $result['type']                  // term id, term slug or term name
                );
            }
            }
            if (isset($_POST['themefilter'])) {
                $result['theme'] = $_POST['themefilter'];
                $filter_args['tax_query'][1] = array(
                    'taxonomy' => 'thema',   // taxonomy name
                    'field' => 'term_id',           // term_id, slug or name
                    'terms' => $result['theme'],                  // term id, term slug or term name
                );
            }

        $result_query = new WP_Query($filter_args);

            if ($result_query->have_posts()) : while ($result_query->have_posts()) : $result_query->the_post();
                    include "render_projects.php";
                    echo $display_posts_script;
                    $display_posts_script = '';
                endwhile;
            else : ?>
                <p>Keine Beiträge</p>
            <?php endif;
            wp_reset_postdata();
            $result = '';
            $filter_args = '';
            ?>
            <script>
                filterMarker();
            </script>
            <?php
    die();
}