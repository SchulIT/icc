document.addEventListener('DOMContentLoaded', function() {
    function deleteOption() {
        console.log(this);
        let selector = this.getAttribute('data-selector') ?? '.form-group';
        this.closest(selector).remove();
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
        collectionHolder.setAttribute('data-index', parseInt(index) + 1);

        // Display the form in the page in an li
        let newForm = htmlToElement(newFormHtml);

        collectionHolder.appendChild(newForm);

        newForm.querySelectorAll('.btn-delete').forEach(function(el) {
            el.removeEventListener('click', deleteOption, false);
            el.addEventListener('click', deleteOption);
        });
    }

    function onButtonAddClick(event) {
        // prevent the link from creating a "#" on the URL
        event.preventDefault();

        let collectionHandlerId = this.getAttribute('data-collection');
        let collectionHandler = document.querySelector('div[data-collection=' + collectionHandlerId + ']');

        // add a new tag form (see next code block)
        addOption(collectionHandler);
    }

    document.querySelectorAll('div[data-collection]').forEach(function(el) {
        //el.addEventListener('click', deleteOption);

        // count the current form inputs we have (e.g. 2), use that as the new
        // index when inserting a new item (e.g. 2)
        el.setAttribute('data-index', el.childNodes.length);

        el.querySelectorAll('.btn-delete').forEach(function(btn) {
            btn.addEventListener('click', deleteOption);
        });
    });

    document.querySelectorAll('button[data-collection]').forEach(function(el) {
        el.removeEventListener('click', onButtonAddClick, false);
        el.addEventListener('click', onButtonAddClick);
    });
});

