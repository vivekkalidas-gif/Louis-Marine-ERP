{{-- Certificate dashboard for the glass shell (CERTIFICATE_ONLY=true).
     Uses the same $drafts / $issued collections as index.blade.php. --}}
@php
    $now = now();
    $hour = (int) $now->format('G');
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
    $firstName = \Illuminate\Support\Str::of(auth()->user()->name ?? '')->explode(' ')->first();

    $draftCount = $drafts->where('status', 'DRAFT')->count();
    $pendingCount = $drafts->where('status', 'PENDING')->count();
    $rejectedCount = $drafts->where('status', 'REJECTED')->count();
    $issuedCount = $issued->count();

    $isExpired = fn ($c) => $c->expiry_date && $c->expiry_date->lt($now->copy()->startOfDay());
    $expiredCount = $issued->filter($isExpired)->count();
    $validCount = $issuedCount - $expiredCount;
    $validPct = $issuedCount ? round($validCount / $issuedCount * 100, 1) : 0;

    $soonLimit = $now->copy()->addDays(30)->endOfDay();
    $expiringSoon = $issued
        ->filter(fn ($c) => $c->expiry_date && ! $isExpired($c) && $c->expiry_date->lte($soonLimit))
        ->sortBy(fn ($c) => $c->expiry_date->timestamp)
        ->values();

    // Issued per month, last 9 months (by issue date, falling back to creation date)
    $months = collect(range(8, 0))->map(fn ($i) => $now->copy()->startOfMonth()->subMonths($i));
    $perMonth = $months->map(function ($m) use ($issued) {
        return $issued->filter(function ($c) use ($m) {
            $d = $c->issue_date ?? $c->created_at;
            return $d && $d->format('Y-m') === $m->format('Y-m');
        })->count();
    });
    $chartMax = max(4, (int) ceil($perMonth->max() / 4) * 4);
    $thisMonth = $perMonth->last();
    $lastMonth = $perMonth->get($perMonth->count() - 2, 0);
    $delta = $lastMonth ? round(($thisMonth - $lastMonth) / $lastMonth * 100) : null;

    // Approval progress: share of open work that is waiting on approval
    $openCount = $drafts->count();
    $pendingPct = $openCount ? round($pendingCount / $openCount * 100) : 0;

    // Gauge geometry: 24 rounded segments on a 240° arc
    $segments = 24;
    $filled = (int) round($validPct / 100 * $segments);
    $gauge = [];
    for ($i = 0; $i < $segments; $i++) {
        $a = deg2rad(150 + $i * (240 / ($segments - 1)));
        $gauge[] = [
            'x1' => round(110 + 70 * cos($a), 2), 'y1' => round(105 + 70 * sin($a), 2),
            'x2' => round(110 + 92 * cos($a), 2), 'y2' => round(105 + 92 * sin($a), 2),
            'on' => $i < $filled,
            'o' => $i < $filled ? round(0.55 + 0.45 * ($i / max(1, $filled - 1)), 2) : 1,
        ];
    }

    $statusChip = fn ($s) => match ($s) {
        'PENDING' => 'cs-chip--amber',
        'REJECTED' => 'cs-chip--red',
        default => 'cs-chip--gray',
    };
    $initials = fn ($name) => \Illuminate\Support\Str::of($name ?: '?')->explode(' ')->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');
@endphp

