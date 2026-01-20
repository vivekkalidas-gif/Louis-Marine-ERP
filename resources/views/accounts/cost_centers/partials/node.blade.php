<li class="relative">
    <div class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700 dark:hover:bg-gray-700 group border border-transparent hover:border-gray-100 dark:hover:border-gray-600 transition-all duration-200" style="margin-left: {{ $level * 20 }}px">
        <div class="flex items-center gap-3">
             <span class="text-gray-400 dark:text-gray-500 dark:text-gray-400">
                @if($level == 0)
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                @else
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                @endif
            </span>
            <div>
                <span class="font-medium text-gray-900 dark:text-white">{{ $center->name }}</span>
                <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">({{ $center->code }})</span>
                @if($center->type == 'support')
                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">Support</span>
                @endif
            </div>
        </div>
        
        <div class="flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
            @if($center->manager)
                <span class="flex items-center gap-1">
                    <svg class="w-4 h-4"fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    {{ $center->manager->name }}
                </span>
            @endif
            <a href="#" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 opacity-0 group-hover:opacity-100 transition-opacity">Edit</a>
        </div>
    </div>
    
    @if($center->children->count() > 0)
        <ul class="mt-1 space-y-1">
            @foreach($center->children as $child)
                @include('accounts.cost_centers.partials.node', ['center' => $child, 'level' => $level + 1])
            @endforeach
        </ul>
    @endif
</li>
