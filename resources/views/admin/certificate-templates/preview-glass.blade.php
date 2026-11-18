{{-- Template preview for the glass theme (CERTIFICATE_ONLY=true). Standalone page,
     same data as preview.blade.php: $certificateTemplate with sections.fields. --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      data-cs-theme="{{ in_array(config('settings.branding_theme'), ['lavender', 'ocean', 'sunset', 'graphite'], true) ? config('settings.branding_theme') : 'ocean' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $certificateTemplate->name }} · Preview</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/cert-theme.css') }}?v={{ @filemtime(public_path('css/cert-theme.css')) }}">
    <script>
        (function () {
            var saved = null;
            try { saved = localStorage.getItem('color-theme'); } catch (e) {}
            var dark = saved ? saved === 'dark' : window.matchMedia('(prefers-color-scheme: dark)').matches;
            document.documentElement.classList.toggle('dark', dark);
        })();
        function csToggleTheme() {
            var dark = !document.documentElement.classList.contains('dark');
            document.documentElement.classList.toggle('dark', dark);
            try { localStorage.setItem('color-theme', dark ? 'dark' : 'light'); } catch (e) {}
        }
    </script>
</head>
<body class="cs-body">
    <div class="cs-bg" aria-hidden="true"></div>

    <div class="cs-frame cs-narrow">
        <section class="cs-glass cs-card" style="margin-bottom: 22px">
            <div class="cs-card-head" style="align-items: center">
                <div>
                    <h1 class="cs-hello">{{ $certificateTemplate->title ?: $certificateTemplate->name }}</h1>
                    <p class="cs-sub">Template preview · {{ $certificateTemplate->name }}</p>
                </div>
                <div class="cs-actions">
                    <button type="button" class="cs-iconbtn" onclick="csToggleTheme()" title="Switch light / dark" aria-label="Switch light or dark mode">
                        <svg class="cs-theme-light" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8Z"/></svg>
                        <svg class="cs-theme-dark" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="4"/><path stroke-linecap="round" d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg>
                    </button>
                    <a href="{{ route('admin.certificate-templates.show', $certificateTemplate) }}" class="cs-btn cs-btn--light"
                       onclick="if (window.opener) { window.close(); return false; }">Close</a>
                </div>
            </div>
            <div class="cs-note">This is how the certificate form will look. Real certificates fill these fields with job data.</div>
        </section>

        <section class="cs-glass cs-card" style="padding: 28px">
            @forelse($certificateTemplate->sections as $section)
                <div class="cs-section">
                    <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap">
                        <h2 class="cs-section__title">{{ $section->name }}</h2>
                        @if($section->is_repeatable)
                            <span class="cs-chip cs-chip--plain">Repeatable</span>
                        @endif
                    </div>
                    @if($section->description)
                        <p class="cs-section__desc">{{ $section->description }}</p>
                    @endif

                    @if($section->layout_type === 'table' && $section->is_repeatable)
                        <div class="cs-table-wrap">
                            <table class="cs-preview-table">
                                <thead>
                                    <tr>
                                        @foreach($section->fields as $field)
                                            <th>{{ $field->field_label }}@if($field->is_required)<span class="cs-req">*</span>@endif</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        @foreach($section->fields as $field)
                                            <td>{{ ucfirst($field->field_type) }}</td>
                                        @endforeach
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="cs-form-grid {{ $section->layout_type === 'two_column' ? '' : 'is-single' }}">
                            @foreach($section->fields as $field)
                                <div class="{{ $field->width === 'full' ? 'is-full' : '' }}">
                                    <label class="cs-label">
                                        {{ $field->field_label }}@if($field->is_required)<span class="cs-req">*</span>@endif
                                    </label>

                                    @if($field->field_type === 'textarea')
                                        <textarea class="cs-input" rows="3" placeholder="{{ $field->placeholder }}"></textarea>
                                    @elseif($field->field_type === 'dropdown')
                                        <select class="cs-input">
                                            <option value="">Select…</option>
                                            @foreach(($field->field_options['options'] ?? []) as $option)
                                                <option>{{ $option }}</option>
                                            @endforeach
                                        </select>
                                    @elseif($field->field_type === 'checkbox')
                                        <label class="cs-check"><input type="checkbox"> {{ $field->placeholder }}</label>
                                    @elseif($field->field_type === 'radio')
                                        @foreach(($field->field_options['options'] ?? []) as $option)
                                            <label class="cs-check"><input type="radio" name="{{ $field->field_name }}"> {{ $option }}</label>
                                        @endforeach
                                    @else
                                        <input type="{{ in_array($field->field_type, ['text', 'number', 'date', 'email', 'tel', 'time', 'url']) ? $field->field_type : 'text' }}"
                                               class="cs-input" placeholder="{{ $field->placeholder }}">
                                    @endif

                                    @if($field->help_text)
                                        <p class="cs-help">{{ $field->help_text }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @empty
                <div class="cs-empty" style="padding: 40px 0">
                    <div style="font-size:16px; font-weight:600; color:var(--cs-ink)">No sections yet</div>
                    Add sections and fields to the template to see the preview.
                </div>
            @endforelse
        </section>
    </div>
</body>
</html>
