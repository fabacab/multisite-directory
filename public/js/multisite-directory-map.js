/**
 * @todo Make this testable.
 */
(function () {
    'use strict';
    jQuery(document).ready(function () {
        var maps = jQuery('.site-directory-map');
        maps.each(function () {
            // find the mappable data sent via wp_localize_script(),
            var data = window['multisite_directory_' + jQuery(this).attr('id').replace(/-/g, '_')];

            var map = L.map(this).setView({'lat': 40.730608477796636, 'lng': -73.99017333984375}, 10);
            map.attributionControl.setPrefix('');
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                'maxZoom': 19
            }).addTo(map);

            var bounds = L.latLngBounds([0,0]); // TODO: Is there a better way to initialize this?

            L.geoJson(data, {
                'pointToLayer': function (feature, latlng) {
                    bounds.extend(latlng);
                    var m = L.marker(latlng);
                    var el = jQuery();
                    if (feature.properties.sites.length) {
                        var el = jQuery('<ul>');
                        jQuery(feature.properties.sites).each(function () {
                            var li = jQuery('<li>');
                            var a = jQuery('<a>');
                            if (this.meta.sitelogo) {
                                var img = jQuery(this.meta.sitelogo);
                                li.append(img);
                            }
                            a.attr('href', this.meta.siteurl);
                            a.text(this.post_title);
                            li.append(a);
                            el.append(li);
                        });
                    } else {
                        el = jQuery('<p>' + multisite_directory_map_ui_strings.i18n_no_sites_at_location + '</p>');
                    }
                    m.bindPopup(el.get(0));
                    return m;
                }
            }).addTo(map);

            map.fitBounds(bounds);
        });
    });
})();
