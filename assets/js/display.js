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