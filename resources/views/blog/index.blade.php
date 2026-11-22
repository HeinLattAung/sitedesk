<x-layouts.site title="Blog" description="Practical tips on building, speeding up and securing business websites.">
    <section class="relative overflow-hidden border-b border-slate-100 bg-gradient-to-b from-brand-50/70 to-white">
        <div class="bg-grid absolute inset-0 [mask-image:radial-gradient(ellipse_at_top,black,transparent_70%)]" aria-hidden="true"></div>
        <div class="relative mx-auto max-w-6xl px-4 py-16 sm:px-6">
            <p class="eyebrow">Blog</p>
            <h1 class="mt-3 text-4xl font-bold tracking-tight text-slate-900 sm:text-5xl">Notes on building better websites</h1>
            <p class="mt-4 max-w-2xl text-lg text-slate-600">Practical advice on speed, security and hosting, from the team that runs our clients' servers.</p>

            <form method="GET" action="{{ route('blog.index') }}" class="mt-8 flex max-w-lg gap-2" role="search">
                <label for="q" class="sr-only">Search posts</label>
                <div class="relative flex-1">
                    <x-icon name="search" class="pointer-events-none absolute left-3.5 top-1/2 size-5 -translate-y-1/2 text-slate-400" />
                    <input id="q" type="search" name="q" value="{{ $search }}" placeholder="Search articles…" class="input py-3 pl-11">
                </div>
                <button class="btn btn-dark px-5">Search</button>
            </form>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-4 py-12 sm:px-6">
        @if ($search !== '')
            <div class="mb-8 flex flex-wrap items-center gap-3 text-sm text-slate-600">
                <span><span class="font-semibold text-slate-900">{{ $posts->total() }}</span> {{ Str::plural('result', $posts->total()) }} for “{{ $search }}”</span>
                <a href="{{ route('blog.index') }}" class="badge bg-slate-100 text-slate-700 hover:bg-slate-200">
                    Clear search <x-icon name="x" class="size-3.5" />
                </a>
            </div>
        @endif

        @if ($posts->isEmpty())
            <div class="mx-auto max-w-md py-16 text-center">
                <span class="mx-auto grid size-14 place-items-center rounded-2xl bg-slate-100 text-slate-400"><x-icon name="search" class="size-7" /></span>
                <h2 class="mt-5 text-lg font-semibold text-slate-900">No posts found</h2>
                <p class="mt-2 text-slate-600">Try a different word, or browse all articles.</p>
                <a href="{{ route('blog.index') }}" class="btn btn-secondary mt-6">View all posts</a>
            </div>
        @else
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($posts as $i => $post)
                    <div data-reveal data-reveal-delay="{{ ($i % 3) * 80 }}">@include('blog._card', ['post' => $post])</div>
                @endforeach
            </div>
            <div class="mt-12">{{ $posts->links() }}</div>
        @endif
    </section>
</x-layouts.site>
