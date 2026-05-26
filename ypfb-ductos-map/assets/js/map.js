(function($) {
    'use strict';

    var map;
    var marker;
    var ductosLayerGroup;
    var selectedPoint = null;

    // Initialize map when document is ready
    $(document).ready(function() {
        if ($('#ypfb-ductos-map').length > 0) {
            initMap();
        }
    });

    function initMap() {
        // Default center (Bolivia - adjust as needed)
        var defaultLat = -17.3895;
        var defaultLng = -66.1568;

        // Create map
        map = L.map('ypfb-ductos-map').setView([defaultLat, defaultLng], 6);

        // Add OpenStreetMap tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 18
        }).addTo(map);

        // Create feature group for ductos
        ductosLayerGroup = L.layerGroup().addTo(map);

        // Add click event to map
        map.on('click', function(e) {
            handleMapClick(e);
        });

        // Add instructions overlay
        addInstructions();
    }

    function addInstructions() {
        var instructions = L.control({position: 'topright'});
        
        instructions.onAdd = function(map) {
            var div = L.DomUtil.create('div', 'map-instructions');
            div.innerHTML = '<strong>Instrucciones:</strong><br>Haga clic en el mapa para seleccionar un punto y consultar ductos cercanos.';
            return div;
        };
        
        instructions.addTo(map);
    }

    function handleMapClick(e) {
        selectedPoint = e.latlng;

        // Remove existing marker if any
        if (marker) {
            map.removeLayer(marker);
        }

        // Add new marker
        marker = L.marker(selectedPoint).addTo(map)
            .bindPopup('Punto seleccionado:<br>Lat: ' + selectedPoint.lat.toFixed(6) + '<br>Lng: ' + selectedPoint.lng.toFixed(6))
            .openPopup();

        // Query ductos
        queryDuctos(selectedPoint);
    }

    function queryDuctos(point) {
        // Show loading state
        $('#ypfb-ductos-info').removeClass('hidden');
        $('#ypfb-ductos-message').html('<div class="loading-spinner"></div><p>Consultando ductos cercanos...</p>');
        $('#ypfb-ductos-list').empty();

        // Convert lat/lng to the coordinate system expected by the API
        // Note: You may need to adjust this based on your spatial reference system
        var coords = convertToProjectedCoords(point.lat, point.lng);

        $.ajax({
            url: ydm_ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'ypfb_get_ductos',
                nonce: ydm_ajax_object.nonce,
                x: coords.x,
                y: coords.y,
                spatial_ref: ydm_ajax_object.spatial_ref || ''
            },
            success: function(response) {
                if (response.success) {
                    processDuctosResponse(response.data);
                } else {
                    showError(response.data.message || 'Error al consultar los ductos');
                }
            },
            error: function(xhr, status, error) {
                showError('Error de conexión: ' + error);
            }
        });
    }

    function processDuctosResponse(data) {
        var features = [];
        
        // Handle different response structures
        if (data.features && Array.isArray(data.features)) {
            features = data.features;
        } else if (data.result && data.result.features) {
            features = data.result.features;
        }

        var contactEmail = ydm_ajax_object.contact_email || 'contacto@ypfbtransporte.com';
        var message1 = ydm_ajax_object.message1 || '';
        var message2 = ydm_ajax_object.message2 || '';

        if (features.length > 0) {
            // Clear previous layers
            ductosLayerGroup.clearLayers();

            var ductosNames = [];
            var ductosListHtml = '<h4>Ductos encontrados:</h4>';

            features.forEach(function(feature, index) {
                var attrs = feature.attributes || {};
                var name = attrs.StationSeriesName || attrs.LineDescription || 'Ducto ' + (index + 1);
                var description = attrs.LineDescription || '';
                var objectid = attrs.OBJECTID || '';

                ductosNames.push(name);

                ductosListHtml += '<div class="ducto-item" data-index="' + index + '">';
                ductosListHtml += '<div class="ducto-name">' + escapeHtml(name) + '</div>';
                if (description) {
                    ductosListHtml += '<div class="ducto-description">' + escapeHtml(description) + '</div>';
                }
                ductosListHtml += '</div>';

                // If geometry is available, draw it on the map
                if (feature.geometry) {
                    drawDuctoOnMap(feature);
                } else {
                    // Fetch vertices for this ducto
                    fetchDuctoVertices(objectid || name);
                }
            });

            // Build message with ductos list
            var message = message1 || 'En la ubicación seleccionada se tienen los siguientes Ductos cercanos: "{DUCTOS}", puede porfavor ponerse en contacto con la siguiente dirección de correo para re-confirmar y seguir instrucciones: {EMAIL}.\n*Los datos entregados no son finales, deben ser reconfirmados por personal de YPFB Transporte S.A.';
            message = message.replace('{DUCTOS}', ductosNames.join(', '));
            message = message.replace('{EMAIL}', contactEmail);

            $('#ypfb-ductos-message').html(message.replace(/\n/g, '<br>'));
            $('#ypfb-ductos-list').html(ductosListHtml);

            // Add click events to ducto items
            $('.ducto-item').on('click', function() {
                var index = $(this).data('index');
                if (features[index] && features[index].geometry) {
                    highlightDucto(features[index]);
                }
            });

        } else {
            // No ductos found
            var message = message2 || 'En la ubicación seleccionada no se tienen registros de Ductos cercanos. Sin embargo, puede por favor ponerse en contacto con la siguiente dirección de correo para re-confirmar y seguir instrucciones: {EMAIL}.\n*Los datos entregados no son finales, deben ser reconfirmados por personal de YPFB Transporte S.A.';
            message = message.replace('{EMAIL}', contactEmail);

            $('#ypfb-ductos-message').html(message.replace(/\n/g, '<br>'));
            $('#ypfb-ductos-list').html('<p>No se encontraron ductos en esta ubicación.</p>');
        }
    }

    function fetchDuctoVertices(codigoDucto) {
        $.ajax({
            url: ydm_ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'ypfb_get_ducto_vertices',
                nonce: ydm_ajax_object.nonce,
                codigo_ducto: codigoDucto
            },
            success: function(response) {
                if (response.success) {
                    drawDuctoOnMap(response.data);
                }
            },
            error: function(xhr, status, error) {
                console.log('Error fetching vertices:', error);
            }
        });
    }

    function drawDuctoOnMap(feature) {
        if (!feature.geometry) {
            return;
        }

        var paths = feature.geometry.paths;
        
        if (paths && Array.isArray(paths)) {
            paths.forEach(function(path) {
                var latLngs = path.map(function(coord) {
                    // Convert from projected coordinates to lat/lng
                    var converted = convertFromProjectedCoords(coord[0], coord[1]);
                    return [converted.lat, converted.lng];
                });

                var polyline = L.polyline(latLngs, {
                    color: '#ff0000',
                    weight: 4,
                    opacity: 0.7
                });

                polyline.addTo(ductosLayerGroup);
                
                // Add popup with info
                var attrs = feature.attributes || {};
                var popupContent = '<strong>' + escapeHtml(attrs.StationSeriesName || attrs.LineDescription || 'Ducto') + '</strong>';
                if (attrs.LineDescription) {
                    popupContent += '<br>' + escapeHtml(attrs.LineDescription);
                }
                polyline.bindPopup(popupContent);
            });
        }
    }

    function highlightDucto(feature) {
        // Zoom to the ducto
        var paths = feature.geometry.paths;
        if (paths && Array.isArray(paths) && paths.length > 0) {
            var latLngs = paths[0].map(function(coord) {
                var converted = convertFromProjectedCoords(coord[0], coord[1]);
                return [converted.lat, converted.lng];
            });
            
            var bounds = L.latLngBounds(latLngs);
            map.fitBounds(bounds, {padding: [50, 50]});
        }
    }

    function showError(message) {
        $('#ypfb-ductos-message').html('<div class="error-message">' + escapeHtml(message) + '</div>');
        $('#ypfb-ductos-list').empty();
    }

    function escapeHtml(text) {
        if (!text) return '';
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Convert latitude/longitude to projected coordinates
     * This is a placeholder - implement based on your spatial reference system
     * Common systems: EPSG:3857 (Web Mercator), EPSG:4326 (WGS84), or local systems
     */
    function convertToProjectedCoords(lat, lng) {
        // Example conversion to Web Mercator (EPSG:3857)
        // Adjust this function based on the spatial reference required by your API
        
        var x = lng * 20037508.34 / 180;
        var y = Math.log(Math.tan((90 + lat) * Math.PI / 360)) / (Math.PI / 180);
        y = y * 20037508.34 / 180;

        return {
            x: x,
            y: y
        };
    }

    /**
     * Convert projected coordinates to latitude/longitude
     * This is a placeholder - implement based on your spatial reference system
     */
    function convertFromProjectedCoords(x, y) {
        // Example conversion from Web Mercator (EPSG:3857) to WGS84
        // Adjust this function based on the spatial reference required by your API
        
        var lng = (x / 20037508.34) * 180;
        var lat = (y / 20037508.34) * 180;
        lat = 180 / Math.PI * (2 * Math.atan(Math.exp(lat * Math.PI / 180)) - Math.PI / 2);

        return {
            lat: lat,
            lng: lng
        };
    }

})(jQuery);
