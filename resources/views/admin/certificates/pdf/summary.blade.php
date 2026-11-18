<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $certificate->certificate_number }}</title>
    @include('admin.certificates.pdf._styles')
</head>
<body>
@php extract(require resource_path('views/admin/certificates/pdf/_brand.php')); @endphp
@include('admin.certificates.pdf._frame', ['continuation' => false])

@php
    $template = $certificate->certificateTemplate;
    $vessel = $certificate->vessel;
    $values = $certificate->fieldValues;
    $valueOf = fn ($field, $instance = 1) => optional($values->first(fn ($v) => $v->certificate_template_field_id == $field->id && (int) ($v->section_instance ?? 1) === $instance))->value;

    $isStatement = fn ($f) => $f->field_type === 'textarea' || $f->width === 'full';
    $sections = $template ? $template->sections->sortBy('display_order')->values() : collect();
    $single = $sections->where('is_repeatable', false);
    // Sections made only of long statements read best at the end, as the declaration
    $declaration = $single->filter(fn ($s) => $s->fields->count() && $s->fields->every($isStatement));
    $body = $single->reject(fn ($s) => $declaration->contains('id', $s->id));
    $lists = $sections->where('is_repeatable', true)->map(function ($s) use ($values) {
        $ids = $s->fields->pluck('id')->all();
        $s->item_count = $values->filter(fn ($v) => in_array($v->certificate_template_field_id, $ids))->pluck('section_instance')->unique()->count();
        return $s;
    })->filter(fn ($s) => $s->item_count > 0);

    $okWords = ['pass', 'passed', 'satisfactory', 'fit for service', 'within tolerance', 'adjusted & within tolerance', 'valid', 'yes'];
    $badWords = ['fail', 'failed', 'not fit for service', 'out of tolerance', 'condemned', 'not satisfactory – replace', 'no'];
    $isResult = fn ($f) => str_contains(strtolower($f->field_name . ' ' . $f->field_label), 'result');

    $va = $vessel ? $vessel->getAttributes() : [];   // columns differ between installs; read safely
    $particulars = array_values(array_filter([
        ['Vessel', $va['name'] ?? '—', true],
        ['IMO Number', $va['imo_number'] ?? null, false],
        ['Flag / Port of Registry', collect([$va['flag'] ?? null, $va['port_of_registry'] ?? null])->filter()->implode(' / ') ?: null, false],
        ['Call Sign', $va['call_sign'] ?? null, false],
        ['Owner / Customer', $certificate->customer->company_name ?? $certificate->customer->name ?? '—', true],
        ['Job Number', $certificate->job_number ?: ($certificate->job->job_number ?? '—'), true],
        ['Equipment', $certificate->equipmentCategory->name ?? null, false],
    ], fn ($p) => $p[1] !== null && $p[1] !== ''));
@endphp

<div class="title-wrap">
    <div class="title-kicker">CERTIFICATE OF INSPECTION &amp; SERVICE</div>
    <div class="title">{{ $template->title ?? 'Certificate of Inspection' }}</div>
    <div class="title-rule"></div>
    <div class="certify">This is to certify that the undernoted equipment has been inspected, tested and serviced in accordance with the applicable requirements.</div>
</div>

{{-- Validity --}}
<table class="validity">
    <tr>
        <td>
            <div class="v-label">DATE OF ISSUE</div>
            <div class="v-value">{{ $certificate->issue_date?->format('d F Y') ?? '—' }}</div>
        </td>
        <td>
            <div class="v-label">VALID UNTIL</div>
            <div class="v-value">{{ $certificate->expiry_date?->format('d F Y') ?? 'Not specified' }}</div>
        </td>
        <td class="{{ $statusValid ? 'v-status-ok' : 'v-status-bad' }}">
            <div class="v-label">STATUS</div>
            <div class="v-value">{{ $statusValid ? 'VALID' : 'EXPIRED' }}</div>
        </td>
    </tr>
</table>