<x-app-layout>
    <div x-data="{ tab: new URLSearchParams(location.search).get('tab') === 'issued' ? 'issued' : 'drafts', search: '' }"
         x-init="if (location.hash === '#search') { tab = 'issued'; $nextTick(() => $refs.search && $refs.search.focus()) }">

        <div class="cs-pagehead">
            <div>
                <h1 class="cs-hello">{{ $greeting }}, {{ $firstName }}!</h1>
                <p class="cs-sub">Here is where your certificates stand today, {{ $now->format('l, j F') }}.</p>
            </div>
            <a href="{{ route('admin.certificates.create') }}" class="cs-btn cs-btn--primary">
                <svg fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
                New certificate
            </a>
        </div>

        <div class="cs-grid">
            {{-- ================= Left column ================= --}}
            <div class="cs-col">
                <div class="cs-stats">
                    <a href="?tab=drafts" @click.prevent="tab = 'drafts'" class="cs-glass cs-stat">
                        <div class="cs-stat__icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8l-5-5Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M14 3v5h5M9 13h6M9 17h4"/></svg></div>
                        <div class="cs-stat__value">{{ $draftCount }}</div>
                        <div class="cs-stat__label">Drafts in<br>progress</div>
                    </a>
                    <a href="?tab=drafts" @click.prevent="tab = 'drafts'" class="cs-glass cs-stat">
                        <div class="cs-stat__icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 7v5l3 2"/></svg></div>
                        <div class="cs-stat__value">{{ $pendingCount }}</div>
                        <div class="cs-stat__label">Waiting for<br>approval</div>
                    </a>
                    <a href="?tab=issued" @click.prevent="tab = 'issued'" class="cs-glass cs-stat">
                        <div class="cs-stat__icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="9" r="6"/><path stroke-linecap="round" stroke-linejoin="round" d="m9.5 9 1.8 1.8L14.8 7.3M8.5 14.5 7 21l5-2.5 5 2.5-1.5-6.5"/></svg></div>
                        <div class="cs-stat__value">{{ $issuedCount }}</div>
                        <div class="cs-stat__label">Certificates<br>issued</div>
                    </a>
                    <a href="?tab=issued" @click.prevent="tab = 'issued'" class="cs-glass cs-stat">
                        <div class="cs-stat__icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="16" rx="3"/><path stroke-linecap="round" d="M8 3v4M16 3v4M3 10h18M12 14v3"/></svg></div>
                        <div class="cs-stat__value">{{ $expiringSoon->count() }}</div>
                        <div class="cs-stat__label">Expiring in<br>30 days</div>
                    </a>
                </div>

                {{-- Issued per month --}}
                <section class="cs-glass cs-card">
                    <div class="cs-card-head">
                        <div>
                            <h2 class="cs-card-title">Certificates issued</h2>
                            <p class="cs-card-sub">Monthly overview of the last 9 months</p>
                        </div>
                        <span class="cs-chip cs-chip--plain">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="16" rx="3"/><path stroke-linecap="round" d="M8 3v4M16 3v4M3 10h18"/></svg>
                            {{ $months->first()->format('M Y') }} – {{ $months->last()->format('M Y') }}
                        </span>
                    </div>

                    <div class="cs-kpis">
                        <div>
                            <div class="cs-kpi__label">This month</div>
                            <div class="cs-kpi__value">
                                {{ $thisMonth }}
                                @if(! is_null($delta))
                                    <span class="cs-badge {{ $delta >= 0 ? 'cs-badge--green' : 'cs-badge--red' }}">{{ $delta >= 0 ? '+' : '' }}{{ $delta }}%</span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <div class="cs-kpi__label">Last 9 months</div>
                            <div class="cs-kpi__value">{{ $perMonth->sum() }}</div>
                        </div>
                        <div>
                            <div class="cs-kpi__label">Rejected drafts</div>
                            <div class="cs-kpi__value">{{ $rejectedCount }}</div>
                        </div>
                    </div>

                    <div class="cs-chart">
                        <div class="cs-chart__axis">
                            @foreach([4, 3, 2, 1, 0] as $step)
                                <span>{{ (int) ($chartMax / 4 * $step) }}</span>
                            @endforeach
                        </div>
                        <div class="cs-chart__plot">
                            @foreach($perMonth as $i => $count)
                                @php $current = $i === $perMonth->count() - 1; @endphp
                                <div class="cs-bar {{ $current ? 'is-current' : '' }}" title="{{ $months[$i]->format('F Y') }}: {{ $count }} issued">
                                    @if($current)
                                        <span class="cs-bar__tip">{{ $count }} issued</span>
                                    @endif
                                    <div class="cs-bar__fill" style="height: {{ max(3, $count / $chartMax * 100) }}%"></div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="cs-chart__labels">
                        @foreach($months as $i => $m)
                            <span class="{{ $i === $months->count() - 1 ? 'is-current' : '' }}">{{ $m->format('M') }}</span>
                        @endforeach
                    </div>
                </section>

                {{-- Certificates table --}}
                <section class="cs-glass cs-card">
                    <div class="cs-card-head" style="flex-wrap: wrap; align-items: center;">
                        <div class="cs-tabs" role="tablist">
                            <button type="button" role="tab" :class="tab === 'drafts' && 'is-active'" @click="tab = 'drafts'">
                                Drafts &amp; pending <span class="cs-tabs__count">{{ $drafts->count() }}</span>
                            </button>
                            <button type="button" role="tab" :class="tab === 'issued' && 'is-active'" @click="tab = 'issued'">
                                Issued <span class="cs-tabs__count">{{ $issuedCount }}</span>
                            </button>
                        </div>
                        <label class="cs-search">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="m20 20-3.5-3.5"/></svg>
                            <input type="search" x-model="search" x-ref="search" placeholder="Search certificates…" aria-label="Search certificates">
                        </label>
                    </div>

                    {{-- Drafts --}}
                    <div class="cs-table-wrap" x-show="tab === 'drafts'">
                        <table class="cs-table">
                            <thead>
                                <tr><th>Vessel / customer</th><th>Type</th><th>Status</th><th>Updated</th><th class="ta-right">Actions</th></tr>
                            </thead>
                            <tbody>
                                @forelse($drafts as $cert)
                                    <tr x-show="!search || $el.textContent.toLowerCase().includes(search.toLowerCase())">
                                        <td>
                                            <div class="cs-who">
                                                <span class="cs-who__avatar">{{ $initials($cert->vessel->name ?? $cert->customer->name ?? '') }}</span>
                                                <div style="min-width:0">
                                                    <div class="cs-who__title">{{ $cert->vessel->name ?? 'No vessel' }}</div>
                                                    <div class="cs-who__meta">{{ $cert->customer->name ?? 'Unknown customer' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $cert->type?->name ?? $cert->certificateTemplate?->name ?? '—' }}</td>
                                        <td><span class="cs-chip {{ $statusChip($cert->status) }}">{{ ucfirst(strtolower($cert->status)) }}</span></td>
                                        <td class="cs-muted" title="{{ $cert->updated_at }}">{{ $cert->updated_at?->diffForHumans() }}</td>
                                        <td class="ta-right">
                                            <div class="cs-row-actions">
                                                <a href="{{ route('admin.certificates.show', $cert->id) }}" class="cs-btn cs-btn--light cs-btn--sm">Preview</a>
                                                <a href="{{ route('admin.certificates.edit', $cert->id) }}" class="cs-btn cs-btn--primary cs-btn--sm">Edit</a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="cs-empty">No drafts right now. <a href="{{ route('admin.certificates.create') }}" style="color: var(--cs-accent-strong); font-weight: 600;">Create a certificate</a></td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Issued --}}
                    <div class="cs-table-wrap" x-show="tab === 'issued'" x-cloak>
                        <table class="cs-table">
                            <thead>
                                <tr><th>Certificate</th><th>Vessel / customer</th><th>Issued</th><th>Expiry</th><th class="ta-right">Actions</th></tr>
                            </thead>
                            <tbody>
                                @forelse($issued as $cert)
                                    @php
                                        $exp = $cert->expiry_date;
                                        [$expClass, $expText] = ! $exp ? ['cs-chip--gray', 'No expiry']
                                            : ($isExpired($cert) ? ['cs-chip--red', 'Expired']
                                            : ($exp->lte($soonLimit) ? ['cs-chip--amber', 'Expires soon'] : ['cs-chip--green', 'Valid']));
                                    @endphp
                                    <tr x-show="!search || $el.textContent.toLowerCase().includes(search.toLowerCase())">
                                        <td>
                                            <div class="cs-mono">{{ $cert->certificate_number ?? '—' }}</div>
                                            <div class="cs-who__meta">{{ $cert->type?->name ?? $cert->certificateTemplate?->name ?? 'Certificate' }}</div>
                                        </td>
                                        <td>
                                            <div class="cs-who">
                                                <span class="cs-who__avatar">{{ $initials($cert->vessel->name ?? $cert->customer->name ?? '') }}</span>
                                                <div style="min-width:0">
                                                    <div class="cs-who__title">{{ $cert->vessel->name ?? 'No vessel' }}</div>
                                                    <div class="cs-who__meta">{{ $cert->customer->name ?? 'Unknown customer' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="cs-muted">{{ $cert->issue_date ? $cert->issue_date->format('d M Y') : '—' }}</td>
                                        <td>
                                            <div style="display:flex; flex-direction:column; gap:4px; align-items:flex-start">
                                                <span class="cs-chip {{ $expClass }}">{{ $expText }}</span>
                                                @if($exp)<span class="cs-who__meta">{{ $exp->format('d M Y') }}</span>@endif
                                            </div>
                                        </td>
                                        <td class="ta-right">
                                            <div class="cs-row-actions">
                                                <a href="{{ route('admin.certificates.show', $cert->id) }}" class="cs-btn cs-btn--light cs-btn--sm">View</a>
                                                <button type="button" class="cs-btn cs-btn--primary cs-btn--sm"
                                                        @click="$dispatch('open-modal', 'confirm-renew-modal'); $dispatch('set-renew-action', '{{ route('admin.certificates.renew', $cert->id) }}')">
                                                    Renew
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="cs-empty">No issued certificates yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>

            {{-- ================= Right column ================= --}}
            <div class="cs-col">
                <section class="cs-glass cs-card">
                    <div class="cs-card-head">
                        <div>
                            <h2 class="cs-card-title">Approval queue</h2>
                            <p class="cs-card-sub">{{ $pendingCount }} of {{ $openCount }} open {{ \Illuminate\Support\Str::plural('certificate', $openCount) }} awaiting sign-off</p>
                        </div>
                        <a href="?tab=drafts" @click.prevent="tab = 'drafts'" class="cs-iconbtn cs-iconbtn--sm" aria-label="Show drafts">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 17 17 7M9 7h8v8"/></svg>
                        </a>
                    </div>
                    <div class="cs-progress"><div class="cs-progress__fill" style="width: {{ $pendingPct }}%"></div></div>
                    <div style="display:flex; justify-content:space-between; font-size:12px; color:var(--cs-muted)">
                        <span>{{ $draftCount }} draft</span><span>{{ $pendingCount }} pending</span><span>{{ $rejectedCount }} rejected</span>
                    </div>
                    <a href="{{ route('admin.certificates.create') }}" class="cs-btn cs-btn--light" style="width:100%; margin-top:18px">Start a new certificate</a>
                </section>

                <section class="cs-glass cs-card">
                    <div class="cs-card-head">
                        <div>
                            <h2 class="cs-card-title">Validity rate</h2>
                            <p class="cs-card-sub">Issued certificates still in date</p>
                        </div>
                        <a href="?tab=issued" @click.prevent="tab = 'issued'" class="cs-iconbtn cs-iconbtn--sm" aria-label="Show issued">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 17 17 7M9 7h8v8"/></svg>
                        </a>
                    </div>
                    <div class="cs-gauge">
                        <svg viewBox="0 0 220 190" aria-hidden="true">
                            @foreach($gauge as $s)
                                <line x1="{{ $s['x1'] }}" y1="{{ $s['y1'] }}" x2="{{ $s['x2'] }}" y2="{{ $s['y2'] }}"
                                      class="{{ $s['on'] ? 'cs-seg-on' : 'cs-seg-off' }}" stroke-opacity="{{ $s['o'] }}"
                                      stroke-width="10" stroke-linecap="round"/>
                            @endforeach
                        </svg>
                        <div class="cs-gauge__center">
                            <div class="cs-gauge__value">{{ $issuedCount ? rtrim(rtrim(number_format($validPct, 1), '0'), '.') : '—' }}{{ $issuedCount ? '%' : '' }}</div>
                            <div class="cs-gauge__hint">{{ $issuedCount ? 'valid' : 'nothing issued yet' }}</div>
                        </div>
                    </div>
                    <div class="cs-split">
                        <div><div class="cs-split__label">Valid</div><div class="cs-split__value">{{ $validCount }}</div></div>
                        <div><div class="cs-split__label">Expired</div><div class="cs-split__value">{{ $expiredCount }}</div></div>
                    </div>
                </section>

                <section class="cs-glass cs-card">
                    <h2 class="cs-card-title">Expiring soon</h2>
                    <p class="cs-card-sub">Next 30 days</p>
                    @if($expiringSoon->isEmpty())
                        <p class="cs-empty">Nothing expires in the next 30 days.</p>
                    @else
                        <ul class="cs-list">
                            @foreach($expiringSoon->take(6) as $cert)
                                <li>
                                    <div class="cs-list__main">
                                        <div class="cs-list__title">{{ $cert->vessel->name ?? $cert->customer->name ?? 'Certificate' }}</div>
                                        <div class="cs-list__meta">{{ $cert->certificate_number }} · {{ $cert->expiry_date->format('d M') }}</div>
                                    </div>
                                    <span class="cs-chip cs-chip--amber cs-chip--plain">{{ (int) $now->copy()->startOfDay()->diffInDays($cert->expiry_date->copy()->startOfDay()) }}d</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </section>
            </div>
        </div>
    </div>

    {{-- Renew confirmation (same behaviour as the classic view) --}}
    <x-modal name="confirm-renew-modal" focusable>
        <form method="POST" x-data="{ action: '' }" @set-renew-action.window="action = $event.detail" :action="action" class="p-6" style="font-family: var(--cs-font)">
            @csrf
            <h2 class="cs-card-title">Renew certificate</h2>
            <p class="cs-card-sub" style="font-size:13.5px; margin-top:8px">A new draft will be created with all details pre-filled.</p>
            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:22px">
                <button type="button" class="cs-btn cs-btn--light" x-on:click="$dispatch('close')">Cancel</button>
                <button type="submit" class="cs-btn cs-btn--primary">Renew certificate</button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
