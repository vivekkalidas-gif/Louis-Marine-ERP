<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('View Enquiry') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $submission->subject }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Received: {{ $submission->created_at->format('F d, Y h:i A') }}</p>
                        </div>
                        <div>
                            <form action="{{ route('admin.cms.contact-submissions.update', $submission) }}" method="POST" class="flex items-center">
                                @csrf
                                @method('PUT')
                                <select name="status" onchange="this.form.submit()" class="mr-2 rounded border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="New" {{ $submission->status == 'New' ? 'selected' : '' }}>New</option>
                                    <option value="In Progress" {{ $submission->status == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="Closed" {{ $submission->status == 'Closed' ? 'selected' : '' }}>Closed</option>
                                </select>
                            </form>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h4 class="font-semibold text-gray-700 dark:text-gray-300 mb-2">Customer Details</h4>
                            <p><strong>Name:</strong> {{ $submission->name }}</p>
                            <p><strong>Email:</strong> <a href="mailto:{{ $submission->email }}" class="text-blue-600 hover:underline">{{ $submission->email }}</a></p>
                            <p><strong>Phone:</strong> {{ $submission->phone ?? 'N/A' }}</p>
                            <p><strong>Company:</strong> {{ $submission->company ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h4 class="font-semibold text-gray-700 dark:text-gray-300 mb-2">Enquiry Details</h4>
                            <p><strong>Service Interest:</strong> {{ $submission->service_type ?? 'General' }}</p>
                            <p><strong>IP Address:</strong> {{ $submission->ip_address }}</p>
                            <p><strong>Status:</strong> {{ $submission->status }}</p>
                        </div>
                    </div>

                    <div class="mb-8">
                        <h4 class="font-semibold text-gray-700 dark:text-gray-300 mb-2">Message</h4>
                        <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg whitespace-pre-wrap text-gray-800 dark:text-gray-200">
                            {{ $submission->message }}
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <a href="mailto:{{ $submission->email }}?subject=Re: {{ $submission->subject }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Reply via Email
                        </a>
                        <form action="{{ route('admin.cms.contact-submissions.destroy', $submission) }}" method="POST" onsubmit="return confirm('Delete this message completely?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:outline-none focus:border-red-700 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Delete Message
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
