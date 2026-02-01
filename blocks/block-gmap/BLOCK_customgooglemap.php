<?php
/**
 * Block Name: BLOCK_customgooglemap
 *
 * This is the template that displays the featured news block.
 * @param   array $block The block settings and attributes.
 */
?>
<?php

// Create class attribute allowing for custom "className" values.
    $CLS_W = 'map-container iframe-container lazyload ';
    $ROLLED_UP_BY_DEFAULT = get_field( 'rolled_up_by_default' );
    if($ROLLED_UP_BY_DEFAULT) :
        $CLS_W .= ' rolled-up';
    endif;
    $GMAP_API_KEY = get_field( 'google_map_api_key' );
    $GMAP_MARKER_IMG_ID = get_field( 'gmap_marker_img' );
    $GMAP_MARKER_IMG_URL = wp_get_attachment_image_url( $GMAP_MARKER_IMG_ID, '' );
    $LAT = get_field( 'gps_n' );
    $LNG = get_field( 'gps_e' );
    $classes = ( ! empty( $block['className'] ) ) ? sprintf( $CLS_W . ' %s', $block['className'] ) : $CLS_W;
    $id = 'LKBA-' . $block['id'];
?>
<div id="<?php echo esc_attr( $id ) ?>" class="<?php echo esc_attr( $classes ); ?>">
        <div class="overlay" onclick="style.pointerEvents='none'"></div>
        <div id="map" class="map-cont"></div>
        <script>
            var map;
            let _LAT = '<?php echo $LAT; ?>',
                _LNG = '<?php echo $LNG; ?>';

                _LAT = parseFloat(_LAT);
                _LNG = parseFloat(_LNG);

// console.log(_LAT + "-" + _LNG);
            function initMap() {
            map = new google.maps.Map(document.getElementById('map'), {
                center: {lat: _LAT, lng: _LNG},
                zoom: 18,
                zoomControl: true,
                mapTypeControl: false,
                scaleControl: false,
                streetViewControl: false,
                rotateControl: false,
                fullscreenControl: false,
                styles: [{"elementType": "geometry", "stylers": [{"color": "#E6DFCF"} ] }, {"elementType": "labels.icon", "stylers": [{"color": "#6F6C64"} ] }, {"elementType": "labels.text.fill", "stylers": [{"color": "#6F6C64"} ] }, {"elementType": "labels.text.stroke", "stylers": [{"visibility": "off"} ] }, {"featureType": "administrative", "elementType": "geometry", "stylers": [{"color": "#E8E1D1"} ] }, {"featureType": "administrative.country", "elementType": "labels.text.fill", "stylers": [{"color": "#6F6C64"} ] }, {"featureType": "administrative.land_parcel", "stylers": [{"visibility": "on"} ] }, {"featureType": "administrative.locality", "elementType": "labels.text.fill", "stylers": [{"color": "#6F6C64"} ] }, {"featureType": "poi", "elementType": "labels.text.fill", "stylers": [{"color": "#6F6C64"} ] }, {"featureType": "poi.business", "stylers": [{"visibility": "on"} ] }, {"featureType": "poi.park", "elementType": "geometry", "stylers": [{"color": "#E6DFCF"} ] }, {"featureType": "poi.park", "elementType": "labels.text", "stylers": [{"visibility": "on"} ] }, {"featureType": "poi.park", "elementType": "labels.text.fill", "stylers": [{"color": "#6F6C64"} ] }, {"featureType": "poi.park", "elementType": "labels.text.stroke", "stylers": [{"visibility": "off"} ] }, {"featureType": "road", "elementType": "geometry.fill", "stylers": [{"color": "#B4AFA2"} ] }, {"featureType": "road", "elementType": "labels.text.fill", "stylers": [{"color": "#6F6C64"} ] }, {"featureType": "road.arterial", "elementType": "geometry", "stylers": [{"color": "#B4AFA2"} ] }, {"featureType": "road.highway", "elementType": "geometry", "stylers": [{"color": "#B4AFA2"} ] }, {"featureType": "road.highway.controlled_access", "elementType": "geometry", "stylers": [{"color": "#B4AFA2"} ] }, {"featureType": "road.local", "elementType": "labels.text.fill", "stylers": [{"color": "#6F6C64"} ] }, {"featureType": "transit", "elementType": "labels.text.fill", "stylers": [{"color": "#6F6C64"} ] }, {"featureType": "water", "elementType": "geometry", "stylers": [{"color": "#BCB6A9"} ] }, {"featureType": "water", "elementType": "labels.text.fill", "stylers": [{"color": "#6F6C64"} ] } ]

                // #2a2a27
            });
            // var image = '<?php #echo get_template_directory_uri(); ?>' +'/assets/images/rezervace-map-marker.png';
            var image = '<?php echo $GMAP_MARKER_IMG_URL; ?>';
            var MAPMarker = new google.maps.Marker({
                position: {lat: _LAT, lng: _LNG},
                map: map,
                icon: image
            });
            }
        </script>
        <script src="https://maps.googleapis.com/maps/api/js?key=<?=$GMAP_API_KEY;?>&callback=initMap" async defer></script>
        <style>.gm-style-mot {margin: auto; }</style>
    </div>