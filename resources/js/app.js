import './bootstrap';
import $ from 'jquery';
import select2 from 'select2';
import 'select2/dist/css/select2.min.css';
import Alpine from 'alpinejs';

window.$ = window.jQuery = $;
window.Alpine = Alpine;
select2($);

function initSelect2(scope = document) {
    $(scope).find('select.select2').each(function () {
        const $select = $(this);

        if ($select.hasClass('select2-hidden-accessible')) {
            $select.select2('destroy');
        }

        $select.select2({
            width: '100%',
            placeholder: $select.data('placeholder') || undefined,
            allowClear: Boolean($select.data('allow-clear')),
            dropdownAutoWidth: true,
        });
    });
}

window.initSelect2 = initSelect2;

$(document).ready(function () {
    initSelect2();
});

document.addEventListener('livewire:navigated', () => initSelect2());

Alpine.start();
