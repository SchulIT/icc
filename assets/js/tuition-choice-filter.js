document.addEventListener('DOMContentLoaded', function() {
    for(let element of document.querySelectorAll('[data-tuition-filter-target]')) {
        console.log(element);

        element.addEventListener('keyup', function() {
            let value = this.value;
            let select = document.querySelector(element.getAttribute('data-tuition-filter-target'));

            if(select === null) {
                console.error('select not found');
                return;
            }

            for(const optgroup of select.children) {
                optgroup.classList.remove('hide');

                for(const option of optgroup.children) {
                    option.classList.remove('hide');
                }
            }

            if(value === null || value === '') {
                return;
            }

            for(const optgroup of select.children) {
                let hidden = 0;
                for(const option of optgroup.children) {
                    if(!option.text.includes(value)) {
                        option.classList.add('hide');
                        hidden++;
                    }
                }

                if(optgroup.children.length === hidden) {
                    optgroup.classList.add('hide');
                }
            }
        });
    }
});

