var wpbdp = window.wpbdp || {};

(function($) {
    var googlemaps = wpbdp.googlemaps = wpbdp.googlemaps || {};
    googlemaps.listeners = { 'map_created': [], 'map_rendered': [] };

    googlemaps.Map = function( htmlID, settings ) {
        this.MAP_TYPES = {
            'roadmap': google.maps.MapTypeId.ROADMAP,
            'satellite': google.maps.MapTypeId.SATELLITE,
            'hybrid': google.maps.MapTypeId.HYBRID,
            'terrain': google.maps.MapTypeId.TERRAIN
        };

        this.$map = $( '#' + htmlID );
        this.locations = [];
        this.settings = settings;
        this.settings.removeEmpty = true;
        this.bounds = new google.maps.LatLngBounds();
        
        this.GoogleMap = new google.maps.Map( this.$map[0], { mapTypeId: this.MAP_TYPES[ this.settings.map_type ] } );
        this.infoWindow = new google.maps.InfoWindow();        
        this.oms = new OverlappingMarkerSpiderfier( this.GoogleMap,
                                                    { markersWontMove: true,
                                                      markersWontHide: true,
                                                      keepSpiderfied: true } );

        for ( i = 0; i < googlemaps.listeners['map_created'].length; i++ )
            googlemaps.listeners['map_created'][i]( this );

    };

    $.extend( googlemaps.Map.prototype, {
        _addMarker: function( place ) {
            if ( 'undefined' === typeof( place ) || ! place )
                return;

            if ( 'undefined' === typeof( place.geolocation) || ! place.geolocation ||
                 'undefined' === typeof( place.geolocation.lat ) || ! place.geolocation.lat ||
                 'undefined' === typeof( place.geolocation.lng ) || ! place.geolocation.lng )
                return;

            var position = new google.maps.LatLng( place.geolocation.lat, place.geolocation.lng );
            this.bounds.extend( position );

            var marker = new google.maps.Marker({
                map: this.GoogleMap,
                position: position,
                animation: this.settings.animate_markers ? google.maps.Animation.DROP : null
            });
            marker.descriptionHTML = '<small><a href="' + place.url + '"><b>' + place.title + '</b></a><br />' + place.content.replace( "\n", "<br />" ) + '</small>';
            this.oms.addMarker( marker );
        },

        setLocations: function( locations ) {
            this.locations = locations;
        },

        fitContainer: function(stretch, enlarge) {
            if ( ! this.settings.auto_resize || "auto" === this.settings.map_size )
                return;

            var parent_width = this.$map.parent().innerWidth();
            var current_width = this.$map.outerWidth();

            if ( parent_width < current_width ) {
                this.$map.width( parent_width );
            } else if ( parent_width >= this.orig_width ) {
                this.$map.width(map.orig_width);
            }

            google.maps.event.trigger( this.GoogleMap, "resize" );
        },

        render: function() {
            var map = this;
            this.orig_width = this.$map.width();

            if ( 'top' == this.settings.position ) {
                this.$map.prependTo( $( 'div.listings' ) );
            } else {
                if ( $( 'div.listings .wpbdp-pagination' ).length > 0 ) {
                    this.$map.insertBefore( $( 'div.listings .wpbdp-pagination' ) );
                }
            }

            // Add markers to map.
            if ( this.locations ) {
                for( var i = 0; i < this.locations.length; i++ ) {
                    this._addMarker( this.locations[i] );
                }
            }

            this.oms.addListener( 'click', function( marker, event ) {
                map.infoWindow.setContent( marker.descriptionHTML );
                map.infoWindow.open( map.GoogleMap, marker );
            });

            this.oms.addListener( 'spiderfy' , function( markers ) {
              map.infoWindow.close();
            });

            for ( var i = 0; i < googlemaps.listeners['map_rendered'].length; i++ )
                googlemaps.listeners['map_rendered'][i]( map );

            google.maps.event.addListenerOnce( this.GoogleMap, 'idle', function() {
                if ( map.settings.removeEmpty && ! map.locations )
                    map.$map.remove();

                if ( map.locations.length == 1 ) {
                    map.GoogleMap.setZoom( 15 );
                } else {
                    map.GoogleMap.fitBounds( map.bounds );
                }

                map.GoogleMap.setCenter( map.bounds.getCenter() );
            });

            $(window).resize(function() {
                map.fitContainer( true, false );
            });

            map.fitContainer( true, false );
        }
    });

})(jQuery);
