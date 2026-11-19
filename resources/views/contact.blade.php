<x-layouts.site title="Contact">
    <section class="mx-auto grid max-w-6xl gap-12 px-4 py-12 md:grid-cols-5">
        <div class="md:col-span-2">
            <h1 class="text-3xl font-bold text-stone-900 md:text-4xl">Let's talk about your website</h1>
            <p class="mt-4 text-stone-600">Tell us what you need. We usually reply within one business day.</p>
            <dl class="mt-8 space-y-4 text-sm">
                <div><dt class="font-semibold text-stone-900">Office</dt><dd class="text-stone-600">{{ config('sitedesk.address') }}</dd></div>
                <div><dt class="font-semibold text-stone-900">Phone</dt><dd class="text-stone-600">{{ config('sitedesk.phone') }}</dd></div>
            </dl>
        </div>

        <div class="md:col-span-3">
            @if (session('status'))
                <div class="mb-6 rounded-lg border border-teal-200 bg-teal-50 p-4 text-teal-800" role="status">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('contact.store') }}" class="space-y-5 rounded-xl border border-stone-200 bg-white p-6 shadow-sm" novalidate>
                @csrf

                {{-- Honeypot field: hidden from people, filled in by bots. --}}
                <div class="hidden" aria-hidden="true">
                    <label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    @include('partials.field', ['name' => 'name', 'label' => 'Your name', 'required' => true])
                    @include('partials.field', ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true])
                    @include('partials.field', ['name' => 'phone', 'label' => 'Phone (optional)', 'type' => 'tel'])
                    <div>
                        <label for="service_id" class="block text-sm font-medium text-stone-700">Service</label>
                        <select id="service_id" name="service_id" class="mt-1 w-full rounded-lg border border-stone-300 bg-white px-3 py-2 focus:border-teal-600 focus:outline-none">
                            <option value="">Not sure yet</option>
                            @foreach ($services as $service)
                                <option value="{{ $service->id }}" @selected(old('service_id') == $service->id)>{{ $service->title }}</option>
                            @endforeach
                        </select>
                        @error('service_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                @include('partials.field', ['name' => 'message', 'label' => 'Message', 'type' => 'textarea', 'required' => true])
                @error('website') <p class="text-sm text-red-600">{{ $message }}</p> @enderror

                <button class="w-full rounded-lg bg-teal-700 px-6 py-3 font-medium text-white hover:bg-teal-800 sm:w-auto">Send message</button>
            </form>
        </div>
    </section>
</x-layouts.site>
