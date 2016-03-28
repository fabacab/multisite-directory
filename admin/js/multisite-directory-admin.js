/**
 * @todo Make this testable.
 */
(function () {
	'use strict';
    jQuery(document).ready(function () {
        var term_geo = jQuery('#term-geo');
        var button = jQuery('.term-geo-wrap a.button');
        var mapmarker;
        var geo;
        if (term_geo.val().length) {
            geo = term_geo.val().split(',');
        } else {
            geo = {'lat': 40.730608477796636, 'lng': -73.99017333984375};
        }
        var mymap = L.map('term-map').setView(geo, 10);
        L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(mymap);
        if (term_geo.val().length) {
            mapmarker = L.marker(geo).addTo(mymap);
        }
        mymap.on('click', function (e) {
            if (mapmarker) {
                mymap.removeLayer(mapmarker);
            }
            mapmarker = L.marker(e.latlng).addTo(mymap);
            term_geo.val(e.latlng.lat + ',' + e.latlng.lng);
        });
        button.on('click', function (e) {
            e.preventDefault();
            term_geo.val('');
            mymap.removeLayer(mapmarker);
        });
    });
})();
