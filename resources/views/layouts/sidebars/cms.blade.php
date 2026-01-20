{{-- CMS Manager Sidebar for Super Admin --}}

<!-- Dashboard -->
<a href="{{ route('admin.cms.dashboard') }}" 
   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-150 
          {{ request()->routeIs('admin.cms.dashboard') 
             ? 'bg-marine-600 text-white shadow-md' 
             : 'text-gray-700 dark:text-gray-300 hover:bg-marine-100 dark:hover:bg-marine-800' }}">
    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
    </svg>
    <span>Dashboard</span>
</a>

<!-- Services Management -->
<a href="{{ route('admin.cms.services.index') }}" 
   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-150 
          {{ request()->routeIs('admin.cms.services.*') 
             ? 'bg-marine-600 text-white shadow-md' 
             : 'text-gray-700 dark:text-gray-300 hover:bg-marine-100 dark:hover:bg-marine-800' }}">
    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
    </svg>
    <span>Services</span>
</a>

<!-- Website Products -->
<a href="{{ route('admin.cms.products.index') }}" 
   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-150 
          {{ request()->routeIs('admin.cms.products.*') 
             ? 'bg-marine-600 text-white shadow-md' 
             : 'text-gray-700 dark:text-gray-300 hover:bg-marine-100 dark:hover:bg-marine-800' }}">
    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
    </svg>
    <span>Products</span>
</a>

<!-- Pages Management -->
<a href="{{ route('admin.cms.pages.index') }}" 
   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-150 
          {{ request()->routeIs('admin.cms.pages.*') 
             ? 'bg-marine-600 text-white shadow-md' 
             : 'text-gray-700 dark:text-gray-300 hover:bg-marine-100 dark:hover:bg-marine-800' }}">
    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
    </svg>
    <span>Pages</span>
</a>

<!-- Contact Submissions -->
<a href="{{ route('admin.cms.contact-submissions.index') }}" 
   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-150 
          {{ request()->routeIs('admin.cms.contact-submissions.*') 
             ? 'bg-marine-600 text-white shadow-md' 
             : 'text-gray-700 dark:text-gray-300 hover:bg-marine-100 dark:hover:bg-marine-800' }}">
    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
    </svg>
    <span>Contact Inbox</span>
</a>

<!-- Partners -->
<a href="{{ route('admin.cms.partners.index') }}" 
   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-150 
          {{ request()->routeIs('admin.cms.partners.*') 
             ? 'bg-marine-600 text-white shadow-md' 
             : 'text-gray-700 dark:text-gray-300 hover:bg-marine-100 dark:hover:bg-marine-800' }}">
    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
    </svg>
    <span>Partners</span>
</a>

<!-- Certifications -->
<a href="{{ route('admin.cms.certifications.index') }}" 
   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-150 
          {{ request()->routeIs('admin.cms.certifications.*') 
             ? 'bg-marine-600 text-white shadow-md' 
             : 'text-gray-700 dark:text-gray-300 hover:bg-marine-100 dark:hover:bg-marine-800' }}">
    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
    </svg>
    <span>Certifications</span>
</a>

<!-- Settings -->
<a href="{{ route('admin.settings.index') }}" 
   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-150 
          {{ request()->routeIs('admin.settings.*') 
             ? 'bg-marine-600 text-white shadow-md' 
             : 'text-gray-700 dark:text-gray-300 hover:bg-marine-100 dark:hover:bg-marine-800' }}">
    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
    </svg>
    <span>System Settings</span>
</a>

