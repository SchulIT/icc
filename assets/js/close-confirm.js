window.onbeforeunload = function(event) {
    return ''
}

document.querySelectorAll('form').forEach(function(el) {
    el.addEventListener('submit', function() {
        window.onbeforeunload = null;
    });
})