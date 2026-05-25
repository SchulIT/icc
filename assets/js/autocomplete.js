import TomSelect from "tom-select/dist/js/tom-select.complete";

const symfonyUxAutocompleteUrlAttributeName = 'data-symfony--ux-autocomplete--autocomplete-url-value';
const symfonyUxAutocompleteNoMoreResultsFoundAttributeName = 'data-symfony--ux-autocomplete--autocomplete-no-results-found-text-value';
const symfonyUxAutocompleteNoResultsFoundAttributeName = 'data-symfony--ux-autocomplete--autocomplete-no-results-found-text-value';
const symfonyUxAutocompleteLoadingMoreAttributeName = 'data-symfony--ux-autocomplete--autocomplete-loading-more-text-value';
const symfonyUxAutocompletePreloadAttributeName = 'data-symfony--ux-autocomplete--autocomplete-preload-value';

for(let $element of document.querySelectorAll('select[' + symfonyUxAutocompleteUrlAttributeName + ']')) {
    let endpointUrl = $element.getAttribute(symfonyUxAutocompleteUrlAttributeName);
    let loadingMoreText = $element.getAttribute(symfonyUxAutocompleteLoadingMoreAttributeName);
    let noMoreResultsText = $element.getAttribute(symfonyUxAutocompleteNoMoreResultsFoundAttributeName);
    let noResultsText = $element.getAttribute(symfonyUxAutocompleteNoResultsFoundAttributeName);
    let preload = $element.getAttribute(symfonyUxAutocompletePreloadAttributeName);

    new TomSelect($element, {
        valueField: 'value',
        labelField: 'text',
        searchField: [ 'text', 'sublabel' ],
        plugins: ['virtual_scroll', 'remove_button'],

        preload: preload,
        score: () => () => 1,
        firstUrl: function(query) {
            const separator = endpointUrl.includes('?') ? '&' : '?';
            return `${endpointUrl}${separator}query=${encodeURIComponent(query)}`;
        },
        load: function(query, callback) {
            const url = this.getUrl(query);

            fetch(url)
                .then(response => response.json())
                .then(json => {
                    this.setNextUrl(query, json.next_page);
                    callback(json.results || [ ]);
                })
                .catch(e => {
                    console.error(e);
                })
        },
        render: {
            option: function(item, escape) {
                return `<div class="py-2 d-flex">
                            <div class="flex-fill">
                                <div class="title">${escape(item.text)}</div>
                                <div class="text-muted">${escape(item.sublabel || '')}</div>
                            </div>
                            <div class="ms-auto">
                                ${escape(item.extra || '')}
                            </div>
						</div>`;
            },
            item: function(item, escape) {
                return `<div>${escape(item.text)}</div>`;
            },
            loading_more: function(data, escape) {
                return `<div class="loading-more-results">${loadingMoreText}</div>`;
            },
            no_results: () => `<div class="no-results">${noResultsText}</div>`,
            no_more_results: () => `<div class="no-more-results">${noMoreResultsText}</div>`,
        }
    })
}