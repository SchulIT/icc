require('../css/display.scss');

let timeSelector = '#time';

function padNumber(number) {
    return ('' + number).padStart(2, '0');
}

setInterval(function() {
    let now = new Date();
    let timeElement = document.querySelector(timeSelector);

    if(timeElement !== null) {
        timeElement.innerHTML = padNumber(now.getHours()) + ':' + padNumber(now.getMinutes());
    }
}, 500);

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-scroll=true]').forEach(function(el) {
        if(el.hasAttribute('data-interval') !== true) {
            console.error('You must specify data-interval (in seconds)');
            return;
        }

        let interval = parseInt(el.getAttribute('data-interval'));

        setInterval(function() {
            let height = el.offsetHeight;
            let maxHeight = el.scrollHeight;

            let currentScrollTop = el.scrollTop;
            let newScrollTop = currentScrollTop + height;

            if(newScrollTop >= maxHeight) {
                newScrollTop = 0;
            }

            el.scrollTo({
                top: newScrollTop,
                behavior: 'smooth'
            });
        }, interval*1000);
    });
});