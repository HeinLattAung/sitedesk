<x-layouts.site>
    <section class="bg-gradient-to-b from-teal-50 to-stone-50">
        <div class="mx-auto max-w-6xl px-4 py-20 md:py-28">
            <p class="text-sm font-semibold uppercase tracking-wider text-teal-700">Web design &middot; Development &middot; Hosting</p>
            <h1 class="mt-4 max-w-3xl text-4xl font-bold leading-tight text-stone-900 md:text-6xl">
                {{ config('sitedesk.tagline') }}
            </h1>
            <p class="mt-6 max-w-2xl text-lg text-stone-600">
                We design, build and look after websites on our own Linux servers, so you get one team for the whole job
                &mdash; from first sketch to SSL renewals.
            </p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('contact') }}" class="rounded-lg bg-teal-700 px-6 py-3 font-medium text-white hover:bg-teal-800">Start a project</a>
                <a href="#services" class="rounded-lg border border-stone-300 bg-white px-6 py-3 font-medium text-stone-700 hover:border-stone-400">See services</a>
            </div>
        </div>
    </section>

    <section id="services" class="mx-auto max-w-6xl px-4 py-16">
        <h2 class="text-2xl font-bold text-stone-900 md:text-3xl">What we do</h2>
        <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($services as $service)
                <article class="flex flex-col rounded-xl border border-stone-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-stone-900">{{ $service->title }}</h3>
                    <p class="mt-2 flex-1 text-stone-600">{{ $service->description }}</p>
                    @if ($service->price_from)
                        <p class="mt-4 text-sm font-medium text-teal-700">From {{ $service->price_from }}</p>
                    @endif
                </article>
            @empty
                <p class="text-stone-500">Services are coming soon.</p>
            @endforelse
        </div>
    </section>

    @if ($posts->isNotEmpty())
        <section class="mx-auto max-w-6xl px-4 py-8">
            <div class="flex items-end justify-between">
                <h2 class="text-2xl font-bold text-stone-900 md:text-3xl">From the blog</h2>
                <a href="{{ route('blog.index') }}" class="text-sm font-medium text-teal-700 hover:underline">All posts &rarr;</a>
            </div>
            <div class="mt-8 grid gap-6 md:grid-cols-3">
                @foreach ($posts as $post)
                    @include('blog._card', ['post' => $post])
                @endforeach
            </div>
        </section>
    @endif
</x-layouts.site>
