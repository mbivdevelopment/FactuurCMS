/* Tabs: [data-tabs] met .tab-link[data-tab] en .tab-pane[data-pane]; actieve tab volgt de #hash. */
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-tabs]').forEach(box => {
        const links = box.querySelectorAll('.tab-link[data-tab]'), panes = box.querySelectorAll('.tab-pane');
        const show = id => {
            if (![...panes].some(p => p.dataset.pane === id)) id = panes[0]?.dataset.pane;
            links.forEach(l => l.classList.toggle('active', l.dataset.tab === id));
            panes.forEach(p => p.classList.toggle('active', p.dataset.pane === id));
        };
        links.forEach(l => l.addEventListener('click', ev => { ev.preventDefault(); history.replaceState(null, '', '#' + l.dataset.tab); show(l.dataset.tab); }));
        show(location.hash.slice(1));
        window.addEventListener('hashchange', () => show(location.hash.slice(1)));
    });
});

/* Bevestigingspop-up voor formulieren met data-confirm. */
document.addEventListener('submit', ev => {
    const form = ev.target;
    if (!form.dataset.confirm || form.dataset.confirmed) return;
    ev.preventDefault();
    const dlg = document.createElement('dialog');
    dlg.className = 'confirm';
    dlg.innerHTML = '<div class="modal-h">Weet je het zeker?</div><p></p><div class="confirm-actions"><button type="button" class="btn btn-ghost" value="no">Annuleren</button><button type="button" class="btn btn-danger" value="yes">Bevestigen</button></div>';
    dlg.querySelector('p').textContent = form.dataset.confirm;
    document.body.appendChild(dlg);
    dlg.addEventListener('click', e => {
        const b = e.target.closest('button');
        if (!b && e.target !== dlg) return;
        dlg.close(); dlg.remove();
        if (b && b.value === 'yes') { form.dataset.confirmed = '1'; form.requestSubmit ? form.requestSubmit() : form.submit(); }
    });
    dlg.showModal();
});

