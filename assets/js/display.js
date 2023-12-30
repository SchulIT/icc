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

function nextPage(column) {
    const currentPage = parseInt(column.getAttribute('data-page'));
    const numberOfPages = parseInt(column.getAttribute('data-pages'));

    let nextPage = currentPage + 1;

    if(nextPage > numberOfPages) {
        nextPage = 1;
    }

    column.setAttribute('data-page', nextPage);
}

function updateTable(column) {
    const currentPage = parseInt(column.getAttribute('data-page'));
    const itemsPerPage = parseInt(column.getAttribute('data-rows'));

    const table = column.querySelector(column.getAttribute('data-table'));

    if(table === null) {
        console.log('data-table not found');
        return;
    }

    const indicator = column.querySelector(column.getAttribute('data-indicator'));

    if(indicator !== null) {
        indicator.innerHTML = 'Seite ' + currentPage + ' / ' + column.getAttribute('data-pages');
    }

    const rows = table.querySelectorAll('tbody > tr');
    const minIdx = (currentPage - 1) * itemsPerPage;
    const maxIdx = minIdx + itemsPerPage - 1;

    for(let idx = 0; idx < rows.length; idx++) {
        if(idx >= minIdx && idx <= maxIdx) {
            rows[idx].classList.remove('d-none');
        } else {
            rows[idx].classList.add('d-none');
        }
    }
}

function enableOrDisableScrollers(column) {
    for(const scroller of column.querySelectorAll('.scroller')) {
        let inner = scroller.querySelector('.scroller__inner');
        let original = scroller.querySelector('.scroller__original');

        let scrollerStyle = window.getComputedStyle(scroller);
        let originalStyle = window.getComputedStyle(original);

        if(parseInt(originalStyle.width) > parseInt(scrollerStyle.width)) {
            scroller.setAttribute('data-animate', "true");
        } else {
            scroller.setAttribute('data-animate', "false");
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const columns = document.querySelectorAll('[data-table]');

    for(const column of columns) {
        const table = column.querySelector(column.getAttribute('data-table'));
        const scrollIntervall = parseInt(column.getAttribute('data-intervall'));

        if(table === null) {
            console.log('data-table not found');
            continue;
        }

        if(scrollIntervall <= 0) {
            console.error('data-intervall must be greater than 0');
            continue;
        }

        const rows = table.querySelectorAll('tbody > tr');

        // Wrap all cells
        for(const row of rows) {
            for(const cell of row.childNodes) {
                let innerHtml = cell.innerHTML;
                cell.innerHTML = '<div class="scroller"><div class="scroller__inner"><div class="scroller__original">' + innerHtml + '</div><div class="scroller__duplicate">' + innerHtml + '</div></div></div>';
            }
        }

        let containerRect = column.closest('.container-fluid').getBoundingClientRect();

        let visibleCount = 0;

        // Determine row count
        for(const row of rows) {
            let rowRect = row.getBoundingClientRect();
            if(rowRect.top + rowRect.height > containerRect.height) {
                break; // Element does not fit entirely -> we have our row count
            }
            visibleCount++;
        }

        console.log('Determined ' + visibleCount + ' item(s) per page');

        const pages = Math.ceil(rows.length / visibleCount);
        column.setAttribute('data-rows', visibleCount);

        column.setAttribute('data-page', 1);
        column.setAttribute('data-pages', pages);

        enableOrDisableScrollers(column);

        updateTable(column);

        setInterval(_ => {
            nextPage(column);
            updateTable(column);
        }, scrollIntervall * 1000);
    }
});