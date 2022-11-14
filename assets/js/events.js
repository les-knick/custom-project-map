function toggleActiveState(thisElement) {
    var element = thisElement;
    var listId = element.dataset.id;
    var content = element.querySelector('.project-content');
    var theMarker = document.querySelector('[data-id="' + listId + '"]');
    theMarker.classList.toggle('marker--active');
    content.classList.toggle('project-content--disabled');
    var projectListContainer = document.getElementById('project-list-container');
    if (projectListContainer.classList.contains('project-list-container--no-scroll')) {
        projectListContainer.classList.remove('project-list-container--no-scroll');
        projectListContainer.scrollTop = projectListContainer.dataset.scrolltop;
    } else {
        var scrollPositionProjectListContainer = projectListContainer.scrollTop;
        projectListContainer.dataset.scrolltop = scrollPositionProjectListContainer;
        projectListContainer.classList.add('project-list-container--no-scroll');
    }
    /* if (projectListContainer.classList.contains("project-list-container--no-scroll")) {
        projectListContainer.classList.remove('project-list-container--no-scroll');
    } else {
        projectListContainer.classList.add('project-list-container--no-scroll');
    } */
}

function toggleActiveStateFromContent(thisElement) {
    var element = thisElement;
    element = element.parentNode.parentNode;

    var listId = element.dataset.id;
    var content = element.querySelector('.project-content');
    var theMarker = document.querySelector('[data-id="' + listId + '"]');
    theMarker.classList.toggle('marker--active');
    content.classList.toggle('project-content--disabled');
    var projectListContainer = document.getElementById('project-list-container');
    if (projectListContainer.classList.contains('project-list-container--no-scroll')) {
        projectListContainer.classList.remove('project-list-container--no-scroll');
        projectListContainer.scrollTop = projectListContainer.dataset.scrolltop;
    } else {
        var scrollPositionProjectListContainer = projectListContainer.scrollTop;
        projectListContainer.dataset.scrolltop = scrollPositionProjectListContainer;
        projectListContainer.classList.add('project-list-container--no-scroll');
    }

    /* if (projectListContainer.classList.contains("project-list-container--no-scroll")) {
        projectListContainer.classList.remove('project-list-container--no-scroll');
    } else {
        projectListContainer.classList.add('project-list-container--no-scroll');
    } */
}

function toggleActiveStateList(thisElement) {
    var element = thisElement;
    var markerId = element.dataset.id;
    var listItems = document.querySelectorAll('.project-list-item');
    var projectListContainer = document.getElementById('project-list-container');
    listItems.forEach(function(listItem) {
        var contentToDisable = listItem.querySelector('.project-content');
        var containsClass = contentToDisable.classList.contains('project-content--disabled');
        if (containsClass == true) {

        } else {
            contentToDisable.classList.add('project-content--disabled');
            var markerToDisable = document.querySelector('.marker[data-id="' + listItem.dataset.id + '"]');
            markerToDisable.classList.remove('marker--active');
            projectListContainer.classList.toggle('project-list-container--no-scroll');
        }

    });
    var theListItem = document.querySelector('.project-list-item[data-id="' + markerId + '"]');
    var content = theListItem.querySelector('.project-content');
    element.classList.toggle('marker--active');
    content.classList.toggle('project-content--disabled');
    projectListContainer.classList.add('project-list-container--active');
    if (projectListContainer.classList.contains('project-list-container--no-scroll')) {
        projectListContainer.classList.remove('project-list-container--no-scroll');
        projectListContainer.scrollTop = projectListContainer.dataset.scrolltop;
    } else {
        var scrollPositionProjectListContainer = projectListContainer.scrollTop;
        projectListContainer.dataset.scrolltop = scrollPositionProjectListContainer;
        projectListContainer.classList.add('project-list-container--no-scroll');
    }
    /* if (projectListContainer.classList.contains("project-list-container--no-scroll")) {
        projectListContainer.classList.remove('project-list-container--no-scroll');
    } else {
        projectListContainer.classList.add('project-list-container--no-scroll');
    } */
}

function filterMarker() {
    var markers = document.querySelectorAll('.marker');
    var listItems = document.querySelectorAll('.project-list-item');
    markers.forEach(function(marker) {
        marker.classList.remove('marker--hidden');
        var markerID = marker.dataset.id;
        var inArray = false;
        listItems.forEach(function(listItem) {
            var listItemID = listItem.dataset.id;
            if (markerID == listItemID) {
                inArray = true;
            }
        });
        if (inArray == false) {
            marker.classList.add('marker--hidden');
        }
    });
}

function toggleProjectList() {
    var projectList = document.getElementById('project-list-container');
    projectList.classList.toggle('project-list-container--active');
}

function waitForMapLoaded() {
    if (!map.loaded()) {
        window.setTimeout(waitForMapLoaded, 100);
    } else {
        filterMarker();
    }
}

function waitForMapLoadedToCluster(map) {
    if (!map.loaded()) {
        window.setTimeout(waitForMapLoadedToCluster(map), 100);
    } else {
        clusterMarkers(map);
        toggleMarkersOnZoom(map)
    }
}

