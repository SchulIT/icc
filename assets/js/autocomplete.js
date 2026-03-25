import TomSelect from "tom-select/dist/js/tom-select.complete";

let $elements = document.querySelectorAll('[data-autocomplete]');

for(let $element of $elements) {
    let endpointUrl = $element.getAttribute('data-autocomplete-url');

    new TomSelect($element, {
        valueField: 'id',
        labelField: 'label',
        searchField: [ 'label', 'sublabel' ],
        plugins: ['virtual_scroll'],

        firstUrl: function(query) {
            return endpointUrl.replace('{query}', encodeURIComponent(query)).replace('{page}', '1');
        },
        load: function(query, callback) {
            const url = this.getUrl(query);

            fetch(url)
                .then(response => response.json())
                .then(json => {
                    console.log(json);

                    if(json.page < json.pages) {
                        const nextUrl = endpointUrl.replace('{query}', encodeURIComponent(query)).replace('{page}', json.page + 1);
                        console.log(nextUrl);
                        this.setNextUrl(query, nextUrl);
                    }

                    callback(json.items);
                })
                .catch(e => {
                    console.error(e);
                })
        },
        render: {
            option: function(item, escape) {
                return `<div class="py-2 d-flex">
                            <div class="flex-fill">
                                <div class="title">${escape(item.label)}</div>
                                <div class="text-muted">${escape(item.sublabel)}</div>
                            </div>
                            <div class="ms-auto">
                                ${escape(item.extra)}
                            </div>
						</div>`;
            },
            loading_more: function(data, escape) {
                return `<div class="loading-more-results py-2 d-flex align-items-center"><div class="spinner"></div></div>`;
            }
        }
    })
}