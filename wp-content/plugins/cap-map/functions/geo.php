<?php

/**
 * Keep it simple, functions for now, no classes
 */

/**
 * Get city data from geo_city
 * @param $post_id
 * @param $lookup
 * @return array
 */
function geo_city_get($post_id,$lookup) {
    global $wpdb;
    $sql    = "SELECT id,CityID,Lat,Lng,CountyID,Name FROM geo_city WHERE Name = '$lookup'";
    $posts  = $wpdb->get_results($sql);
    //echo 'wtf';
    //_err($posts);
    return (array) $posts[0];
}

/**
 * Get just the lat/lng of city
 * @param $post_id
 * @param $lookup
 * @return array
 */
function geo_city_lats_get($post_id,$lookup) {
    global $wpdb;
    $sql    = "SELECT Lat,Lng FROM geo_city WHERE Name = '$lookup'";
    $posts  = $wpdb->get_results($sql);
    //echo 'wtf';
    //_err($posts);
    return (array) $posts[0];
}

/**
 * Get county data from geo_county
 * @param $post_id
 * @param $lookup
 * @return array
 */
function geo_county_list($post_id,$lookup) {
    global $wpdb;
    //LEFT JOIN to get lat,lng from city
    $sql    = "SELECT cnt.name as county_name,cty.id as id,cnt.geoid,cty.Lat,cty.Lng,cty.Name as city_name
                FROM geo_county cnt
                LEFT JOIN geo_city cty on cnt.geoid = cty.CountyID
                WHERE cnt.name = '$lookup'";
    $posts  = $wpdb->get_results($sql); //_err($posts);
    return (array) $posts;
}

/**
 * Get county data from geo_county
 * @param $post_id
 * @param $lookup
 * @return array
 */
function geo_county_get($post_id,$lookup) {
    global $wpdb;
    //LEFT JOIN to get lat,lng from city
    $sql    = "SELECT cnt.name as county_name,cnt.geoid
                FROM geo_county cnt
                WHERE cnt.name = '$lookup'";
    $posts  = $wpdb->get_results($sql); //_err($posts);
    //return (array) $posts;
    return (array) $posts[0];
}

/**
 * Return all cities/municipalities within a county ID
 * LEFT JOIN need county_name
 * @param $county_id
 * @return mixed
 */
function geo_city_list($county_id) {

    global $wpdb;
    $sql    = "SELECT cty.Name as city_name,cty.Lat,cty.Lng, cnt.name as county_name
                FROM geo_city cty
                LEFT JOIN geo_county cnt on  cty.CountyID = cnt.geoid
                 WHERE cty.CountyID = '$county_id'";
    $posts  = $wpdb->get_results($sql);
    return $posts;
}

/**
 * Add geo meta tags
 *
 */
function _geo_meta_tags() {

    global $lat, $lng;

    if($lat) {
        $meta = <<<EOT
    <meta name="geo.region" content="US-PA" />
    <meta name="geo.position" content="$lat;$lng" />
    <meta name="ICBM" content="$lat, $lng" />
EOT;

    } else {
        $meta = <<<EOT
    <meta name="geo.region" content="US-PA" />
    <meta name="geo.position" content="40.263835;-76.884821" />
    <meta name="ICBM" content="40.263835, -76.884821" />
EOT;
    }

    echo $meta;
}