function getMarkerCoordinates() {
    var markers = document.querySelectorAll('.marker');
    markers.forEach(marker => {
        // get map coordinates
        marker['lng'] = marker.dataset.lng;
        marker['lat'] = marker.dataset.lat;
        marker['lnglat'] = marker.dataset.lnglat;
        // get screen coordinates
        marker['rect'] = marker.getBoundingClientRect();
        marker['centerx'] = marker['rect'].left + marker.offsetWidth / 2;
        marker['centery'] = marker['rect'].top + marker.offsetHeight / 2;
    });
    return markers;
}


function deleteClusterMarkers() {
    var clusterPointsToDelete = document.querySelectorAll('.cluster-point');
    var markersToReset = document.querySelectorAll('.marker');
    clusterPointsToDelete.forEach(clusterPoint => {
        clusterPoint.remove();
    });
    markersToReset.forEach(marker => {
        marker.dataset.clusterid = 0;
        marker.classList.remove('marker--clustered');
    });
}

function clusterMarkers(map) {
    var clusterPoints = [];
    var clusterCountArray = [];

    // var markerToMarkerOffset = 20;
    var markers = getMarkerCoordinates();
    var clusterIndex = 0;
    var mapContainer = document.getElementById('map-container');
    // init first clusterPoint
    clusterPoints[0] = document.createElement('div');
    clusterPoints[0].dataset.clusterid = clusterIndex;
    clusterPoints[0].classList.add('cluster-point');
    mapContainer.appendChild(clusterPoints[0]);
    clusterIndex++;

    markers.forEach(constMarker => {
        var clusterNumber = checkIfClusterPointAlreadyExists(constMarker, clusterPoints);
        if (clusterNumber) {
            clusterCountArray[clusterNumber]++;
            clusterPoints[clusterNumber].dataset.markercount = clusterCountArray[clusterNumber];
            clusterPoints[clusterNumber].innerHTML = clusterCountArray[clusterNumber];
            // continue
        } else {
            clusterPoints[clusterIndex] = document.createElement('div');
            clusterPoints[clusterIndex].dataset.clusterid = clusterIndex;
            clusterPoints[clusterIndex].classList.add('cluster-point');
            clusterPoints[clusterIndex].dataset.lng = constMarker['lng'];
            clusterPoints[clusterIndex].dataset.lat = constMarker['lat'];
            //clusterPoints[clusterIndex].onclick = "zoomInClusterpoint(this)";
            clusterCountArray[clusterIndex] = 1;
            new mapboxgl.Marker(clusterPoints[clusterIndex])
                .setLngLat([constMarker['lng'], constMarker['lat']])
                .addTo(map);
            clusterIndex++;
        }
        constMarker.classList.add('marker--clustered');
        constMarker.dataset.clusterid = clusterIndex - 1;
    });

    derenderLonelyClusterBoys(clusterPoints);

    clusterPoints.forEach(clusterPoint => {
        clusterPoint.addEventListener('click', function() {
            var zoomCenter = [this.dataset.lng, this.dataset.lat];
            var zoomLevel = map.getZoom() + 2;
            map.flyTo({ center: zoomCenter, zoom: zoomLevel });
        });
    });
}

function checkIfClusterPointAlreadyExists(marker, clusterPoints) {
    var markerToClusterPointOffset = 50;
    var returnHelper = false;
    clusterPoints.forEach(function(clusterPoint, index) {
        clusterPoint['rect'] = clusterPoint.getBoundingClientRect();
        clusterPoint['centerx'] = clusterPoint['rect'].left + clusterPoint.offsetWidth / 2;
        clusterPoint['centery'] = clusterPoint['rect'].top + clusterPoint.offsetHeight / 2;
        // calc pythagoras
        var a = marker['centerx'] - clusterPoint['centerx'];
        var b = marker['centery'] - clusterPoint['centery'];
        var distance = Math.sqrt(a * a + b * b);
        if (distance < markerToClusterPointOffset) {
            returnHelper = index;
            return true;
        }
    });
    return returnHelper;
}

// toggle Markers on Zoom

function toggleMarkersOnZoom(map) {
    var zoomLevel = map.getZoom();
    var mapContainer = document.querySelector('#map-container');
    if (zoomLevel > 9.5) {
        mapContainer.classList.add('high-zoom');
        mapContainer.classList.remove('low-zoom');
    } else {
        mapContainer.classList.remove('high-zoom');
        mapContainer.classList.add('low-zoom');
    }
}

function derenderLonelyClusterBoys(clusterPoints) {
    clusterPoints.forEach(function(clusterPoint, index) {
        if (clusterPoint.dataset.markercount == undefined && index > 0) {
            var markerQuery = ".marker[data-clusterid='" + clusterPoint.dataset.clusterid + "']";
            var lonelyMarker = document.querySelector(markerQuery);
            lonelyMarker.classList.remove('marker--clustered');
            clusterPoint.classList.add('cluster-point--declustered');
        }
    });
}