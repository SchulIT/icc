let bsCustomFileInput = require('bs-custom-file-input');

document.addEventListener('DOMContentLoaded', function() {
    function deleteOption() {
        this.closest('.form-inline').remove();
    }

    function htmlToElement(html) {
        let template = document.createElement('template');
        html = html.trim();

        template.innerHTML = html;
        return template.content.firstChild;
    }

    function addOption(collectionHolder) {
        // Get the data-prototype explained earlier
        let prototype = collectionHolder.getAttribute('data-prototype');

        // get the new index
        let index = collectionHolder.getAttribute('data-index');

        // Replace '__name__' in the prototype's HTML to
        // instead be a number based on how many items we have
        let newFormHtml = prototype.replace(/__name__/g, index);

        // increase the index with one for the next item
        collectionHolder.setAttribute('index', index + 1);

        // Display the form in the page in an li
        let newForm = htmlToElement(newFormHtml);

        collectionHolder.appendChild(newForm);

        newForm.querySelectorAll('.btn-delete').forEach(function(el) {
            el.addEventListener('click', deleteOption);
        });

        bsCustomFileInput.destroy();
        bsCustomFileInput.init();
    }

    function onButtonAddClick(event) {
        // prevent the link from creating a "#" on the URL
        event.preventDefault();

        // add a new tag form (see next code block)
        addOption(collectionHolder);
    }

    // Get the ul that holds the collection of tags
    let collectionHolder = document.querySelector('.attachments');

    collectionHolder.querySelectorAll('.btn-delete').forEach(function (el) {
        el.addEventListener('click', deleteOption);
    });

    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    collectionHolder.setAttribute('data-index', collectionHolder.querySelectorAll('input').length);

    let button = collectionHolder
        .parentNode.parentNode.querySelector('button.btn-add');

    button.removeEventListener('click', onButtonAddClick, false);
    button.addEventListener('click', onButtonAddClick);

});

