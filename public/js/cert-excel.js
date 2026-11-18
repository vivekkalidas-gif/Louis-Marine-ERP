/**
 * Certificate form ⇄ Excel.
 *
 *  CertExcel.download(template)            – build a sample .xlsx for the template
 *  CertExcel.importFile(file, template, form) – fill the rendered form from a filled .xlsx/.csv
 *  CertExcel.toolbarHtml()                 – buttons injected at the top of the template form
 *
 * Workbook layout:
 *  - "Instructions"  how to fill it + allowed values for dropdowns in repeatable sections
 *  - "Details"       one row per single-entry field: Section | Field | Value | Required | Allowed values | Key
 *  - one sheet per repeatable section: row 1 = field labels, one row per item from row 2
 *
 * Form conventions (rendered by the certificate create/edit views):
 *  single fields      name="field_{id}"
 *  repeatable fields  name="field_{id}_instance_{n}"  (n = 1-based row)
 *  repeatable section container  [data-cs-section="{sectionId}"] with Alpine data { instanceIds, nextId }
 *
 * Needs SheetJS (window.XLSX) loaded first.
 */
(function () {
    'use strict';

    const DETAILS = 'Details';
    const INSTRUCTIONS = 'Instructions';
    const norm = (s) => String(s ?? '').replace(/\s*\*\s*$/, '').trim().toLowerCase();

    function options(field) {
        let o = field.field_options;
        try { if (typeof o === 'string') o = JSON.parse(o); } catch (e) { return []; }
        if (o && !Array.isArray(o) && Array.isArray(o.options)) o = o.options;
        return Array.isArray(o) ? o.map(String) : [];
    }

    function hint(field) {
        const t = field.field_type;
        if (t === 'dropdown' || t === 'select' || t === 'radio') return options(field).join(' / ');
        if (t === 'date') return 'Date (DD/MM/YYYY)';
        if (t === 'number') return 'Number';
        return field.placeholder ? 'e.g. ' + field.placeholder : 'Text';
    }

    // ------------------------------------------------------------ example data
    // Realistic sample values so the downloaded sheet is self-explanatory.
    const MAKERS = [
        [/scba|breathing app/i, ['Dräger', 'MSA', 'Interspiro'], ['PSS 3000', 'AirGo Pro', 'Spiromatic S8']],
        [/eebd/i, ['Dräger', 'Ocenco', 'Spasciani'], ['Saver CF 15', 'M-20.3', 'ESA 15']],
        [/extinguisher/i, ['Gloria', 'Kidde', 'Total'], ['PD 6 GA', 'K6 CO2', 'P12 Foam']],
        [/co2|co₂/i, ['Kidde', 'Tyco', 'Minimax'], ['45 kg CO2 bank', '68 L cylinder', 'MX 1230']],
        [/life ?raft/i, ['Viking', 'Survitec', 'Zodiac'], ['25DK+', 'RFD Surviva 25', 'Zodiac 20 SOLAS']],
        [/life ?jacket/i, ['Viking', 'Lalizas', 'Crewsaver'], ['PV9330', 'SOLAS 2020 Adult', 'Ergofit 150N']],
        [/immersion/i, ['Viking', 'Survitec', 'Hansen'], ['PS2002', 'Seatex Ocean', 'Neptun']],
        [/lifeboat|rescue boat/i, ['Fassmer', 'Hatecke', 'Viking Norsafe'], ['SEL 8.5 T', 'Free-fall FFB 6.5', 'Magnum 750']],
        [/gas det/i, ['Dräger', 'MSA', 'Honeywell BW'], ['X-am 5000', 'ALTAIR 4XR', 'GasAlertMax XT II']],
        [/pressure gauge/i, ['WIKA', 'Ashcroft', 'Baumer'], ['232.50', 'Type 1279', 'MEX5']],
        [/fire detection|alarm/i, ['Consilium', 'Autronica', 'Tyco'], ['Salwico CS4000', 'AutroSafe 4', 'MX 4100']],
        [/foam/i, ['Sabo', 'Angus', 'Dr. Sthamer'], ['Sabofoam AFFF 3%', 'Tridol S 3%', 'Moussol APS']],
        [/hose|hydrant/i, ['Angus', 'Delta', 'Mertex'], ['Duraline 45', 'Marine 2"', 'Type 3']],
        [/compressor|breathing air/i, ['Bauer', 'Coltri', 'L&W'], ['Mariner 250', 'MCH 16', 'LW 300 E']],
        [/oxygen|resuscitator/i, ['Laerdal', 'Ambu', 'Oxy-Res'], ['LSU', 'Oxy-Viva 3', 'Res-Q-Vac']],
        [/pyrotechnic|distress/i, ['Pains Wessex', 'Ikaros', 'Comet'], ['Para Red Rocket', 'Hand Flare MkIII', 'MOB Smoke']],
        [/harness|fall arrest/i, ['3M DBI-SALA', 'Petzl', 'MSA'], ['ExoFit NEX', 'NEWTON', 'V-FORM']],
        [/cylinder|hydrostatic/i, ['Luxfer', 'Faber', 'Worthington'], ['L65G', '6.8 L 300 bar', '50 L CO2']],
    ];
    const PEOPLE = ['Rajesh Kumar', 'Mohammed Al-Kuwari', 'Anil Thomas', 'Sameer Khan'];
    const LOCATIONS = ['Engine Room, Deck 2', 'Bridge', 'Accommodation, Deck 3', 'Main Deck, Port Side', 'Galley'];

    const makerFor = (template) => (MAKERS.find(([re]) => re.test(template.name || '') || re.test(template.title || '')) || [null, ['Viking', 'Dräger', 'Kidde'], ['Model A', 'Model B', 'Model C']]);

    function serialDate(y, m, d) { return (Date.UTC(y, m - 1, d) - Date.UTC(1899, 11, 30)) / 86400000; }
    const dateCell = (y, m, d) => ({ t: 'n', v: serialDate(y, m, d), z: 'dd/mm/yyyy' });

    function fromPlaceholder(ph) {
        if (!ph) return null;
        const s = String(ph).replace(/^e\.?g\.?,?\s*/i, '').trim();
        if (!s || s.length > 60) return null;
        return s.split(/,\s*/)[0].replace(/^[≤≥<>±~]\s*/, '').trim() || null;
    }

    function exampleFor(field, template, row = 0) {
        const key = `${field.field_name} ${field.field_label}`.toLowerCase();
        const [, makers, models] = makerFor(template);
        const now = new Date(); const y = now.getFullYear(), m = now.getMonth() + 1;

        if (field.default_value) return field.default_value;
        const opts = options(field);
        if (['dropdown', 'select', 'radio'].includes(field.field_type) && opts.length) {
            if (/result|status|test$/.test(key)) return opts[0];                      // Pass / Satisfactory …
            return opts[Math.min(row, opts.length - 1)];
        }
        if (field.field_type === 'date') {
            if (/next|due|expir|valid until/.test(key)) return dateCell(y + 1, m, 10);
            if (/manufactur|produc|made/.test(key)) return dateCell(y - 3 - row, 3 + row, 15);
            if (/last|previous|hydro/.test(key)) return dateCell(y - 1, m, 12);
            return dateCell(y, m, 10);
        }
        if (field.field_type === 'number') {
            if (/person|capacity_persons/.test(key)) return 25 + row * 5;
            if (/zone|loop/.test(key)) return 8;
            if (/detector/.test(key)) return 64;
            if (/call point/.test(key)) return 22;
            if (/hour/.test(key)) return 1240 + row * 85;
            if (/litre|quantity/.test(key)) return 200;
            if (/pressure/.test(key)) return 300;
            return 1 + row;
        }
        if (field.field_type === 'textarea') return /remark|defect|note/.test(key) ? 'No defects found.' : 'Satisfactory.';

        // text
        const unitInLabel = /\((bar|l|kg|litres?|ppm|%|mm)\)/i.test(field.field_label);
        if (/test pressure/.test(key)) { const v = /hose|hydrant/i.test(template.name) ? '15' : '450'; return unitInLabel ? v : v + ' bar'; }
        if (/working pressure/.test(key)) return unitInLabel ? '300' : '300 bar';
        if (/cylinder.*serial|cylinder no/.test(key)) return `CYL-${String(y).slice(2)}-${String(7731 + row * 7).padStart(4, '0')}`;
        if (/serial|tag|s\/n/.test(key)) return `LM-${String(y).slice(2)}-${String(4187 + row * 13).padStart(4, '0')}`;
        if (/davit|launching/.test(key)) return ['Gravity davit, 2 × 3.5 t', 'Single-arm davit, 3 t', 'Free-fall ramp'][row % 3];
        if (/release gear/.test(key)) return `${makers[row % makers.length]} on-load release hook`;
        if (/manufacturer|make|brand/.test(key) && !/model/.test(key)) return makers[row % makers.length];
        if (/make.*model|model/.test(key)) return `${makers[row % makers.length]} ${models[row % models.length]}`.replace(/^(\S+) \1 /, '$1 ');
        if (/inspector|engineer|technician|tested by|surveyor|name/.test(key)) return PEOPLE[row % PEOPLE.length];
        if (/location|position|area/.test(key)) return LOCATIONS[row % LOCATIONS.length];
        if (/batch|lot/.test(key)) return `B${y}-${String(318 + row).padStart(4, '0')}`;
        if (/laborator/.test(key)) return 'Intertek Qatar';
        const ph = fromPlaceholder(field.placeholder);
        if (ph) return ph;
        if (/pressure/.test(key)) return '300 bar';
        if (/capacity|size/.test(key)) return '6.8 L';
        if (/reading|value|error/.test(key)) return 'Within limits';
        if (/condition/.test(key)) return 'Good';
        return 'As found';
    }

    function sortByOrder(list) {
        return [...(list || [])].sort((a, b) => (a.display_order ?? 0) - (b.display_order ?? 0));
    }

    // Excel sheet names: max 31 chars, no []:*?/\ , unique, not reserved
    function sheetNames(sections) {
        const used = new Set([DETAILS.toLowerCase(), INSTRUCTIONS.toLowerCase()]);
        const map = new Map();
        sections.forEach((s) => {
            let base = String(s.name || 'Section').replace(/[\[\]:*?\/\\]/g, ' ').replace(/\s+/g, ' ').trim().slice(0, 31) || 'Section';
            let name = base, i = 2;
            while (used.has(name.toLowerCase())) name = (base.slice(0, 28) + ' ' + i++).trim();
            used.add(name.toLowerCase());
            map.set(s.id, name);
        });
        return map;
    }

    function fileName(template) {
        return String(template.name || 'certificate').replace(/[^a-z0-9]+/gi, '-').replace(/^-|-$/g, '').toLowerCase() + '-data.xlsx';
    }

    // ---------------------------------------------------------------- download
    function download(template) {
        if (!window.XLSX) { alert('Excel library failed to load. Check your internet connection and reload the page.'); return; }
        if (!template) { alert('Choose an equipment category first.'); return; }

        const sections = sortByOrder(template.sections);
        const repeatable = sections.filter((s) => s.is_repeatable);
        const names = sheetNames(repeatable);
        const wb = XLSX.utils.book_new();

        // Instructions
        const info = [
            ['Certificate data sheet: ' + (template.title || template.name)],
            [],
            ['How to use this file'],
            ['The sheets are pre-filled with EXAMPLE data so you can see what goes where. Replace every example with the real values before importing.'],
            [],
            ['1. "Details" sheet: type each value in column C (Value). Do not change columns A, B, D, E or F.'],
            ['2. Every other sheet is a list section (for example several cylinders). One row per item, starting on row 2; delete example rows you do not need. Keep the column headings and sheet names as they are.'],
            ['3. Dates: use an Excel date cell, or type DD/MM/YYYY or YYYY-MM-DD.'],
            ['4. Dropdown fields must use one of the allowed values listed below or in column E of "Details".'],
            ['5. In the certificate form click "Import Excel", pick this file, then check the form before saving.'],
        ];
        const listHints = [];
        repeatable.forEach((s) => sortByOrder(s.fields).forEach((f) => {
            if (['dropdown', 'select', 'radio'].includes(f.field_type)) listHints.push([names.get(s.id) + ' › ' + f.field_label + ': ' + hint(f)]);
        }));
        if (listHints.length) info.push([], ['Allowed values in list sheets'], ...listHints);
        const wsInfo = XLSX.utils.aoa_to_sheet(info);
        wsInfo['!cols'] = [{ wch: 110 }];
        XLSX.utils.book_append_sheet(wb, wsInfo, INSTRUCTIONS);

        // Details (single-entry fields)
        const details = [['Section', 'Field', 'Value', 'Required', 'Allowed values / format', 'Key (do not edit)']];
        sections.filter((s) => !s.is_repeatable).forEach((s) => sortByOrder(s.fields).forEach((f) => {
            details.push([s.name, f.field_label, '', f.is_required ? 'Yes' : '', hint(f), 'field_' + f.id]);
        }));
        const wsDetails = XLSX.utils.aoa_to_sheet(details);
        // Example values in column C (dates as real Excel dates)
        let r = 1;
        sections.filter((s) => !s.is_repeatable).forEach((s) => sortByOrder(s.fields).forEach((f) => {
            const ex = exampleFor(f, template, 0);
            wsDetails['C' + (++r)] = typeof ex === 'object' ? ex : { t: typeof ex === 'number' ? 'n' : 's', v: ex };
        }));
        wsDetails['!cols'] = [{ wch: 26 }, { wch: 32 }, { wch: 48 }, { wch: 9 }, { wch: 44 }, { wch: 18 }];
        XLSX.utils.book_append_sheet(wb, wsDetails, DETAILS);

        // One sheet per repeatable section
        repeatable.forEach((s) => {
            const fields = sortByOrder(s.fields);
            const ws = XLSX.utils.aoa_to_sheet([fields.map((f) => f.field_label + (f.is_required ? ' *' : ''))]);
            [0, 1, 2].forEach((row) => fields.forEach((f, c) => {
                const ex = exampleFor(f, template, row);
                ws[XLSX.utils.encode_cell({ r: row + 1, c })] = typeof ex === 'object' ? ex : { t: typeof ex === 'number' ? 'n' : 's', v: ex };
            }));
            ws['!ref'] = XLSX.utils.encode_range({ s: { r: 0, c: 0 }, e: { r: 3, c: Math.max(0, fields.length - 1) } });
            ws['!cols'] = fields.map((f) => ({ wch: Math.max(14, Math.min(40, f.field_label.length + 6)) }));
            XLSX.utils.book_append_sheet(wb, ws, names.get(s.id));
        });

        XLSX.writeFile(wb, fileName(template));
    }

    // ---------------------------------------------------------------- import
    function pad(n) { return String(n).padStart(2, '0'); }

    function toIsoDate(raw) {
        if (raw === null || raw === undefined || raw === '') return '';
        if (typeof raw === 'number') {
            const d = XLSX.SSF.parse_date_code(raw);
            return d ? `${d.y}-${pad(d.m)}-${pad(d.d)}` : '';
        }
        if (raw instanceof Date && !isNaN(raw)) return `${raw.getFullYear()}-${pad(raw.getMonth() + 1)}-${pad(raw.getDate())}`;
        const s = String(raw).trim();
        let m = s.match(/^(\d{4})[-\/.](\d{1,2})[-\/.](\d{1,2})/);            // YYYY-MM-DD
        if (m) return `${m[1]}-${pad(m[2])}-${pad(m[3])}`;
        m = s.match(/^(\d{1,2})[-\/.](\d{1,2})[-\/.](\d{2,4})$/);              // DD/MM/YYYY
        if (m) { const y = m[3].length === 2 ? '20' + m[3] : m[3]; return `${y}-${pad(m[2])}-${pad(m[1])}`; }
        const d = new Date(s);
        return isNaN(d) ? '' : `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
    }

    // Returns null on success, or a warning string
    function setValue(form, name, field, raw) {
        if (raw === null || raw === undefined || String(raw).trim() === '') return 'empty';
        const els = form.querySelectorAll(`[name="${name}"]`);
        if (!els.length) return `${field.field_label}: field not found on the form`;
        const first = els[0];
        let value = raw;

        if (field.field_type === 'date') {
            value = toIsoDate(raw);
            if (!value) return `${field.field_label}: "${raw}" is not a date`;
        } else if (typeof raw === 'number' && field.field_type !== 'number') {
            value = String(raw);
        }

        if (first.type === 'radio') {
            const match = [...els].find((r) => norm(r.value) === norm(value));
            if (!match) return `${field.field_label}: "${raw}" is not one of ${options(field).join(' / ')}`;
            match.checked = true;
            match.dispatchEvent(new Event('change', { bubbles: true }));
            return null;
        }
        if (first.tagName === 'SELECT') {
            const match = [...first.options].find((o) => o.value && (norm(o.value) === norm(value) || norm(o.text) === norm(value)));
            if (!match) return `${field.field_label}: "${raw}" is not one of ${options(field).join(' / ')}`;
            first.value = match.value;
        } else {
            first.value = String(value).trim();
        }
        first.dispatchEvent(new Event('input', { bubbles: true }));
        first.dispatchEvent(new Event('change', { bubbles: true }));
        return null;
    }

    function rowsOf(ws) {
        return XLSX.utils.sheet_to_json(ws, { header: 1, raw: true, defval: '', blankrows: false });
    }

    const nextFrame = () => new Promise((r) => (window.Alpine && Alpine.nextTick ? Alpine.nextTick(r) : setTimeout(r, 30)));

    async function importFile(file, template, form) {
        if (!file) return;
        if (!window.XLSX) { alert('Excel library failed to load. Check your internet connection and reload the page.'); return; }
        if (!template) { alert('Choose an equipment category first.'); return; }

        let wb;
        try {
            wb = XLSX.read(await file.arrayBuffer(), { type: 'array', cellDates: false });
        } catch (e) {
            alert('Could not read this file. Please use the sample Excel (.xlsx) downloaded from this form.');
            return;
        }

        const sections = sortByOrder(template.sections);
        const byId = new Map();
        sections.forEach((s) => (s.fields || []).forEach((f) => byId.set('field_' + f.id, { field: f, section: s })));
        const warnings = [];
        let filled = 0, listRows = 0;

        // ---- Details sheet (or the first sheet of a 2-column style file)
        const detailsName = wb.SheetNames.find((n) => norm(n) === 'details');
        if (detailsName) {
            const rows = rowsOf(wb.Sheets[detailsName]);
            const head = (rows[0] || []).map(norm);
            const col = (label, fallback) => { const i = head.indexOf(label); return i >= 0 ? i : fallback; };
            const cSection = col('section', 0), cField = col('field', 1), cValue = col('value', 2), cKey = col('key (do not edit)', 5);
            rows.slice(1).forEach((r) => {
                let hit = byId.get(String(r[cKey] || '').trim());
                if (!hit) {
                    const s = sections.find((x) => !x.is_repeatable && norm(x.name) === norm(r[cSection]));
                    const f = s && (s.fields || []).find((x) => norm(x.field_label) === norm(r[cField]));
                    if (f) hit = { field: f, section: s };
                }
                if (!hit) { if (String(r[cValue] ?? '').trim()) warnings.push(`Details: no form field "${r[cField]}"`); return; }
                if (hit.section.is_repeatable) return;
                const w = setValue(form, 'field_' + hit.field.id, hit.field, r[cValue]);
                if (w === null) filled++; else if (w !== 'empty') warnings.push(w);
            });
        }

        // ---- One sheet per repeatable section
        const repeatable = sections.filter((s) => s.is_repeatable);
        const names = sheetNames(repeatable);
        for (const s of repeatable) {
            const sheetName = wb.SheetNames.find((n) => norm(n) === norm(names.get(s.id)) || norm(n) === norm(s.name));
            if (!sheetName) continue;
            const rows = rowsOf(wb.Sheets[sheetName]);
            if (rows.length < 2) continue;
            const head = rows[0].map(norm);
            const fields = sortByOrder(s.fields);
            const colOf = new Map(fields.map((f) => [f.id, head.indexOf(norm(f.field_label))]));
            const data = rows.slice(1).filter((r) => r.some((v) => String(v).trim() !== ''));
            if (!data.length) continue;

            const container = form.querySelector(`[data-cs-section="${s.id}"]`);
            const alpine = container && window.Alpine ? Alpine.$data(container) : null;
            if (!alpine) { warnings.push(`${s.name}: could not add rows on this form`); continue; }

            alpine.instanceIds = data.map(() => alpine.nextId++);   // replace existing rows
            await nextFrame();
            await new Promise((r) => setTimeout(r, 30));

            data.forEach((r, i) => {
                fields.forEach((f) => {
                    const c = colOf.get(f.id);
                    if (c < 0) return;
                    const w = setValue(form, `field_${f.id}_instance_${i + 1}`, f, r[c]);
                    if (w === null) filled++; else if (w !== 'empty') warnings.push(`${s.name} row ${i + 2} – ${w}`);
                });
            });
            listRows += data.length;
            fields.filter((f) => colOf.get(f.id) < 0).forEach((f) => warnings.push(`${s.name}: column "${f.field_label}" not found in sheet "${sheetName}"`));
        }

        let msg = `Imported ${filled} value${filled === 1 ? '' : 's'}`;
        if (listRows) msg += ` including ${listRows} list row${listRows === 1 ? '' : 's'}`;
        msg += '.';
        if (!filled) msg = 'Nothing was imported. Use the sample Excel downloaded from this form and fill column C of the "Details" sheet.';
        if (warnings.length) msg += '\n\nPlease check:\n• ' + warnings.slice(0, 12).join('\n• ') + (warnings.length > 12 ? `\n…and ${warnings.length - 12} more` : '');
        msg += '\n\nReview the form before saving.';
        alert(msg);
    }

    function toolbarHtml() {
        return `
        <div class="cs-excel-bar flex flex-wrap items-center gap-3 p-4 rounded-lg border border-gray-200 bg-gray-50">
            <div class="cs-excel-bar__text flex-1 min-w-[220px]">
                <div class="font-semibold text-sm">Fill from Excel</div>
                <div class="text-xs text-gray-500">Download the sample, fill it in Excel, then import it to complete this form.</div>
            </div>
            <button type="button" class="cs-btn cs-btn--light cs-btn--sm px-3 py-1.5 rounded border text-sm bg-white"
                    @click="CertExcel.download(currentTemplate)">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v12m0 0-4-4m4 4 4-4M4 20h16"/></svg>
                Download sample Excel
            </button>
            <label class="cs-btn cs-btn--primary cs-btn--sm px-3 py-1.5 rounded text-sm bg-blue-600 text-white cursor-pointer">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 20V8m0 0-4 4m4-4 4 4M4 4h16"/></svg>
                Import Excel
                <input type="file" accept=".xlsx,.xls,.csv" class="hidden" style="display:none"
                       @change="CertExcel.importFile($event.target.files[0], currentTemplate, $el.closest('form')); $event.target.value = ''">
            </label>
        </div>`;
    }

    window.CertExcel = { download, importFile, toolbarHtml, toIsoDate };
})();
