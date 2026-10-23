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
    });
});

/* Bevestigingspop-up voor formulieren met data-confirm. */
document.addEventListener('submit', ev => {
    const form = ev.target;
    if (!form.dataset.confirm || form.dataset.confirmed) return;
    ev.preventDefault();
    const dlg = document.createElement('dialog');
    dlg.className = 'confirm';
    dlg.innerHTML = '<p></p><div class="confirm-actions"><button type="button" class="btn btn-ghost" value="no">Annuleren</button><button type="button" class="btn btn-danger" value="yes">Bevestigen</button></div>';
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
