<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $certificate->certificate_number }} – continuation</title>
    @include('admin.certificates.pdf._styles')
</head>
<body>
@php extract(require resource_path('views/admin/certificates/pdf/_brand.php')); @endphp
@include('admin.certificates.pdf._frame', ['continuation' => true])

@php
    $template = $certificate->certificateTemplate;
    $values = $certificate->fieldValues;
    $sections = $template ? $template->sections->where('is_repeatable', true)->sortBy('display_order') : collect();
@endphp

<div class="title-wrap" style="margin-bottom: 3mm;">
    <div class="title-kicker">CONTINUATION SHEET</div>
    <div class="title" style="font-size: 14pt;">{{ $template->title ?? 'Certificate' }}</div>
    <div class="title-rule"></div>
    <div class="certify">
        Vessel: <strong>{{ $certificate->vessel->name ?? '—' }}</strong>
        &nbsp;·&nbsp; Customer: <strong>{{ $certificate->customer->company_name ?? '—' }}</strong>
        &nbsp;·&nbsp; Job: <strong>{{ $certificate->job_number ?: ($certificate->job->job_number ?? '—') }}</strong>
    </div>
</div>

@foreach($sections as $section)
    @php
        $fields = $section->fields->sortBy('display_order')->values();
        $ids = $fields->pluck('id')->all();
        $rows = $values->filter(fn ($v) => in_array($v->certificate_template_field_id, $ids))
            ->groupBy(fn ($v) => (int) ($v->section_instance ?? 1))
            ->sortKeys();
    @endphp
    @if($rows->count())
        <div class="sec">
            <div class="sec-head">
                <span class="sec-title">{{ $section->name }}</span>
                <span class="sec-desc">&nbsp;·&nbsp;{{ $rows->count() }} {{ \Illuminate\Support\Str::plural('item', $rows->count()) }}</span>
            </div>
            <table class="register">
                <thead>
                    <tr>
                        <th class="idx">#</th>
                        @foreach($fields as $field)<th>{{ $field->field_label }}</th>@endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $instance => $cells)
                        <tr class="{{ $loop->even ? 'alt' : '' }}">
                            <td class="idx">{{ $loop->iteration }}</td>
                            @foreach($fields as $field)
                                <td>{{ $fmt($field, optional($cells->firstWhere('certificate_template_field_id', $field->id))->value) }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endforeach
</body>
</html>
