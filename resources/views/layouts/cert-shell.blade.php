{{-- Glass shell used when CERTIFICATE_ONLY=true. Included from layouts/app.blade.php,
     so $slot / $header and @yield('content') come from the page being rendered. --}}
@php
    $user = auth()->user();
    $isAdmin = $user && $user->hasRole(['super-admin', 'admin']);
    // Settings may store the logo as "storage/uploads/x.jpg" or "uploads/x.jpg";
    // the storage-file route expects a path relative to storage/app/public.
    $logoPath = config('settings.company_logo')
        ? preg_replace('#^/?(public/)?storage/#', '', ltrim(config('settings.company_logo'), '/'))
        : null;
    $companyName = config('settings.company_name') ?: config('app.name', 'Certificates');
    $csTheme = in_array(config('settings.branding_theme'), ['lavender', 'ocean', 'sunset', 'graphite'], true)
        ? config('settings.branding_theme') : 'ocean';

    $nav = [
        ['Overview', 'admin.certificates.index', 'admin.certificates.*', true],
        ['Templates', 'admin.certificate-templates.index', 'admin.certificate-templates.*', true],
        ['Customers', 'admin.customers.index', 'admin.customers.*', true],
        ['Vessels', 'admin.vessels.index', 'admin.vessels.*', true],
        ['Equipment', 'admin.equipment.categories.index', 'admin.equipment.*', true],
        ['Users', 'admin.users.index', 'admin.users.*', $isAdmin],
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-cs-theme="{{ $csTheme }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Certificates · {{ $companyName }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Existing bundle: Alpine + Tailwind utilities used by the page bodies --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13"></script>

    {{-- Searchable dropdowns everywhere (Tom Select + auto-enhancer) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tom-select/2.3.1/css/tom-select.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tom-select/2.3.1/js/tom-select.complete.min.js"></script>
    <script src="{{ asset('js/cs-select.js') }}?v={{ @filemtime(public_path('js/cs-select.js')) }}"></script>

    <link rel="stylesheet" href="{{ asset('css/cert-theme.css') }}?v={{ @filemtime(public_path('css/cert-theme.css')) }}">

    {{-- Theme: saved choice ('color-theme', shared with the classic UI), else the OS setting.
         Runs before paint so there is no light/dark flash. --}}
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

    <div class="cs-frame">
        <header class="cs-top">
            <a href="{{ route('admin.certificates.index') }}" class="cs-logo" title="{{ $companyName }}">
                @if($logoPath)
                    <img src="{{ url('storage-file/' . ltrim($logoPath, '/')) }}" alt="{{ $companyName }}"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-flex';">
                    <span class="cs-logo__mark" style="display:none">{{ \Illuminate\Support\Str::of($companyName)->explode(' ')->take(2)->map(fn ($w) => mb_substr($w, 0, 1))->implode('') }}</span>
                @else
                    <span class="cs-logo__mark">{{ \Illuminate\Support\Str::of($companyName)->explode(' ')->take(2)->map(fn ($w) => mb_substr($w, 0, 1))->implode('') }}</span>
                    <span class="cs-logo__name">{{ $companyName }}</span>
                @endif
            </a>

            <nav class="cs-nav" aria-label="Main">
                @foreach($nav as [$label, $routeName, $pattern, $visible])
                    @if($visible && \Illuminate\Support\Facades\Route::has($routeName))
                        <a href="{{ route($routeName) }}" class="{{ request()->routeIs($pattern) ? 'is-active' : '' }}">{{ $label }}</a>
                    @endif
                @endforeach
            </nav>

            <div class="cs-actions">
                <a href="{{ route('admin.certificates.create') }}" class="cs-iconbtn" title="New certificate" aria-label="New certificate">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
                </a>
                <a href="{{ route('admin.certificates.index', ['tab' => 'issued']) }}#search" class="cs-iconbtn" title="Search certificates" aria-label="Search certificates">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="m20 20-3.5-3.5"/></svg>
                </a>
                <button type="button" class="cs-iconbtn" onclick="csToggleTheme()" title="Switch light / dark" aria-label="Switch light or dark mode">
                    <svg class="cs-theme-light" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8Z"/></svg>
                    <svg class="cs-theme-dark" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="4"/><path stroke-linecap="round" d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg>
                </button>
                @if($isAdmin && \Illuminate\Support\Facades\Route::has('admin.settings.index'))
                    <a href="{{ route('admin.settings.index') }}" class="cs-iconbtn" title="Settings" aria-label="Settings">
                        <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.3 4.3c.4-1.8 3-1.8 3.4 0a1.7 1.7 0 0 0 2.6 1.1c1.5-.9 3.3.8 2.4 2.4a1.7 1.7 0 0 0 1 2.5c1.8.5 1.8 3 0 3.4a1.7 1.7 0 0 0-1 2.6c.9 1.5-.9 3.3-2.4 2.4a1.7 1.7 0 0 0-2.6 1c-.4 1.8-3 1.8-3.4 0a1.7 1.7 0 0 0-2.6-1c-1.5.9-3.3-.9-2.4-2.4a1.7 1.7 0 0 0-1-2.6c-1.8-.4-1.8-3 0-3.4a1.7 1.7 0 0 0 1-2.5c-.9-1.6.9-3.3 2.4-2.4a1.7 1.7 0 0 0 2.6-1.1Z"/><circle cx="12" cy="12" r="3"/></svg>
                    </a>
                @endif

                <div class="cs-user" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
                    <button type="button" class="cs-user__btn" @click="open = !open" :aria-expanded="open" aria-label="Account menu">
                        <span class="cs-avatar">{{ mb_substr($user->name ?? 'U', 0, 2) }}</span>
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
                    </button>
                    <div class="cs-user__menu" x-show="open" x-transition.opacity.duration.120ms x-cloak>
                        <div class="cs-user__head">
                            <div class="cs-user__name">{{ $user->name }}</div>
                            <div class="cs-user__role">{{ $user->roles->first()?->name ?? 'User' }}</div>
                        </div>
                        <a href="{{ route('profile.edit') }}">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path stroke-linecap="round" d="M4 21a8 8 0 0 1 16 0"/></svg>
                            Profile &amp; password
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17l5-5-5-5M20 12H9M12 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7"/></svg>
                                Log out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main class="cs-main">
            <div class="cs-alerts">
                <x-alert-success />
                <x-alert-error />
            </div>

            @isset($header)
                <div class="cs-pagehead">{{ $header }}</div>
            @endisset

            {{ $slot ?? '' }}
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
