<x-layouts.site title="Contact" description="Tell us about your website project and get a free, fixed-price quote.">
    <section class="relative overflow-hidden">
        <div class="absolute -top-32 right-0 h-96 w-96 rounded-full bg-brand-100/60 blur-3xl" aria-hidden="true"></div>

        <div class="relative mx-auto grid max-w-6xl gap-12 px-4 py-16 sm:px-6 lg:grid-cols-5 lg:py-20">
            <div class="lg:col-span-2">
                <p class="eyebrow">Contact</p>
                <h1 class="mt-3 text-4xl font-bold tracking-tight text-slate-900 text-balance sm:text-5xl">Let's talk about your website</h1>
                <p class="mt-5 text-lg text-slate-600">Tell us what you need. We reply within one business day with clear next steps and a fixed-price quote.</p>

                <ul class="mt-10 space-y-4">
                    @foreach ([
                        ['map-pin', 'Office', config('sitedesk.address')],
                        ['phone', 'Phone', config('sitedesk.phone')],
                        ['clock', 'Hours', 'Monday – Friday, 9:00 – 18:00'],
                    ] as [$infoIcon, $infoLabel, $infoValue])
                        <li class="flex items-center gap-4 rounded-2xl bg-white p-4 ring-1 ring-slate-200">
                            <span class="grid size-11 shrink-0 place-items-center rounded-xl bg-brand-50 text-brand-600"><x-icon :name="$infoIcon" /></span>
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">{{ $infoLabel }}</p>
                                <p class="font-medium text-slate-900">{{ $infoValue }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="lg:col-span-3">
                @if (session('status'))
                    {{-- Success state replaces the form, so the visitor clearly knows it worked. --}}
                    <div class="card animate-fade-up p-10 text-center" role="status">
                        <span class="mx-auto grid size-16 place-items-center rounded-full bg-brand-100 text-brand-600">
                            <x-icon name="check" class="size-8" />
                        </span>
                        <h2 class="mt-6 text-2xl font-bold text-slate-900">Message sent!</h2>
                        <p class="mx-auto mt-3 max-w-md text-slate-600">{{ session('status') }}</p>
                        <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                            <a href="{{ route('home') }}" class="btn btn-secondary">Back to home</a>
                            <a href="{{ route('blog.index') }}" class="btn btn-primary">Read our blog</a>
                        </div>
                    </div>
                @else
                    <form method="POST" action="{{ route('contact.store') }}" data-loading="Sending…" class="card space-y-5 p-6 sm:p-8" novalidate>
                        @csrf
                        <div>
                            <h2 class="text-lg font-semibold text-slate-900">Send us a message</h2>
                            <p class="text-sm text-slate-500">Fields marked <span class="text-red-500">*</span> are required.</p>
                        </div>

                        {{-- Honeypot field: hidden from people, filled in by bots. --}}
                        <div class="hidden" aria-hidden="true">
                            <label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                        </div>

                        <div class="grid gap-5 sm:grid-cols-2">
                            @include('partials.field', ['name' => 'name', 'label' => 'Your name', 'required' => true, 'autocomplete' => 'name', 'placeholder' => 'Somchai Jaidee'])
                            @include('partials.field', ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true, 'autocomplete' => 'email', 'placeholder' => 'you@company.com'])
                            @include('partials.field', ['name' => 'phone', 'label' => 'Phone', 'type' => 'tel', 'autocomplete' => 'tel', 'placeholder' => '08x xxx xxxx', 'hint' => 'Optional'])
                            <div>
                                <label for="service_id" class="label">What do you need?</label>
                                <select id="service_id" name="service_id" class="input {{ $errors->has('service_id') ? 'input-error' : '' }}">
                                    <option value="">Not sure yet</option>
                                    @foreach ($services as $service)
                                        <option value="{{ $service->id }}" @selected(old('service_id') == $service->id)>{{ $service->title }}</option>
                                    @endforeach
                                </select>
                                @error('service_id') <p class="error"><x-icon name="warning" class="size-4" /> {{ $message }}</p> @enderror
                            </div>
                        </div>

                        @include('partials.field', ['name' => 'message', 'label' => 'Tell us about your project', 'type' => 'textarea', 'rows' => 6, 'required' => true, 'autosize' => true,
                            'placeholder' => 'What kind of website do you need? Do you have a deadline or budget in mind?'])
                        @error('website') <p class="error">{{ $message }}</p> @enderror

                        <div class="flex flex-col-reverse items-start gap-4 pt-2 sm:flex-row sm:items-center sm:justify-between">
                            <p class="flex items-center gap-1.5 text-xs text-slate-500"><x-icon name="lock" class="size-4" /> We never share your details.</p>
                            <button type="submit" class="btn btn-primary btn-lg w-full sm:w-auto">Send message <x-icon name="arrow-right" class="size-5" /></button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </section>
</x-layouts.site>
