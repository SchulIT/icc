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
        let height = el.offsetHeight;
        let maxHeight = el.scrollHeight;

        if(el.hasAttribute('data-intervall') !== true) {
            console.error('You must specify data-intervall (in seconds)');
            return;
        }

        let intervall = parseInt(el.getAttribute('data-intervall'));

        setInterval(function() {
            let currentScrollTop = el.scrollTop;
            let newScrollTop = currentScrollTop + height;

            if(newScrollTop >= maxHeight) {
                newScrollTop = 0;
            }

            el.scrollTo({
                top: newScrollTop,
                behavior: 'smooth'
            });
        }, intervall*1000);
    });
});