/* Documentregels: toevoegen, verwijderen, product kiezen en totalen live berekenen. */
(() => {
    const num = v => parseFloat(String(v || '0').replace(/\./g, '').replace(',', '.')) || 0;
    const fmt = c => '€ ' + (c / 100).toLocaleString('nl-NL', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const calc = table => {
        let sub = 0, vat = 0;
        table.querySelectorAll('tbody tr').forEach(tr => {
            const q = num(tr.querySelector('[name$="[Aantal]"]')?.value), p = Math.round(num(tr.querySelector('[name$="[Prijs]"]')?.value) * 100), r = num(tr.querySelector('[name$="[BtwTarief]"]')?.value);
            const amount = Math.round(q * p);
            sub += amount; vat += Math.round(amount * r / 100);
            const cell = tr.querySelector('[data-line-total]'); if (cell) cell.textContent = fmt(amount);
        });
        const set = (k, v) => { const el = table.parentElement.querySelector('[data-sum="' + k + '"]'); if (el) el.textContent = fmt(v); };
        set('sub', sub); set('vat', vat); set('total', sub + vat);
    };
    document.addEventListener('click', ev => {
        const add = ev.target.closest('[data-add-line]'), del = ev.target.closest('[data-remove-line]');
        if (add) {
            const table = add.closest('form').querySelector('[data-lines]'), body = table.querySelector('tbody'), row = body.lastElementChild.cloneNode(true), n = Date.now();
            row.querySelectorAll('input, select').forEach(el => {
                el.name = el.name.replace(/Regels\[\w+\]/, 'Regels[' + n + ']');
                if (el.tagName === 'INPUT') el.value = /Aantal/.test(el.name) ? '1' : (/Prijs/.test(el.name) ? '0,00' : (/BtwTarief/.test(el.name) ? el.value : ''));
                if (el.tagName === 'SELECT') el.selectedIndex = 0;
            });
            body.appendChild(row); calc(table);
        } else if (del) {
            const table = del.closest('[data-lines]'), body = del.closest('tbody');
            if (body.children.length > 1) del.closest('tr').remove(); else body.querySelectorAll('input').forEach(i => { if (!/BtwTarief|Aantal/.test(i.name)) i.value = ''; });
            calc(table);
        }
    });
    document.addEventListener('change', ev => {
        const pick = ev.target.closest('[data-product-pick]');
        if (pick && pick.selectedOptions[0]?.dataset.name) {
            const tr = pick.closest('tr'), o = pick.selectedOptions[0];
            tr.querySelector('[name$="[Omschrijving]"]').value = o.dataset.name;
            tr.querySelector('[name$="[Prijs]"]').value = o.dataset.price;
            tr.querySelector('[name$="[BtwTarief]"]').value = o.dataset.vat;
        }
    });
    document.addEventListener('input', ev => { const t = ev.target.closest('[data-lines]'); if (t) calc(t); });
    document.addEventListener('change', ev => { const t = ev.target.closest('[data-lines]'); if (t) calc(t); });
    document.addEventListener('DOMContentLoaded', () => document.querySelectorAll('[data-lines]').forEach(calc));
})();

/* Mobiel menu */
document.addEventListener('click', ev => {
    const t = ev.target.closest('[data-menu-toggle]');
    if (t) document.querySelector(t.dataset.menuToggle)?.classList.toggle('open');
});

/* Bestand kiezen: voorbeeld van het gekozen logo tonen. */
document.addEventListener('change', ev => {
    const input = ev.target.closest('input[type="file"][data-preview]');
    if (!input || !input.files[0]) return;
    const img = document.querySelector(input.dataset.preview);
    if (img) { img.src = URL.createObjectURL(input.files[0]); img.hidden = false; }
});

/* Meldingen rechtsboven verdwijnen na 4 seconden. */
document.addEventListener('DOMContentLoaded', () => document.querySelectorAll('[data-toasts] .toast').forEach(t => setTimeout(() => { t.classList.add('hide'); setTimeout(() => t.remove(), 400); }, 4000)));

document.addEventListener('click', (ev) => {
    const caret = ev.target.closest('.nav-item.has-drop .caret');
    const item = caret?.closest('.nav-item');
    document.querySelectorAll('.nav-item.open').forEach((el) => { if (el !== item) el.classList.remove('open'); });
    if (item) { ev.preventDefault(); item.classList.toggle('open'); }
});
document.addEventListener('keydown', (ev) => {
    if (ev.key === 'Escape') document.querySelectorAll('.nav-item.open').forEach((el) => { el.classList.remove('open'); el.querySelector('.nav-title')?.blur(); });
});

/* Website: megamenu openen via het pijltje (touch/mobiel) en sluiten met Esc of klik ernaast. */
document.addEventListener('click', ev => {
    const toggle = ev.target.closest('[data-mega-toggle]');
    const item = toggle?.closest('.mi');
    document.querySelectorAll('.mi.open').forEach(el => { if (el !== item && !el.contains(ev.target)) el.classList.remove('open'); });
    if (item) { ev.preventDefault(); item.classList.toggle('open'); }
});
document.addEventListener('keydown', ev => { if (ev.key === 'Escape') document.querySelectorAll('.mi.open').forEach(el => el.classList.remove('open')); });

/* Website: carrousels met pijlen, bolletjes en optioneel automatisch doorschuiven. */
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-carousel]').forEach(car => {
        const track = car.querySelector('.car-track'), items = [...track.children], dots = car.querySelector('.car-dots');
        const perView = () => Math.max(1, Math.round(track.clientWidth / (items[0]?.getBoundingClientRect().width || track.clientWidth)));
        const pages = () => Math.max(1, Math.ceil(items.length / perView()));
        const page = () => Math.round(track.scrollLeft / Math.max(1, track.clientWidth));
        const go = n => { const p = (n + pages()) % pages(); track.scrollTo({ left: p * track.clientWidth, behavior: 'smooth' }); };
        const drawDots = () => {
            dots.innerHTML = '';
            for (let i = 0; i < pages(); i++) { const b = document.createElement('button'); b.type = 'button'; b.setAttribute('aria-label', 'Pagina ' + (i + 1)); b.addEventListener('click', () => go(i)); dots.appendChild(b); }
            mark();
        };
        const mark = () => [...dots.children].forEach((b, i) => b.classList.toggle('active', i === Math.min(page(), pages() - 1)));
        car.querySelector('[data-car-prev]')?.addEventListener('click', () => go(page() - 1));
        car.querySelector('[data-car-next]')?.addEventListener('click', () => go(page() + 1));
        track.addEventListener('scroll', () => window.requestAnimationFrame(mark), { passive: true });
        window.addEventListener('resize', drawDots);
        drawDots();
        car.querySelector('.car-nav').hidden = pages() < 2;
        if (car.dataset.autoplay && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            let paused = false;
            car.addEventListener('mouseenter', () => { paused = true; });
            car.addEventListener('mouseleave', () => { paused = false; });
            setInterval(() => { if (!paused && !document.hidden) go(page() + 1); }, parseInt(car.dataset.autoplay, 10));
        }
    });

    /* Website: logo's per groep wisselen. */
    document.querySelectorAll('[data-logo-tabs]').forEach(box => box.addEventListener('click', ev => {
        const b = ev.target.closest('[data-logo-tab]');
        if (!b) return;
        box.querySelectorAll('[data-logo-tab]').forEach(x => x.classList.toggle('active', x === b));
        box.querySelectorAll('[data-logo-pane]').forEach(p => p.classList.toggle('active', p.dataset.logoPane === b.dataset.logoTab));
    }));
});

