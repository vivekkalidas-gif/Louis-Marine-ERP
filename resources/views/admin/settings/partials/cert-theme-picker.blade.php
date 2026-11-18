{{-- Colour theme picker for the certificate manager (glass shell).
     Saves as setting "branding_theme"; clicking a card previews it live. --}}
@php
    $current = in_array(config('settings.branding_theme'), ['lavender', 'ocean', 'sunset', 'graphite'], true) ? config('settings.branding_theme') : 'ocean';
    $themes = [
        'lavender' => ['Lavender Glass', 'Soft violet with pink and blue haze', ['#f6c4c8', '#d6c4f0', '#cee2f2'], ['#9a8cf8', '#7a68f0']],
        'ocean'    => ['Ocean Breeze', 'Default · teal and aqua, calm marine feel', ['#b0e8de', '#b8d6f6', '#c8ecf6'], ['#43c6cf', '#1d8fb9']],
        'sunset'   => ['Sunset Coral', 'Warm coral, peach and rose', ['#fac8b4', '#f0c8dc', '#fde2be'], ['#ffa27e', '#ec6a53']],
        'graphite' => ['Graphite', 'Neutral monochrome, business-like', ['#d6dae2', '#e2e4ec', '#d0d8e2'], ['#545b6e', '#232733']],
    ];
@endphp
<div class="md:col-span-2">
    <label class="cs-label" style="font-size:14px">Colour theme</label>
    <p class="cs-help" style="margin-top:0">Applies to everyone. Each theme has a light and dark version; people switch light/dark with the moon button.</p>
    <div class="cs-theme-grid">
        @foreach($themes as $key => [$name, $desc, $blobs, $btn])
            <label class="cs-theme-card">
                <input type="radio" name="branding_theme" value="{{ $key }}" @checked($current === $key)
                       onchange="document.documentElement.setAttribute('data-cs-theme', this.value)">
                <div class="cs-theme-card__swatch"
                     style="background: radial-gradient(70% 90% at 0% 100%, {{ $blobs[0] }} 0%, transparent 70%), radial-gradient(60% 80% at 50% 60%, {{ $blobs[1] }} 0%, transparent 70%), radial-gradient(70% 90% at 100% 0%, {{ $blobs[2] }} 0%, transparent 70%), #f5f5f8;">
                    <span style="left:12px; width:46%; background: linear-gradient(135deg, {{ $btn[0] }}, {{ $btn[1] }});"></span>
                    <span style="left:calc(46% + 20px); width:22%; background: rgba(255,255,255,.9);"></span>
                </div>
                <div class="cs-theme-card__name">
                    {{ $name }}
                    <span class="cs-theme-card__tick"><svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m5 12 5 5 9-10"/></svg></span>
                </div>
                <p class="cs-theme-card__desc">{{ $desc }}</p>
            </label>
        @endforeach
    </div>
</div>
