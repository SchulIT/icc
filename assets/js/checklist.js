import TomSelect from "tom-select/dist/js/tom-select.complete";

for(let $element of document.querySelectorAll('[data-checklist-target]')) {
    let formSelector = $element.getAttribute('data-checklist-target');
    let plugins = [  ];

    if($element.multiple) {
        plugins.push( 'remove_button' );
    }

    let options = [ ];

    for(let $checkbox of document.querySelector(formSelector).querySelectorAll('input[type="checkbox"]')) {
        let label = $checkbox.labels[0]?.innerText;

        if(label !== undefined && label.trim() !== '') {
            options.push({
                id: $checkbox.id,
                label: label
            })
        }
    }

    const select = new TomSelect($element, {
        options: options,
        plugins: plugins,
        valueField: 'id',
        labelField: 'label',
        searchField: 'label',
        create: false
    });

    const $button = $element.parentNode.querySelector('button');
    $button?.addEventListener('click', () => {
        let values = select.getValue();

        if(!Array.isArray(values)) {
            values = [ values ];
        }

        for(let id of values) {
            let $checkbox = document.getElementById(id);

            if($checkbox) {
                $checkbox.checked = true;
            }
        }

        select.setValue(null);
    });
}