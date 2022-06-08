function toggleActiveState(thisElement) {
    var element = thisElement;
    var listId = element.dataset.id;
    var content = element.querySelector('.project-content');
    var theMarker = document.querySelector('[data-id="' + listId + '"]');
    theMarker.classList.toggle('marker--active');
    content.classList.toggle('project-content--disabled');
    var projectListContainer = document.getElementById('project-list-container');
    if (projectListContainer.classList.contains("project-list-container--no-scroll")) {
        projectListContainer.classList.remove('project-list-container--no-scroll');
    } else {
        projectListContainer.classList.add('project-list-container--no-scroll');
    }
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
    if (projectListContainer.classList.contains("project-list-container--no-scroll")) {
        projectListContainer.classList.remove('project-list-container--no-scroll');
    } else {
        projectListContainer.classList.add('project-list-container--no-scroll');
    }
}

function toggleActiveStateList(thisElement) {
    var element = thisElement;
    var markerId = element.dataset.id;
    var listItems = document.querySelectorAll('.project-list-item');
    listItems.forEach(function(listItem) {
        var contentToDisable = listItem.querySelectorAll('.project-content');
        contentToDisable.forEach(function(ctd) {
            ctd.classList.add('project-content--disabled');
            var markerToDisable = document.querySelector('.marker[data-id="' + listItem.dataset.id + '"]');
            markerToDisable.classList.remove('marker--active');
        });
    });
    var theListItem = document.querySelector('.project-list-item[data-id="' + markerId + '"]');
    var content = theListItem.querySelector('.project-content');
    element.classList.toggle('marker--active');
    content.classList.toggle('project-content--disabled');
    var projectListContainer = document.getElementById('project-list-container');
    if (projectListContainer.classList.contains("project-list-container--no-scroll")) {
        projectListContainer.classList.remove('project-list-container--no-scroll');
    } else {
        projectListContainer.classList.add('project-list-container--no-scroll');
    }
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