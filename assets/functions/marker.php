<?php

global $wpdb;






/* ROBERTS CODE
$location = $wpdb->get_results(
    "SELECT 
    p.ID,
    p.post_status,
    p.post_type,
    p.post_name, 
    p.post_title, 
    pmtyp.meta_value AS pmtyp,
    pmlat.meta_value AS pmlat,
    pmlong.meta_value AS pmlong,
    pmpage.meta_value AS pmpage,
    pmtext.meta_value AS pmtext
    FROM wp_posts AS p 
    INNER JOIN wp_postmeta AS pmtyp 
            ON p.ID = pmtyp.post_id AND pmtyp.meta_key = 'select_markertyp'
    INNER JOIN wp_postmeta AS pmlat 
            ON p.ID = pmlat.post_id AND pmlat.meta_key = 'txt_latitude'
    INNER JOIN wp_postmeta AS pmlong 
            ON p.ID = pmlong.post_id AND pmlong.meta_key = 'txt_longitude'
    INNER JOIN wp_postmeta AS pmpage 
            ON p.ID = pmpage.post_id AND pmpage.meta_key = 'select_haspage'
    INNER JOIN wp_postmeta AS pmtext 
            ON p.ID = pmtext.post_id AND pmtext.meta_key = 'txt_shorttext'
    WHERE p.post_status = 'publish' 
    AND p.post_type = 'project' 
    
");


 foreach ( $location as $marker ) {
    $i++;
    unset($textArray);
    $m = "marker".$i;
    $markerid = $marker->ID;
    $markertitle = $marker->post_title;
    $markerlatitude = $marker->pmlat;
    $markerlongitude = $marker->pmlong;
    $markertypen = $marker->pmtyp;
    $markerpage = $marker->pmpage;
    $markerlink = $marker->post_name;
    $markertext = $marker->pmtext;
    
    // type
    $markerObj["type"] = "Feature";

    // geometry
    $textArray[] = floatval($markerlongitude); 
    $textArray[] = floatval($markerlatitude); 
    $geometryObj["type"] = "Point";
    $geometryObj["coordinates"] = $textArray;
    $markerObj["geometry"] = $geometryObj;

    // properties
    $propertiesObj["title"] = $markertitle;
    $propertiesObj["description"] = $markertext;
    $propertiesObj["filterby"] = $markertypen;
    $propertiesObj["haspage"] = $markerpage;
    $propertiesObj["link"] = $markerlink;
    $markerObj["properties"] = $propertiesObj;

    $collectionObj[] = $markerObj;

    $myObj["type"] = "FeatureCollection";
    $myObj["features"] = $collectionObj;
    
       // $myObj[] = $collectionObj;
}

$myJSON = json_encode($myObj);

$json_data = json_encode($myObj);
file_put_contents('../wp-content/plugins/custom-project-map/assets', $json_data);  */

?>