/* Ticket: snel antwoord invoegen in de reactie. */
document.addEventListener('change', ev => {
    const sel = ev.target.closest('[data-quick-reply]');
    if (!sel || !sel.value) return;
    const ta = document.querySelector(sel.dataset.quickReply);
    if (ta) { ta.value = (ta.value.trim() ? ta.value.trimEnd() + '\n\n' : '') + sel.value; ta.focus(); }
    sel.selectedIndex = 0;
});

/* Megamenu: links met #tab of ?filter actief markeren als ze precies de huidige weergave zijn. */
const markHashLinks = () => document.querySelectorAll('.drop-link[href*="#"], .drop-link[href*="?"]').forEach(a => {
    const u = new URL(a.href);
    const hash = location.hash || '#' + (document.querySelector('.tab-pane')?.dataset.pane ?? '');
    a.classList.toggle('active', u.pathname === location.pathname && (u.hash ? u.hash === hash : u.search === location.search));
    if (a.classList.contains('active')) a.closest('.nav-drop')?.querySelectorAll('.drop-link:not([href*="?"]):not([href*="#"])').forEach(b => { if (new URL(b.href).pathname === u.pathname) b.classList.remove('active'); });
});
document.addEventListener('DOMContentLoaded', markHashLinks);
window.addEventListener('hashchange', markHashLinks);

/* Importtabellen: filteren en alles (zichtbaar) selecteren. */
document.querySelectorAll('[data-filter]').forEach(function (input) {
    input.addEventListener('input', function () {
        var q = input.value.trim().toLowerCase();
        document.querySelectorAll('[data-filter-table] tbody tr').forEach(function (row) {
            row.style.display = row.textContent.toLowerCase().indexOf(q) > -1 ? '' : 'none';
        });
    });
});
document.querySelectorAll('[data-check-all]').forEach(function (box) {
    box.addEventListener('change', function () {
        box.closest('table').querySelectorAll('tbody tr').forEach(function (row) {
            var check = row.querySelector('input[type=checkbox]');
            if (check && row.style.display !== 'none') { check.checked = box.checked; }
        });
    });
});

/* Popups openen met data-dialog="id" en sluiten met data-close. */
document.addEventListener('click', function (e) {
    var open = e.target.closest('[data-dialog]');
    if (open) { var dlg = document.getElementById(open.dataset.dialog); if (dlg && dlg.showModal) { dlg.showModal(); } return; }
    var close = e.target.closest('[data-close]');
    if (close) { var d = close.closest('dialog'); if (d) { d.close(); } }
});