{{-- Particulars --}}
<table class="grid panel">
    @foreach(array_chunk($particulars, 3) as $row)
        <tr>
            @foreach($row as [$label, $value, $strong])
                <td>
                    <div class="lbl">{{ $label }}</div>
                    <div class="{{ $strong ? 'val' : 'val-n' }}">{{ $value }}</div>
                </td>
            @endforeach
            @for($i = count($row); $i < 3; $i++)<td></td>@endfor
        </tr>
    @endforeach
</table>

{{-- Field sections --}}
@foreach($body as $section)
    @php
        $short = $section->fields->sortBy('display_order')->reject($isStatement)->values();
        $long = $section->fields->sortBy('display_order')->filter($isStatement)->values();
    @endphp
    <div class="sec">
        <div class="sec-head">
            <span class="sec-title">{{ $section->name }}</span>
            @if($section->description)<span class="sec-desc">&nbsp;·&nbsp;{{ $section->description }}</span>@endif
        </div>
        @if($short->count())
            <table class="grid">
                @foreach($short->chunk(3) as $row)
                    <tr>
                        @foreach($row as $field)
                            @php $v = $fmt($field, $valueOf($field)); $lv = strtolower(trim((string) $v)); @endphp
                            <td>
                                <div class="lbl">{{ $field->field_label }}</div>
                                @if($isResult($field) && in_array($lv, $okWords, true))
                                    <div style="margin-top: 0.6mm;"><span class="pill pill-ok">{{ $v }}</span></div>
                                @elseif($isResult($field) && in_array($lv, $badWords, true))
                                    <div style="margin-top: 0.6mm;"><span class="pill pill-bad">{{ $v }}</span></div>
                                @else
                                    <div class="val-n">{{ $v }}</div>
                                @endif
                            </td>
                        @endforeach
                        @for($i = $row->count(); $i < 3; $i++)<td></td>@endfor
                    </tr>
                @endforeach
            </table>
        @endif
        @foreach($long as $field)
            <div class="statement" style="margin-top: 1.6mm;">
                <div class="lbl">{{ $field->field_label }}</div>
                <div class="val-n">{{ $fmt($field, $valueOf($field)) }}</div>
            </div>
        @endforeach
    </div>
@endforeach

{{-- Lists are printed on the continuation sheet --}}
@foreach($lists as $section)
    <div class="sec">
        <div class="sec-head"><span class="sec-title">{{ $section->name }}</span></div>
        <div class="note">
            <strong>{{ $section->item_count }} {{ \Illuminate\Support\Str::plural('item', $section->item_count) }}</strong>
            covered by this certificate, listed individually on the attached continuation sheet.
        </div>
    </div>
@endforeach

{{-- Declaration --}}
@foreach($declaration as $section)
    <div class="sec">
        <div class="sec-head"><span class="sec-title">{{ $section->name }}</span></div>
        {{-- statements side by side, two per row, to keep the certificate on one page --}}
        <table class="decl">
            @foreach($section->fields->sortBy('display_order')->values()->chunk(2) as $pair)
                @php $pair = $pair->values(); @endphp
                <tr>
                    @if($pair->count() === 2)
                        @foreach($pair as $i => $field)
                            @if($i === 1)<td class="gap"></td>@endif
                            <td class="half"><div class="statement"><div class="lbl">{{ $field->field_label }}</div><div class="val-n">{{ $fmt($field, $valueOf($field)) }}</div></div></td>
                        @endforeach
                    @else
                        <td colspan="3"><div class="statement"><div class="lbl">{{ $pair[0]->field_label }}</div><div class="val-n">{{ $fmt($pair[0], $valueOf($pair[0])) }}</div></div></td>
                    @endif
                </tr>
            @endforeach
        </table>
    </div>
@endforeach

@if($certificate->remarks)
    <div class="sec">
        <div class="sec-head"><span class="sec-title">Remarks</span></div>
        <div class="statement"><div class="val-n">{{ $certificate->remarks }}</div></div>
    </div>
@endif


</body>
</html>
