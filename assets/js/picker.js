import Picker from 'vanilla-picker';

document.addEventListener('DOMContentLoaded', function() {
    let convertColor = function(hexa) {
        return hexa.substring(0, 7);
    };
    
    document.querySelectorAll('[data-toggle=color-picker]').forEach(function(button) {
        let input = button.closest('.input-group').querySelector('input');

        let colorRect = button.closest('.input-group').querySelector('.color-rect');
        colorRect.style.backgroundColor = convertColor(input.value);

        let picker = new Picker({
            parent: button,
            alpha: false,
            popup: 'left',
            color: input.value
        });

        picker.onDone = function(color) {
            console.log('onDone()');
            input.value = convertColor(color.hex);
        };

        picker.onClose = function(color) {
            console.log('onClose()');
            colorRect.style.backgroundColor  = convertColor(color.hex);
        };
    });
});