jQuery(document).ready(function() {
    google.maps.event.addDomListener(window, 'load', initMap);

    jQuery("#provinces").change(function() {
        if (jQuery(this).val() != "España") {
            centerAddress(jQuery(this).val() + ", España");
            map.setZoom(7);
        } else {
            centerAddress(jQuery(this).val());
            map.setZoom(4);
        }
    });
});

var map;
var geocoder;

function initMap() {

    var location = new google.maps.LatLng(35.377265200018556, -3.471493972093127);

    var mapCanvas = document.getElementById('map');
    var mapOptions = {
        center: location,
        zoom: 4,
        panControl: false,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    geocoder = new google.maps.Geocoder();
    map = new google.maps.Map(mapCanvas, mapOptions);
    google.maps.event.addListener(map, 'zoom_changed', function() {
        if (map.getZoom() < 2) map.setZoom(2);
    });
    var styles = [
        {
            "elementType": "geometry",
            "stylers": [
                {
                    "color": "#1d2c4d"
                }
            ]
        },
        {
            "elementType": "labels.text.fill",
            "stylers": [
                {
                    "color": "#8ec3b9"
                }
            ]
        },
        {
            "elementType": "labels.text.stroke",
            "stylers": [
                {
                    "color": "#1a3646"
                }
            ]
        },
        {
            "featureType": "administrative.country",
            "elementType": "geometry.stroke",
            "stylers": [
                {
                    "color": "#4b6878"
                }
            ]
        },
        {
            "featureType": "administrative.land_parcel",
            "elementType": "labels.text.fill",
            "stylers": [
                {
                    "color": "#64779e"
                }
            ]
        },
        {
            "featureType": "administrative.province",
            "elementType": "geometry.stroke",
            "stylers": [
                {
                    "color": "#4b6878"
                }
            ]
        },
        {
            "featureType": "landscape.man_made",
            "elementType": "geometry.stroke",
            "stylers": [
                {
                    "color": "#334e87"
                }
            ]
        },
        {
            "featureType": "landscape.natural",
            "elementType": "geometry",
            "stylers": [
                {
                    "color": "#023e58"
                }
            ]
        },
        {
            "featureType": "poi",
            "elementType": "geometry",
            "stylers": [
                {
                    "color": "#283d6a"
                }
            ]
        },
        {
            "featureType": "poi",
            "elementType": "labels.text.fill",
            "stylers": [
                {
                    "color": "#6f9ba5"
                }
            ]
        },
        {
            "featureType": "poi",
            "elementType": "labels.text.stroke",
            "stylers": [
                {
                    "color": "#1d2c4d"
                }
            ]
        },
        {
            "featureType": "poi.park",
            "elementType": "geometry.fill",
            "stylers": [
                {
                    "color": "#023e58"
                }
            ]
        },
        {
            "featureType": "poi.park",
            "elementType": "labels.text.fill",
            "stylers": [
                {
                    "color": "#3C7680"
                }
            ]
        },
        {
            "featureType": "road",
            "elementType": "geometry",
            "stylers": [
                {
                    "color": "#304a7d"
                }
            ]
        },
        {
            "featureType": "road",
            "elementType": "labels.text.fill",
            "stylers": [
                {
                    "color": "#98a5be"
                }
            ]
        },
        {
            "featureType": "road",
            "elementType": "labels.text.stroke",
            "stylers": [
                {
                    "color": "#1d2c4d"
                }
            ]
        },
        {
            "featureType": "road.highway",
            "elementType": "geometry",
            "stylers": [
                {
                    "color": "#2c6675"
                }
            ]
        },
        {
            "featureType": "road.highway",
            "elementType": "geometry.stroke",
            "stylers": [
                {
                    "color": "#255763"
                }
            ]
        },
        {
            "featureType": "road.highway",
            "elementType": "labels.text.fill",
            "stylers": [
                {
                    "color": "#b0d5ce"
                }
            ]
        },
        {
            "featureType": "road.highway",
            "elementType": "labels.text.stroke",
            "stylers": [
                {
                    "color": "#023e58"
                }
            ]
        },
        {
            "featureType": "transit",
            "elementType": "labels.text.fill",
            "stylers": [
                {
                    "color": "#98a5be"
                }
            ]
        },
        {
            "featureType": "transit",
            "elementType": "labels.text.stroke",
            "stylers": [
                {
                    "color": "#1d2c4d"
                }
            ]
        },
        {
            "featureType": "transit.line",
            "elementType": "geometry.fill",
            "stylers": [
                {
                    "color": "#283d6a"
                }
            ]
        },
        {
            "featureType": "transit.station",
            "elementType": "geometry",
            "stylers": [
                {
                    "color": "#3a4762"
                }
            ]
        },
        {
            "featureType": "water",
            "elementType": "geometry",
            "stylers": [
                {
                    "color": "#0e1626"
                }
            ]
        },
        {
            "featureType": "water",
            "elementType": "labels.text.fill",
            "stylers": [
                {
                    "color": "#4e6d70"
                }
            ]
        }
    ];

    map.set('styles', styles);

    let offices = [{"name":"Zona Noroeste - Galicia/Asturias","address":"Rúa Uruguay, 18, 1º Oficina 4","zip":"36201","city":"Vigo, Pontevedra","email":"vigo.info@previsionmallorquina.com","phone":"986221659","lat":"42.2360656","long":"-8.7199046"},{"name":"Zona Centro - Madrid/Castillas","address":"Calle de Goya, 59, 1º Oficina 1","zip":"28001","city":"Madrid","email":"madrid.info@previsionmallorquina.com","phone":"914315654","lat":"40.4252393","long":"-3.6831346"},{"name":"Zona Catalunya","address":"Carrer d'Aribau, 168-170, entresuelo 1a","zip":"08036","city":"Barcelona","email":"cat.info@previsionmallorquina.com","phone":"932440850","lat":"41.3929452","long":"2.1513784"},{"name":"Zona Aragón/Navarra/Soria","address":"Pl. de Miguel Salamero, 12, entresuelo A","zip":"50004","city":"Zaragoza","email":"zaragoza.info@previsionmallorquina.com","phone":"976213637","lat":"41.6527258","long":"-0.8864166"},{"name":"Zona Norte - Euskadi-Burgos/Rioja-Cantabria","address":"Paseo Campo de Volantín. 7 exterior, Departamento 2","zip":"48007","city":"Bilbao","email":"bilbao.info@previsionmallorquina.com","phone":"944132630","lat":"43.2648933","long":"-2.9270302"},{"name":"Zona Levante - Cdad. Valenciana/Balears","address":"Edificio Mozart, Passeig de l'Albereda, 34, 2º Despacho C","zip":"46023","city":"Valencia","email":"valencia.info@previsionmallorquina.com","phone":"963374048","lat":"39.4668992","long":"-0.3618643"},{"name":"Zona Sur - Andalucía/Extremadura","address":"Edificio Cristal, C. Luis Montoto, 107, 3º F y G","zip":"41007","city":"Sevilla","email":"sevilla.info@previsionmallorquina.com","phone":"954571013","lat":"37.3860354","long":"-5.9737243"},{"name":"Zona Canarias - Sta. Cruz de Tenerife","address":"C. San Clemente, 24, 1º D","zip":"38002","city":"El Pilar, Santa Cruz de Tenerife","email":"tenerife.info@previsionmallorquina.com","phone":"922244076","lat":"28.4697488","long":"-16.2537727"}];
    var markers = [];

    let icon = {
        url: "/img/poi.png",
        // url: "https://www.previsionmallorquina.com/wp-content/themes/linx-child/assets/images/markerclusterer/poi.png",
        // scaledSize: new google.maps.Size(31, 41), // scaled size
    };
    jQuery.each(offices, function(i, obj) {
        var location = new google.maps.LatLng(obj.lat, obj.long);
        var marker = new google.maps.Marker({
            position: location,
            map: map,
            icon: icon
        });
        markers.push(marker);

        var contentString = '<div class="info-window">' +
            '<h5>' + obj.name + '</h5>' +
            '<div class="info-content">' +
            '<p>' + obj.address + '</p>' +
            '<p>' + obj.zip + ' ' + obj.city + '</p>' +
            '<p>' + obj.phone + '</p>' +
            '<p class="small">' + obj.email + '</p>' +
            '</div>' +
            '</div>';

        var infowindow = new google.maps.InfoWindow({
            content: contentString,
            maxWidth: 300
        });

        marker.addListener('click', function () {
            infowindow.open(map, marker);
        });


    });

    var markerCluster = new MarkerClusterer(map, markers,
        {imagePath: '/img/m'});
    // {imagePath: 'https://www.previsionmallorquina.com/wp-content/themes/linx-child/assets/images/markerclusterer/m'});
}

function centerAddress(address) {
    geocoder.geocode( { 'address': address}, function(results, status) {
        if (status == 'OK') {
            map.setCenter(results[0].geometry.location);
        } else {
            alert('Geocode was not successful for the following reason: ' + status);
        }
    });
}
