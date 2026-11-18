<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-xl text-slate-900 dark:text-white leading-tight tracking-tight flex items-center gap-2">
                    <svg class="w-6 h-6 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span>Contact Submissions & Inbox</span>
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Manage incoming website inquiries, quote requests, and customer messages</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6 space-y-6">
        
        @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800/80 text-emerald-800 dark:text-emerald-200 text-xs font-semibold flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        </div>
        @endif

        <!-- Contact Inbox Table Card -->
        <div class="theme-card">
            <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-200 dark:border-slate-700/80 flex-wrap gap-3">
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <span>Messages & Enquiries</span>
                        <span class="badge-neutral text-[10px]">{{ $submissions->total() }} Inquiries</span>
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Received via website contact form</p>
                </div>
            </div>

            <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-800">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-900/60 border-b border-slate-200 dark:border-slate-800 font-bold text-slate-700 dark:text-slate-300">
                        <tr>
                            <th class="p-3.5">Date & Time</th>
                            <th class="p-3.5">Sender Info</th>
                            <th class="p-3.5">Subject</th>
                            <th class="p-3.5">Status</th>
                            <th class="p-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800 bg-white dark:bg-slate-900">
                        @forelse($submissions as $submission)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition {{ !$submission->read_at ? 'bg-brand-50/40 dark:bg-brand-950/20 font-semibold' : '' }}">
                                <td class="p-3.5 text-slate-500 dark:text-slate-400 whitespace-nowrap font-mono text-[11px]">
                                    {{ $submission->created_at->format('M d, Y H:i') }}
                                </td>
                                <td class="p-3.5">
                                    <strong class="font-bold text-slate-900 dark:text-white block text-xs">{{ $submission->name }}</strong>
                                    <span class="text-[11px] text-slate-500 font-mono">{{ $submission->email }}</span>
                                </td>
                                <td class="p-3.5 text-slate-800 dark:text-slate-200">
                                    {{ Str::limit($submission->subject, 40) }}
                                </td>
                                <td class="p-3.5">
                                    @if($submission->status === 'New')
                                        <span class="badge-brand text-[10px] font-bold">New</span>
                                    @elseif($submission->status === 'In Progress')
                                        <span class="badge-warning text-[10px] font-bold">In Progress</span>
                                    @else
                                        <span class="badge-emerald text-[10px] font-bold">Resolved</span>
                                    @endif
                                </td>
                                <td class="p-3.5 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.cms.contact-submissions.show', $submission) }}" class="btn-secondary btn-sm text-xs gap-1 font-bold">
                                            <svg class="w-3.5 h-3.5 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <span>View</span>
                                        </a>

                                        <form action="{{ route('admin.cms.contact-submissions.destroy', $submission) }}" method="POST" onsubmit="return confirm('Delete this message?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-ghost btn-sm text-xs p-1.5 text-rose-500 hover:text-rose-700" title="Delete Submission">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center bg-slate-50/50 dark:bg-slate-900/40">
                                    <strong class="block text-sm font-bold text-slate-800 dark:text-slate-200">No Messages Found</strong>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Incoming contact submissions will appear here.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($submissions->hasPages())
                <div class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-800">
                    {{ $submissions->links() }}
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
