function toggleActiveState() {
    var listId = this.dataset.id;
    var content = this.querySelector('.project-content');
    var theMarker = document.querySelector('[data-id="' + listId + '"]');
    theMarker.classList.toggle('marker--active');
    content.classList.toggle('project-content--disabled');
}