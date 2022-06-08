function toggleActiveState(element) {
    var listId = element.dataset.id;
    var content = element.querySelector('.project-content');
    var theMarker = document.querySelector('[data-id="' + listId + '"]');
    theMarker.classList.toggle('marker--active');
    content.classList.toggle('project-content--disabled');
}

function toggleActiveStateList(element) {
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

$(document).ready(function() {

    // only small screens
    if ($(window).width() <= 800) {
        // show menu on swipe to right
        $(document).on('swiperight', function(e) {
            e.preventDefault();
            $('#project-list-container').animate({
                left: '0'
            });
        });  // hide menu on swipe to left
        $(document).on('swipeleft', function(e) {
            e.preventDefault();
            $('#project-list-container').animate({
                left: '-100%'
            });
        });
    }

});