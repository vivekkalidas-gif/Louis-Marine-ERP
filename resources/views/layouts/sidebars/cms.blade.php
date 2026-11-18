{{-- CMS Manager Sidebar for Super Admin & Admin --}}

<!-- Dashboard -->
<a href="{{ route('admin.cms.dashboard') }}" 
   :title="isCollapsed ? 'Dashboard' : ''"
   class="flex items-center px-3.5 py-2.5 text-xs font-semibold rounded-lg transition-all duration-150 
          {{ request()->routeIs('admin.cms.dashboard') 
             ? 'bg-brand-600 text-white shadow-md shadow-brand-600/20' 
             : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
    <svg class="w-4 h-4 mr-2.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
    </svg>
    <span x-show="!isCollapsed" class="truncate">Dashboard</span>
</a>

<!-- Services Management -->
<a href="{{ route('admin.cms.services.index') }}" 
   :title="isCollapsed ? 'Services' : ''"
   class="flex items-center px-3.5 py-2.5 text-xs font-semibold rounded-lg transition-all duration-150 
          {{ request()->routeIs('admin.cms.services.*') 
             ? 'bg-brand-600 text-white shadow-md shadow-brand-600/20' 
             : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
    <svg class="w-4 h-4 mr-2.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
    </svg>
    <span x-show="!isCollapsed" class="truncate">Services</span>
</a>

<!-- Website Products -->
<a href="{{ route('admin.cms.products.index') }}" 
   :title="isCollapsed ? 'Products' : ''"
   class="flex items-center px-3.5 py-2.5 text-xs font-semibold rounded-lg transition-all duration-150 
          {{ request()->routeIs('admin.cms.products.*') 
             ? 'bg-brand-600 text-white shadow-md shadow-brand-600/20' 
             : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
    <svg class="w-4 h-4 mr-2.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
    </svg>
    <span x-show="!isCollapsed" class="truncate">Products</span>
</a>

<!-- Pages Management -->
<a href="{{ route('admin.cms.pages.index') }}" 
   :title="isCollapsed ? 'Pages' : ''"
   class="flex items-center px-3.5 py-2.5 text-xs font-semibold rounded-lg transition-all duration-150 
          {{ request()->routeIs('admin.cms.pages.*') 
             ? 'bg-brand-600 text-white shadow-md shadow-brand-600/20' 
             : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
    <svg class="w-4 h-4 mr-2.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
    </svg>
    <span x-show="!isCollapsed" class="truncate">Pages</span>
</a>

<!-- Contact Submissions -->
<a href="{{ route('admin.cms.contact-submissions.index') }}" 
   :title="isCollapsed ? 'Contact Inbox' : ''"
   class="flex items-center px-3.5 py-2.5 text-xs font-semibold rounded-lg transition-all duration-150 
          {{ request()->routeIs('admin.cms.contact-submissions.*') 
             ? 'bg-brand-600 text-white shadow-md shadow-brand-600/20' 
             : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
    <svg class="w-4 h-4 mr-2.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
    </svg>
    <span x-show="!isCollapsed" class="truncate">Contact Inbox</span>
</a>

<!-- Partners -->
<a href="{{ route('admin.cms.partners.index') }}" 
   :title="isCollapsed ? 'Partners' : ''"
   class="flex items-center px-3.5 py-2.5 text-xs font-semibold rounded-lg transition-all duration-150 
          {{ request()->routeIs('admin.cms.partners.*') 
             ? 'bg-brand-600 text-white shadow-md shadow-brand-600/20' 
             : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
    <svg class="w-4 h-4 mr-2.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
    </svg>
    <span x-show="!isCollapsed" class="truncate">Partners</span>
</a>

<!-- Approvals & Certifications -->
<a href="{{ route('admin.cms.certifications.index') }}" 
   :title="isCollapsed ? 'Approvals' : ''"
   class="flex items-center px-3.5 py-2.5 text-xs font-semibold rounded-lg transition-all duration-150 
          {{ request()->routeIs('admin.cms.certifications.*') 
             ? 'bg-brand-600 text-white shadow-md shadow-brand-600/20' 
             : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
    <svg class="w-4 h-4 mr-2.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
    </svg>
    <span x-show="!isCollapsed" class="truncate">Approvals</span>
</a>

<!-- Access Control Matrix -->
<a href="{{ route('admin.roles.index') }}" 
   :title="isCollapsed ? 'Access Control Matrix' : ''"
   class="flex items-center px-3.5 py-2.5 text-xs font-semibold rounded-lg transition-all duration-150 
          {{ request()->routeIs('admin.roles.*') 
             ? 'bg-brand-600 text-white shadow-md shadow-brand-600/20 font-bold' 
             : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
    <svg class="w-4 h-4 mr-2.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
    </svg>
    <span x-show="!isCollapsed" class="truncate">Access Control Matrix</span>
</a>

<!-- User Accounts -->
<a href="{{ route('admin.users.index') }}" 
   :title="isCollapsed ? 'User Accounts' : ''"
   class="flex items-center px-3.5 py-2.5 text-xs font-semibold rounded-lg transition-all duration-150 
          {{ request()->routeIs('admin.users.*') 
             ? 'bg-brand-600 text-white shadow-md shadow-brand-600/20 font-bold' 
             : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
    <svg class="w-4 h-4 mr-2.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
    </svg>
    <span x-show="!isCollapsed" class="truncate">User Accounts</span>
</a>

<!-- Settings -->
<a href="{{ route('admin.settings.index') }}" 
   :title="isCollapsed ? 'System Settings' : ''"
   class="flex items-center px-3.5 py-2.5 text-xs font-semibold rounded-lg transition-all duration-150 
          {{ request()->routeIs('admin.settings.*') 
             ? 'bg-brand-600 text-white shadow-md shadow-brand-600/20' 
             : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
    <svg class="w-4 h-4 mr-2.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
    </svg>
    <span x-show="!isCollapsed" class="truncate">System Settings</span>
</a>

<!-- LANDING PAGE EDITOR GROUP -->
<div class="mt-4 pt-2 border-t border-slate-800" x-data="{ open: true }">
    <div class="flex items-center justify-between px-3.5 mb-2 text-[11px] font-bold text-slate-400 uppercase tracking-wider" x-show="!isCollapsed">
        <span>Landing Page Editor</span>
        <button @click="open = !open" type="button" class="text-slate-400 hover:text-white">
            <svg class="w-3.5 h-3.5 transition-transform" :class="{'rotate-180': !open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
    </div>
    
    <div x-show="open" x-transition class="space-y-0.5">
        <a href="{{ route('admin.cms.landing-page.hero-slides.index') }}" 
           :title="isCollapsed ? 'Hero Banners' : ''"
           class="flex items-center px-3.5 py-2 text-xs font-semibold rounded-lg transition-colors 
                  {{ request()->routeIs('admin.cms.landing-page.hero-slides.*') 
                     ? 'bg-brand-600 text-white shadow-sm' 
                     : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
            <svg class="w-4 h-4 mr-2.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <span x-show="!isCollapsed" class="truncate">Hero Banners</span>
        </a>

        <a href="{{ route('admin.cms.landing-page.features.index') }}" 
           :title="isCollapsed ? 'Features & Highlights' : ''"
           class="flex items-center px-3.5 py-2 text-xs font-semibold rounded-lg transition-colors 
                  {{ request()->routeIs('admin.cms.landing-page.features.*') 
                     ? 'bg-brand-600 text-white shadow-sm' 
                     : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
            <svg class="w-4 h-4 mr-2.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            <span x-show="!isCollapsed" class="truncate">Features & Highlights</span>
        </a>

        <a href="{{ route('admin.cms.landing-page.testimonials.index') }}" 
           :title="isCollapsed ? 'Testimonials' : ''"
           class="flex items-center px-3.5 py-2 text-xs font-semibold rounded-lg transition-colors 
                  {{ request()->routeIs('admin.cms.landing-page.testimonials.*') 
                     ? 'bg-brand-600 text-white shadow-sm' 
                     : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
            <svg class="w-4 h-4 mr-2.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            <span x-show="!isCollapsed" class="truncate">Testimonials</span>
        </a>

        <a href="{{ route('admin.cms.landing-page.stats.index') }}" 
           :title="isCollapsed ? 'Statistics' : ''"
           class="flex items-center px-3.5 py-2 text-xs font-semibold rounded-lg transition-colors 
                  {{ request()->routeIs('admin.cms.landing-page.stats.*') 
                     ? 'bg-brand-600 text-white shadow-sm' 
                     : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
            <svg class="w-4 h-4 mr-2.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 00-2 2h2a2 2 0 00-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            <span x-show="!isCollapsed" class="truncate">Statistics</span>
        </a>

        <a href="{{ route('admin.cms.landing-page.cta-sections.index') }}" 
           :title="isCollapsed ? 'CTA Banners' : ''"
           class="flex items-center px-3.5 py-2 text-xs font-semibold rounded-lg transition-colors 
                  {{ request()->routeIs('admin.cms.landing-page.cta-sections.*') 
                     ? 'bg-brand-600 text-white shadow-sm' 
                     : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
            <svg class="w-4 h-4 mr-2.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
            <span x-show="!isCollapsed" class="truncate">CTA Banners</span>
        </a>
    </div>
</div>

<!-- Divider -->
<div class="my-4 border-t border-slate-800"></div>

<!-- Website Preview -->
<a href="{{ route('home') }}" target="_blank"
   :title="isCollapsed ? 'View Public Website' : ''"
   class="flex items-center px-3.5 py-2.5 text-xs font-semibold rounded-lg text-slate-300 hover:text-white hover:bg-slate-800/70 transition-colors">
    <svg class="w-4 h-4 mr-2.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
    </svg>
    <span x-show="!isCollapsed" class="truncate">View Public Website</span>
</a>
