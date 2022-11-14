<?php
 global $wpdb;
 $locations = $wpdb->get_results(
     "SELECT
    p.ID,
    p.post_title,
    p.post_content,
    pmlat.meta_value AS pmlat,
    pmlong.meta_value AS pmlong
     FROM ahcff_posts AS p /* CHANGE IF SITE IS MOVED */
    INNER JOIN ahcff_postmeta AS pmlat 
             ON p.ID = pmlat.post_id AND pmlat.meta_key = '_cpm_project_latitude'
     INNER JOIN ahcff_postmeta AS pmlong 
             ON p.ID = pmlong.post_id AND pmlong.meta_key = '_cpm_project_longitude'
    WHERE post_status = 'publish' AND post_type = 'project'
    ");

 foreach ($locations as $location) {

     $location_taxTyp_list[] = get_the_terms( $location->ID, 'typ' );
     $location_taxThema_list[] = get_the_terms( $location->ID, 'thema' );

     $locationid = $location->ID;
     $locationtitle = $location->post_title;
     $locationcontent = $location->post_content;
     $locationlat = $location->pmlat;
     $locationlong = $location->pmlong;

     //type

     $locationObj["type"] = "Feature";

     // geometry
     $textArray[] = $locationlong;
     $textArray[] = $locationlat;
     $geometryObj["type"] = "Point";
     $geometryObj["coordinates"] = $textArray;
     $textArray = null;
     $locationObj["geometry"] = $geometryObj;

     // properties
     $propertiesObj["id"] = $locationid;
     $propertiesObj["title"] = $locationtitle;
     $propertiesObj["description"] = $locationcontent;
     $propertiesObj["typ"] = $location_taxTyp_list;
     $propertiesObj["thema"] = $location_taxThema_list;
     $locationObj["properties"] = $propertiesObj;

     $location_taxTyp_list = null;
     $location_taxThema_list = null;

     $collectionObj[] = $locationObj;

 }

 $object["type"] = "FeatureCollection";
 $object["features"] = $collectionObj;



 $json_data = json_encode($object);
 file_put_contents(WP_PLUGIN_DIR . '/custom-project-map/assets/data.geojson', $json_data);
?>