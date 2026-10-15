// Progressive enhancement untuk <select> dan <input type="date"> di seluruh
// halaman Analisis (HR + pelamar) -- dropdown & kalender bawaan browser diganti
// tampilan custom bergaya GSM. Form tetap submit lewat <select>/<input> asli
// (disembunyikan, bukan dihapus) supaya tidak ada perubahan pada name/value yang
// dibaca controller.
(function () {
    'use strict';

    function perkayaSelect(select) {
        if (select.dataset.csDiperkaya) { return; }
        select.dataset.csDiperkaya = '1';

        var wrap = document.createElement('div');
        wrap.className = 'cs-wrap' + (select.classList.contains('select-mini') ? ' mini' : '');
        select.parentNode.insertBefore(wrap, select);
        wrap.appendChild(select);

        var trigger = document.createElement('div');
        trigger.className = 'cs-trigger';
        trigger.tabIndex = 0;
        wrap.appendChild(trigger);

        var menu = document.createElement('div');
        menu.className = 'cs-menu';
        wrap.appendChild(menu);

        function bangunMenu() {
            menu.innerHTML = '';
            Array.prototype.forEach.call(select.options, function (opt) {
                var item = document.createElement('div');
                item.className = 'cs-opsi' + (opt.selected ? ' terpilih' : '') + (opt.disabled ? ' nonaktif' : '');
                item.dataset.value = opt.value;

                var label = document.createElement('span');
                label.className = 'cs-opsi-teks';
                label.textContent = opt.textContent;
                item.appendChild(label);

                if (opt.dataset.status) {
                    var chip = document.createElement('span');
                    chip.className = 'cs-chip cs-chip-' + opt.dataset.status;
                    chip.textContent = opt.dataset.statusLabel || '';
                    item.appendChild(chip);
                }

                if (!opt.disabled) {
                    item.addEventListener('click', function () {
                        select.value = opt.value;
                        select.dispatchEvent(new Event('change', { bubbles: true }));
                        tutup();
                    });
                }
                menu.appendChild(item);
            });
        }

        function sinkronLabel() {
            var opt = select.options[select.selectedIndex];
            trigger.textContent = opt ? opt.textContent : '';
            trigger.classList.toggle('kosong', !opt || opt.value === '');
            Array.prototype.forEach.call(menu.children, function (el) {
                el.classList.toggle('terpilih', el.dataset.value === select.value);
            });
        }

        function buka() {
            document.querySelectorAll('.cs-menu.terbuka').forEach(function (m) { m.classList.remove('terbuka'); });
            document.querySelectorAll('.cs-trigger.terbuka').forEach(function (t) { t.classList.remove('terbuka'); });
            menu.classList.add('terbuka');
            trigger.classList.add('terbuka');
        }
        function tutup() {
            menu.classList.remove('terbuka');
            trigger.classList.remove('terbuka');
        }

        trigger.addEventListener('click', function (e) {
            e.stopPropagation();
            if (menu.classList.contains('terbuka')) { tutup(); } else { buka(); }
        });
        trigger.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); trigger.click(); }
            if (e.key === 'Escape') { tutup(); }
        });
        document.addEventListener('click', function (e) {
            if (!wrap.contains(e.target)) { tutup(); }
        });
        select.addEventListener('change', sinkronLabel);
        // select bisa diisi ulang lewat JS pemanggil (mis. ganti opsi dinamis) --
        // observer memastikan dropdown custom ikut sinkron tanpa perlu re-init manual.
        new MutationObserver(function () { bangunMenu(); sinkronLabel(); }).observe(select, { childList: true });

        bangunMenu();
        sinkronLabel();
    }

    function perkayaTanggal(input) {
        if (input.dataset.fpDiperkaya || typeof flatpickr === 'undefined') { return; }
        input.dataset.fpDiperkaya = '1';
        var fp = flatpickr(input, {
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'd-m-Y',
            minDate: input.min || null,
            maxDate: input.max || null,
            allowInput: true,
        });
        // flatpickr menyalin atribut (termasuk required) dari input asli ke altInput
        // yang dibuatnya. altInput itu tidak punya "name" dan tidak pernah terisi
        // lewat JS (fp.setDate hanya mengisi input asli) -- kalau dibiarkan required,
        // querySelectorAll('[required]') di kode pemanggil akan selalu menganggapnya
        // "kosong & wajib", memblokir validasi form padahal input aslinya sudah valid.
        if (fp.altInput) { fp.altInput.required = false; }

        // Set value dari luar (mis. pemulihan draf localStorage lewat input.value = ...)
        // tidak otomatis disadari flatpickr -- dengarkan 'change' di input ASLI (bukan
        // altInput-nya) dan paksa flatpickr mengikuti nilai itu.
        input.addEventListener('change', function () {
            if (input.value && fp.input.value !== input.value) { fp.setDate(input.value, false); }
        });

        // data-link-min-from="nama_field_lain" (mis. tgl_selesai -> tgl_mulai): kalender
        // tidak membiarkan tanggal sebelum field pasangannya dipilih, dan ikut geser
        // minDate-nya kalau field pasangan diubah belakangan.
        var nama_field_min = input.dataset.linkMinFrom;
        if (nama_field_min) {
            var field_min = document.querySelector('[name="' + nama_field_min + '"]');
            if (field_min) {
                var sinkronMin = function () {
                    if (field_min.value) { fp.set('minDate', field_min.value); }
                };
                field_min.addEventListener('change', sinkronMin);
                sinkronMin();
            }
        }
    }

    function init() {
        document.querySelectorAll('select:not([data-cs-skip])').forEach(perkayaSelect);
        document.querySelectorAll('input[type="date"]').forEach(perkayaTanggal);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