<!-- Security Group -->
<div x-data="{ open: {{ request()->routeIs(['admin.users.*', 'admin.roles.*', 'admin.backups.*', 'admin.activity-logs.*']) ? 'true' : 'false' }} }">
    <button @click="open = !open" 
       class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-150 
              {{ request()->routeIs(['admin.users.*', 'admin.roles.*', 'admin.backups.*', 'admin.activity-logs.*'])
                 ? 'bg-marine-600 text-white shadow-md' 
                 : 'text-gray-700 dark:text-gray-300 hover:bg-marine-100 dark:hover:bg-marine-800' }}">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
            <span>Security</span>
        </div>
        <svg class="w-4 h-4 transition-transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>
    
    <div x-show="open" x-transition class="ml-4 mt-2 space-y-1">
        <a href="{{ route('admin.backups.index') }}" 
           class="flex items-center px-4 py-2 text-sm rounded-lg transition-colors duration-150 
                  {{ request()->routeIs('admin.backups.*') 
                     ? 'bg-marine-500 text-white' 
                     : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
            Backups
        </a>
        <a href="{{ route('admin.users.index') }}" 
           class="flex items-center px-4 py-2 text-sm rounded-lg transition-colors duration-150 
                  {{ request()->routeIs('admin.users.*') 
                     ? 'bg-marine-500 text-white' 
                     : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
            Users
        </a>
        <a href="{{ route('admin.roles.index') }}" 
           class="flex items-center px-4 py-2 text-sm rounded-lg transition-colors duration-150 
                  {{ request()->routeIs('admin.roles.*') 
                     ? 'bg-marine-500 text-white' 
                     : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
            Roles & Permissions
        </a>
        <a href="{{ route('admin.activity-logs.index') }}" 
           class="flex items-center px-4 py-2 text-sm rounded-lg transition-colors duration-150 
                  {{ request()->routeIs('admin.activity-logs.*') 
                     ? 'bg-marine-500 text-white' 
                     : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
            Activity Logs
        </a>
    </div>
</div>

{{-- Landing Page Editor --}}
<div x-data="{ open: {{ request()->routeIs('admin.cms.landing-page.*') ? 'true' : 'false' }} }">
    <button @click="open = !open" 
       class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-150 
              {{ request()->routeIs('admin.cms.landing-page.*') 
                 ? 'bg-marine-600 text-white shadow-md' 
                 : 'text-gray-700 dark:text-gray-300 hover:bg-marine-100 dark:hover:bg-marine-800' }}">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
            </svg>
            <span>Landing Page Editor</span>
        </div>
        <svg class="w-4 h-4 transition-transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>
    
    <div x-show="open" x-transition class="ml-4 mt-2 space-y-1">
        <a href="{{ route('admin.cms.landing-page.hero-slides.index') }}" 
           class="flex items-center px-4 py-2 text-sm rounded-lg transition-colors duration-150 
                  {{ request()->routeIs('admin.cms.landing-page.hero-slides.*') 
                     ? 'bg-marine-500 text-white' 
                     : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
            Hero Banners
        </a>
        <a href="{{ route('admin.cms.landing-page.features.index') }}" 
           class="flex items-center px-4 py-2 text-sm rounded-lg transition-colors duration-150 
                  {{ request()->routeIs('admin.cms.landing-page.features.*') 
                     ? 'bg-marine-500 text-white' 
                     : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
            Features
        </a>
        <a href="{{ route('admin.cms.landing-page.testimonials.index') }}" 
           class="flex items-center px-4 py-2 text-sm rounded-lg transition-colors duration-150 
                  {{ request()->routeIs('admin.cms.landing-page.testimonials.*') 
                     ? 'bg-marine-500 text-white' 
                     : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
            Testimonials
        </a>
        <a href="{{ route('admin.cms.landing-page.stats.index') }}" 
           class="flex items-center px-4 py-2 text-sm rounded-lg transition-colors duration-150 
                  {{ request()->routeIs('admin.cms.landing-page.stats.*') 
                     ? 'bg-marine-500 text-white' 
                     : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
            Statistics
        </a>
        <a href="{{ route('admin.cms.landing-page.cta-sections.index') }}" 
           class="flex items-center px-4 py-2 text-sm rounded-lg transition-colors duration-150 
                  {{ request()->routeIs('admin.cms.landing-page.cta-sections.*') 
                     ? 'bg-marine-500 text-white' 
                     : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
            CTA Sections
        </a>
    </div>
</div>

<!-- Divider -->
<div class="my-4 border-t border-gray-200 dark:border-gray-700"></div>

<!-- Website Preview -->
<a href="{{ route('home') }}" target="_blank"
   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-150 text-gray-700 dark:text-gray-300 hover:bg-marine-100 dark:hover:bg-marine-800">
    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
    </svg>
    <span>View Website</span>
</a>
