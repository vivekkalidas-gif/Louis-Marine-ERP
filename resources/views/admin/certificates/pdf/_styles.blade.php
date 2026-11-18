<style>
    /* A4 = 210 × 297 mm. Header and footer live in the page margins (position: fixed),
       so content can never push them onto an extra blank page. */
    @page { size: A4; margin: 38mm 15mm 40mm 15mm; }
    * { box-sizing: border-box; }
    body { margin: 0; font-family: 'DejaVu Sans', sans-serif; font-size: 8.4pt; line-height: 1.38; color: #1c2b3a; }

    /* ---------- palette ---------- */
    .navy { color: #0b2545; } .teal { color: #0e8fa0; } .muted { color: #6b7a8a; }

    /* ---------- page frame (fixed = repeated on every page) ---------- */
    .frame-outer { position: fixed; top: -32mm; left: -9mm; right: -9mm; bottom: -33mm; border: 1.6pt solid #0b2545; }
    .frame-inner { position: fixed; top: -30.6mm; left: -7.6mm; right: -7.6mm; bottom: -31.6mm; border: 0.5pt solid #0e8fa0; }
    .topband { position: fixed; top: -38mm; left: -15mm; right: -15mm; height: 5mm; background: #0b2545; }
    .topband-accent { position: fixed; top: -33mm; left: -15mm; right: -15mm; height: 1.2mm; background: #0e8fa0; }
    .bottomband { position: fixed; bottom: -40mm; left: -15mm; right: -15mm; height: 3.5mm; background: #0b2545; }

    .watermark { position: fixed; top: 78mm; left: 32mm; width: 116mm; opacity: 0.045; }

    /* ---------- header ---------- */
    .hdr { position: fixed; top: -27.5mm; left: -3mm; right: -3mm; height: 22mm; }
    .hdr table { width: 100%; border-collapse: collapse; }
    .hdr td { vertical-align: middle; padding: 0; }
    .hdr-logo { height: 15mm; max-width: 46mm; }
    .co-name { font-size: 11pt; font-weight: bold; color: #0b2545; letter-spacing: 0.3pt; }
    .co-line { font-size: 6.6pt; color: #5b6b7b; margin-top: 0.8mm; }
    .certno-box { border: 1pt solid #0b2545; padding: 2.2mm 3.5mm; text-align: center; }
    .certno-label { font-size: 6.2pt; letter-spacing: 1.4pt; color: #0e8fa0; font-weight: bold; }
    .certno { font-family: 'DejaVu Sans Mono', monospace; font-size: 9.6pt; white-space: nowrap; font-weight: bold; color: #0b2545; margin-top: 0.8mm; }
    .hdr-rule { position: fixed; top: -4mm; left: -3mm; right: -3mm; border-top: 0.6pt solid #c9d4de; }

    /* ---------- footer ---------- */
    .ftr { position: fixed; bottom: -30mm; left: -3mm; right: -3mm; height: 29mm; }
    .ftr table { width: 100%; border-collapse: collapse; }
    .ftr td { vertical-align: bottom; padding: 0; }
    .sig-img { height: 10mm; max-width: 46mm; }
    .sig-space { height: 10mm; }
    .sig-line { border-top: 0.8pt solid #0b2545; margin: 0 6mm; padding-top: 1.2mm; }
    .sig-name { font-size: 9pt; font-weight: bold; color: #0b2545; }
    .sig-role { font-size: 7pt; color: #5b6b7b; }
    .qr { width: 19mm; height: 19mm; }
    .qr-label { font-size: 6pt; letter-spacing: 1.2pt; color: #0e8fa0; font-weight: bold; margin-top: 0.6mm; }
    .fineprint { font-size: 5.8pt; color: #8795a3; text-align: center; margin-top: 2.2mm; border-top: 0.5pt solid #dde4ea; padding-top: 1.4mm; }

    /* ---------- title ---------- */
    .title-wrap { text-align: center; margin: 0 0 3mm; }
    .title-kicker { font-size: 6.8pt; letter-spacing: 3pt; color: #0e8fa0; font-weight: bold; }
    .title { font-family: 'DejaVu Serif', serif; font-size: 16pt; font-weight: bold; color: #0b2545; letter-spacing: 0.4pt; margin: 1.5mm 0 1.2mm; }
    .title-rule { width: 34mm; margin: 0 auto; border-top: 1.4pt solid #0e8fa0; }
    .certify { font-family: 'DejaVu Serif', serif; font-style: italic; font-size: 8.2pt; color: #3d4f61; margin-top: 2mm; }

    /* ---------- particulars ---------- */
    table.grid { width: 100%; border-collapse: collapse; table-layout: fixed; }
    table.grid td { padding: 1.3mm 2.4mm; vertical-align: top; border: 0.5pt solid #dbe3ea; }
    .lbl { font-size: 6.2pt; letter-spacing: 0.9pt; color: #6b7a8a; font-weight: bold; text-transform: uppercase; }
    .val { font-size: 8.8pt; color: #0b2545; font-weight: bold; margin-top: 0.4mm; }
    .val-n { font-size: 8.4pt; color: #1c2b3a; margin-top: 0.4mm; }
    .panel { background: #f3f8fa; }

    /* ---------- sections ---------- */
    .sec { margin-top: 3mm; }
    .sec-head { border-left: 2.4mm solid #0e8fa0; padding: 0.6mm 0 0.6mm 2.4mm; margin-bottom: 1.6mm; }
    .sec-title { font-size: 8.6pt; font-weight: bold; letter-spacing: 1.2pt; color: #0b2545; text-transform: uppercase; }
    .sec-desc { font-size: 7pt; color: #6b7a8a; }
    .statement { border: 0.5pt solid #cfe4e8; background: #f3fafb; padding: 2.4mm 3mm; margin-bottom: 1.6mm; }
    .statement .val-n { font-family: 'DejaVu Serif', serif; font-size: 7.8pt; line-height: 1.45; text-align: justify; }

    .pill { display: inline-block; padding: 0.5mm 2.2mm; border-radius: 3mm; font-size: 7.4pt; font-weight: bold; }
    .pill-ok { background: #def3e4; color: #1e7a3a; }
    .pill-bad { background: #fbe1dc; color: #b3321b; }

    /* ---------- validity strip ---------- */
    table.validity { width: 100%; border-collapse: collapse; margin: 0 0 3mm; table-layout: fixed; }
    table.validity td { text-align: center; padding: 2.4mm 2mm; border: 1pt solid #0b2545; }
    .v-label { font-size: 6.2pt; letter-spacing: 1.4pt; color: #6b7a8a; font-weight: bold; }
    .v-value { font-size: 10.5pt; font-weight: bold; color: #0b2545; margin-top: 0.6mm; }
    .v-status-ok { background: #0b2545; }
    .v-status-ok .v-label { color: #9fd9e0; } .v-status-ok .v-value { color: #ffffff; }
    .v-status-bad { background: #b3321b; }
    .v-status-bad .v-label { color: #ffd6cc; } .v-status-bad .v-value { color: #ffffff; }

    /* ---------- register table (continuation sheet) ---------- */
    .note { font-size: 7.6pt; color: #3d4f61; margin: 1mm 0 0; }
    table.register { width: 100%; border-collapse: collapse; margin-top: 1mm; }
    table.register th { background: #0b2545; color: #ffffff; font-size: 7pt; font-weight: bold; text-align: left; padding: 1.8mm 2mm; letter-spacing: 0.4pt; }
    table.register td { font-size: 7.6pt; padding: 1.5mm 2mm; border-bottom: 0.5pt solid #dbe3ea; vertical-align: top; }
    table.register tr.alt td { background: #f3f8fa; }
    table.register td.idx { color: #8795a3; text-align: center; width: 8mm; }
    table.register th.idx { text-align: center; width: 8mm; }
    thead { display: table-header-group; }
    tr { page-break-inside: avoid; }
    table.decl { width: 100%; border-collapse: collapse; table-layout: fixed; }
    table.decl td { vertical-align: top; padding: 0 0 1.6mm 0; }
    table.decl td.half { width: 49%; }
    table.decl td.gap { width: 2%; padding: 0; }
    table.decl .statement .val-n { text-align: left; }
    table.decl .statement { margin: 0; }
    .co-name { white-space: nowrap; }
</style>
