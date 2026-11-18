/**
 * Searchable dropdowns everywhere in the certificate manager.
 *
 * Every single <select> (including ones added later by Alpine / the template form)
 * is upgraded with Tom Select. The original <select> stays in the DOM and remains the
 * source of truth, so form posts, x-model and existing JS keep working:
 *  - picking in Tom Select fires input/change on the original select;
 *  - a light watcher keeps Tom Select in step when code changes the original select's
 *    value, options (e.g. vessel list filtered by customer) or disabled state.
 *
 * Opt out per element with class "no-search" or attribute data-no-search.
 * Needs window.TomSelect.
 */
(function () {
    'use strict';
    if (!window.TomSelect) return;

    const SKIP = 'select[multiple], select.no-search, select[data-no-search]';
    // Order-insensitive: Tom Select itself reorders the original <option>s when a value is picked
    const signature = (sel) => Array.from(sel.options).map((o) => o.value + '\u0001' + o.text + (o.disabled ? '\u0003' : '')).sort().join('\u0002');

    function enhance(sel) {
        if (sel.tomselect || sel.matches(SKIP) || sel.closest('template')) return;
        const empty = Array.from(sel.options).find((o) => o.value === '');
        try {
            new TomSelect(sel, {
                allowEmptyOption: true,
                create: false,
                maxOptions: null,
                placeholder: empty ? empty.text : 'Select…',
                searchField: ['text'],
                dropdownParent: 'body',
                render: {
                    no_results: () => '<div class="no-results">No matches</div>',
                },
                onInitialize() { this.wrapper.title = sel.title || ''; },
            });
            sel.__csSig = signature(sel);
        } catch (e) {
            console.warn('cs-select: could not enhance', sel, e);
        }
    }

    function enhanceAll(root) {
        (root || document).querySelectorAll('select:not(.tomselected)').forEach(enhance);
    }

    // Keep Tom Select in step with programmatic changes to the original <select>
    function syncAll() {
        document.querySelectorAll('select.tomselected').forEach((sel) => {
            const ts = sel.tomselect;
            if (!ts || ts.isOpen) return;
            const sig = signature(sel);
            if (sig !== sel.__csSig) {
                // The option list really changed (e.g. vessels filtered by customer): rebuild, keep value
                const keep = sel.value;
                ts.clearOptions();
                ts.sync();
                const stillThere = Array.from(sel.options).some((o) => o.value === keep);
                ts.setValue(stillThere ? keep : '', true);
                if (sel.value !== (stillThere ? keep : '')) sel.value = stillThere ? keep : '';
                sel.__csSig = signature(sel);
            }
            if (String(ts.getValue()) !== String(sel.value)) ts.setValue(sel.value, true);
            if (sel.disabled && !ts.isDisabled) ts.disable();
            if (!sel.disabled && ts.isDisabled) ts.enable();
        });
    }

    let pending = null;
    const schedule = () => {
        if (pending) return;
        pending = setTimeout(() => { pending = null; enhanceAll(); }, 60);
    };

    function start() {
        enhanceAll();
        new MutationObserver((muts) => {
            if (muts.some((m) => Array.from(m.addedNodes).some((n) => n.nodeType === 1 && (n.tagName === 'SELECT' || n.querySelector?.('select'))))) schedule();
        }).observe(document.body, { childList: true, subtree: true });
        setInterval(syncAll, 300);
    }

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', start);
    else start();
    document.addEventListener('alpine:initialized', () => setTimeout(() => { enhanceAll(); syncAll(); }, 0));
})();
