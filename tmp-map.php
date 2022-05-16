<script src='https://api.mapbox.com/mapbox-gl-js/v1.7.0/mapbox-gl.js'></script>
<link href='https://api.mapbox.com/mapbox-gl-js/v1.7.0/mapbox-gl.css' rel='stylesheet' />

<div id='map'></div>
        <script>
        mapboxgl.accessToken = 'pk.eyJ1Ijoia25pY2siLCJhIjoiY2tjYm53MHh6MjRpbDMwcXB0bHhiOWJyYSJ9.VKDZBoyMTi6WeHcP5tla5w';
        var map = new mapboxgl.Map({
            container: 'map',
            style: 'mapbox://styles/knick/ck99xe0930rg91iobpck77l5o',
            center: [12.467, 52.281],
            zoom: 6,
			minZoom: 5.5
		});
		var nav = new mapboxgl.NavigationControl({
			showCompass: false
		}
		);
		map.addControl(nav, 'bottom-right');

		
		// get marker json
		var jsonPath = "<?php echo WP_PLIGIN_DIR ?>/custom-project-map/assets/data.json";
		fetch (jsonPath)
		.then (function (response) {
			return response.json();
		}).then (function (data) {
			geojsonTest = data;
			addMarkersToMap(geojsonTest);
		}).catch (function (error) {
			console.log ("error: " + error);
		});

		// add markers to map
		function addMarkersToMap(geojson) {
			geojson.features.forEach(function (marker) {
				//console.log("new marker added");

				// create a HTML element for each feature
				var el = document.createElement('div');
				el.className = 'marker';
				el.classList.add("hidden");

				function hasPage(){
					if ( marker.properties.haspage == 1){
						//console.log("no page");
						//open popup
						new mapboxgl.Marker(el)
						.setLngLat(marker.geometry.coordinates)
						// add popups
						.setPopup(new mapboxgl.Popup({ offset: 45 }) 
						.setHTML('<h3>' + marker.properties.title + '</h3><p>' + marker.properties.description + '</p><a class="btn-arrow" href="' + marker.properties.link + '" target="_blank">Mehr</a>'))
						.addTo(map);
					} else {
						//console.log("no page");
						//open popup
						new mapboxgl.Marker(el)
						.setLngLat(marker.geometry.coordinates)
						// add popups
						.setPopup(new mapboxgl.Popup({ offset: 45 }) 
						.setHTML('<h3>' + marker.properties.title + '</h3><p>' + marker.properties.description + '</p>'))
						.addTo(map);
					}
				}

				// make a marker for each feature and add to the map
				if (marker.properties.filterby == 'group') {
					el.classList.add("marker-group");
					hasPage();
				}
				else if (marker.properties.filterby == 'location') {
					el.classList.add("marker-location");
					hasPage();
				}
				else if (marker.properties.filterby == 'person') {
					el.classList.add("marker-person");
					hasPage();
				}
				else if (marker.properties.filterby == 'place') {
					el.classList.add("marker-place");
					hasPage();
				}

			});
		}

		// hide marker on zoom
		map.on('zoomend', function () {
		var marker = document.getElementsByClassName('marker');
		var staedte = document.getElementsByClassName('city');
		var i;

		
		// weit weg
		if (map.getZoom() < 10) {
			//console.log("weit weg");

			enableBtn();
			document.getElementById("btn-marker-group").classList.remove("checked");
			document.getElementById("btn-marker-person").classList.remove("checked");
			document.getElementById("btn-marker-place").classList.remove("checked");
			document.getElementById("btn-marker-location").classList.remove("checked");

			for (i = 0; i < staedte.length; i++) {
			staedte[i].classList.remove("hidden");
			staedte[i].classList.add("visible");
			}
			for (i = 0; i < marker.length; i++) {
			marker[i].classList.remove("visible");
			marker[i].classList.add("hidden");
			}
		// nah dran
		} else {
			//console.log("nah dran");
			hideMarker(marker);

			document.getElementById("btn-marker-group").classList.add("checked");
			document.getElementById("btn-marker-person").classList.add("checked");
			document.getElementById("btn-marker-place").classList.add("checked");
			document.getElementById("btn-marker-location").classList.add("checked");

			enableBtn();
			for (i = 0; i < staedte.length; i++) {
			staedte[i].classList.remove("visible");
			staedte[i].classList.add("hidden");
			}
		}
		});

		function enableBtn(){
			var btn = document.getElementsByClassName('btn-check-map');
			for (i = 0; i < btn.length; i++) {
				btn[i].disabled = false;
			}
		}
			
		function hideMarker(marker){
			//console.log("funktion läuft");
			//console.log(marker);
			for (i = 0; i < marker.length; i++) {

				if (document.getElementById("btn-marker-group").classList.contains("checked")){
					if (marker[i].classList.contains("marker-group")) {
						marker[i].classList.remove("hidden");
						marker[i].classList.add("visible");
					}
				}
				if (document.getElementById("btn-marker-person").classList.contains("checked")){
					if (marker[i].classList.contains("marker-person")) {
						marker[i].classList.remove("hidden");
						marker[i].classList.add("visible");
					}
				}
				if (document.getElementById("btn-marker-place").classList.contains("checked")){
					if (marker[i].classList.contains("marker-place")) {
						marker[i].classList.remove("hidden");
						marker[i].classList.add("visible");
					}
				}
				if (document.getElementById("btn-marker-location").classList.contains("checked")){
					if (marker[i].classList.contains("marker-location")) {
						marker[i].classList.remove("hidden");
						marker[i].classList.add("visible");
					}
				}

			}
		}

		// toggle marker by type
		function toggleVisibility(buttonName) {
  			// toogle class disabled
			var markerType = 'marker-' + buttonName;
			var markerElement = document.getElementsByClassName(markerType);
			var i;

			//if (map.getZoom() >= 10) {
				for (i = 0; i < markerElement.length; i++) {
					if (markerElement[i].classList.contains("visible")) {
						markerElement[i].classList.remove("visible");
						markerElement[i].classList.add("hidden");
					}
					else {
						markerElement[i].classList.remove("hidden");
						markerElement[i].classList.add("visible");
					}
				}
			//}
		};

		// toogle DDR border
		function toggleDDR() {
			var layer = 'ddr-fill';
		
			//console.log(layer);

			var visibility = map.getLayoutProperty(layer, 'visibility');
			if (!visibility){
				visibility = "visible";
			}
			//console.log(visibility);
			
			if (visibility != 'visible') {
				map.setLayoutProperty(layer, 'visibility', 'visible');
				
			} else {
				map.setLayoutProperty(layer, 'visibility', 'none');
			}
		};


        </script>
