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
        let spinnerElementSelector = el.getAttribute('data-spinner');
        let spinnerElement = null;
        if(spinnerElementSelector !== null) {
            spinnerElement = document.querySelector(spinnerElementSelector);
        }
        let lastScrollTop = 0;
        let seconds = interval;

        setInterval(function() {
            seconds--;

            if(spinnerElement !== null && seconds > 0) {
                spinnerElement.textContent = "" + seconds;
            }

            if(seconds > 0) {
                return;
            }

            let height = el.offsetHeight;
            let offset = el.scrollHeight;

            let currentScrollTop = el.scrollTop;
            let newScrollTop = currentScrollTop + height - 100;

            if(newScrollTop === lastScrollTop) {
                newScrollTop = 0;
            }

            if(newScrollTop >= offset - 100) {
                newScrollTop = 0;
            }

            lastScrollTop = newScrollTop;
            seconds = interval;
            if(spinnerElement !== null && seconds > 0) {
                spinnerElement.textContent = "" + seconds;
            }

            el.scrollTo({
                top: newScrollTop,
                behavior: 'smooth'
            });
        }, 1000);
    });
});