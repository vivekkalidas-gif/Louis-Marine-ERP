<x-layouts.frontend title="Contact Us | Louis Marine Qatar">
    <!-- Page Header -->
    <div class="bg-[#0b1c3c] py-16 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 bg-[url('https://louismarineqatar.com/wp-content/uploads/2024/11/banner1-1.jpg')] bg-cover bg-center"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <h1 class="text-4xl font-extrabold text-white sm:text-5xl md:text-6xl">Contact Us</h1>
            <p class="mt-4 text-xl text-gray-300 max-w-3xl mx-auto font-light">
                Get in touch with our team for enquiries, support, or to request a quote.
            </p>
        </div>
    </div>

    <!-- Contact Content -->
    <div class="py-16 bg-white dark:bg-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
                <!-- Contact Form -->
                <div>
                    <h2 class="text-2xl font-bold text-[#0b1c3c] mb-6 border-l-4 border-red-600 pl-4">Send us a Message</h2>
                    
                    @if(session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                            <p class="font-bold">Success!</p>
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif

                    <form action="{{ route('contact.submit') }}" method="POST" class="space-y-6">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Your Name</label>
                                <input type="text" name="name" id="name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 bg-gray-50 dark:bg-gray-700 p-3">
                            </div>
                            <div>
                                <label for="company" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Company Name</label>
                                <input type="text" name="company" id="company" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 bg-gray-50 dark:bg-gray-700 p-3">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="email" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Email Address</label>
                                <input type="email" name="email" id="email" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 bg-gray-50 dark:bg-gray-700 p-3">
                            </div>
                            <div>
                                <label for="phone" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Phone Number</label>
                                <input type="text" name="phone" id="phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 bg-gray-50 dark:bg-gray-700 p-3">
                            </div>
                        </div>

                        <div>
                            <label for="service_type" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Interested Service</label>
                            <select name="service_type" id="service_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 bg-gray-50 dark:bg-gray-700 p-3">
                                <option value="">Select a Service...</option>
                                <option value="Marine Surveying" {{ request('service') == 'Marine Surveying' ? 'selected' : '' }}>Marine Surveying</option>
                                <option value="Fire Safety Inspection" {{ request('service') == 'Fire Safety Inspection' ? 'selected' : '' }}>Fire Safety Inspection</option>
                                <option value="Hull Engineering" {{ request('service') == 'Hull Engineering' ? 'selected' : '' }}>Hull Engineering</option>
                                <option value="Vessel Certification" {{ request('service') == 'Vessel Certification' ? 'selected' : '' }}>Vessel Certification</option>
                                <option value="Maintenance Contract" {{ request('service') == 'Maintenance Contract' ? 'selected' : '' }}>Annual Maintenance Contract</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div>
                            <label for="subject" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Subject</label>
                            <input type="text" name="subject" id="subject" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 bg-gray-50 dark:bg-gray-700 p-3">
                        </div>

                        <div>
                            <label for="message" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Message</label>
                            <textarea name="message" id="message" rows="4" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 bg-gray-50 dark:bg-gray-700 p-3"></textarea>
                        </div>

                        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-4 px-4 rounded shadow-lg transition duration-300 transform hover:-translate-y-1">
                            SEND MESSAGE
                        </button>
                    </form>
                </div>

                <!-- Contact Info -->
                <div class="space-y-8">
                    <div class="bg-gray-50 dark:bg-gray-700 p-8 rounded-xl shadow-md border-t-4 border-[#0b1c3c]">
                        <h2 class="text-2xl font-bold text-[#0b1c3c] mb-6">Contact Information</h2>
                        
                        <div class="space-y-6">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 bg-white dark:bg-gray-800 p-3 rounded-full shadow-sm">
                                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                </div>
                                <div class="ml-4">
                                    <p class="font-bold text-[#0b1c3c] uppercase text-sm tracking-wide">Headquarters</p>
                                    <p class="text-gray-600 mt-1 whitespace-pre-line">{{ $globalSettings['company_address'] ?? 'P.O. Box: 9864, Street #24, Industrial Area, Doha, Qatar' }}</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                 <div class="flex-shrink-0 bg-white dark:bg-gray-800 p-3 rounded-full shadow-sm">
                                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                </div>
                                <div class="ml-4">
                                    <p class="font-bold text-[#0b1c3c] uppercase text-sm tracking-wide">Phone</p>
                                    <p class="text-gray-600 mt-1 font-mono">{{ $globalSettings['company_phone'] ?? '+974 4460 3388' }}</p>
                                    <p class="text-sm text-gray-400 mt-1">Mon-Sat 8am to 6pm</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="flex-shrink-0 bg-white dark:bg-gray-800 p-3 rounded-full shadow-sm">
                                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                </div>
                                <div class="ml-4">
                                    <p class="font-bold text-[#0b1c3c] uppercase text-sm tracking-wide">Email</p>
                                    <p class="text-gray-600 mt-1">{{ $globalSettings['company_email'] ?? 'info@louismarineqatar.com' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Map Embed -->
                    <div class="rounded-xl overflow-hidden h-80 shadow-lg border-2 border-gray-100 relative">
                        <iframe src="{{ $globalSettings['google_maps_embed_url'] ?? 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d230888.75010660317!2d51.36531393685952!3d25.285447293587635!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e45c534ffdce87f%3A0x44d9319f78cfd4b1!2sDoha%2C%20Qatar!5e0!3m2!1sen!2s!4v1645437891234!5m2!1sen!2s' }}" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/50 to-transparent h-12 pointer-events-none"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.frontend>
