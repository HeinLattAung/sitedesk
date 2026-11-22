<x-layouts.site :title="$post->title" :description="$post->excerpt">
    <div id="reading-progress" class="fixed inset-x-0 top-0 z-50 h-1 bg-gradient-to-r from-brand-500 to-accent-400" aria-hidden="true"></div>

    <article data-article>
        <header class="border-b border-slate-100 bg-gradient-to-b from-brand-50/60 to-white">
            <div class="mx-auto max-w-3xl px-4 pb-12 pt-10 sm:px-6">
                <a href="{{ route('blog.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 transition hover:text-brand-700">
                    <x-icon name="arrow-left" class="size-4" /> All posts
                </a>
                <h1 class="mt-6 text-3xl font-bold leading-tight tracking-tight text-slate-900 text-balance sm:text-5xl">{{ $post->title }}</h1>
                @if ($post->excerpt)
                    <p class="mt-5 text-xl leading-relaxed text-slate-600">{{ $post->excerpt }}</p>
                @endif
                <div class="mt-8 flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <span class="grid size-10 place-items-center rounded-full bg-brand-600 text-sm font-semibold text-white">
                            {{ Str::of($post->author->name)->explode(' ')->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->implode('') }}
                        </span>
                        <div class="text-sm">
                            <p class="font-semibold text-slate-900">{{ $post->author->name }}</p>
                            <p class="text-slate-500">
                                <time datetime="{{ $post->published_at->toDateString() }}">{{ $post->published_at->format('j F Y') }}</time>
                                · {{ $post->readingMinutes() }} min read
                            </p>
                        </div>
                    </div>
                    <button type="button" data-copy="{{ route('blog.show', $post) }}" class="btn btn-secondary btn-sm">
                        <x-icon name="link" class="size-4" /> <span data-copy-label>Copy link</span>
                    </button>
                </div>
            </div>
        </header>

        <div class="mx-auto max-w-3xl px-4 py-12 sm:px-6">
            @if ($post->coverUrl())
                <img src="{{ $post->coverUrl() }}" alt="" class="mb-12 w-full rounded-2xl object-cover shadow-soft">
            @endif

            {{-- Body is escaped, then split into paragraphs on blank lines: no raw HTML from the database reaches the page. --}}
            <div class="prose-post">
                @foreach (preg_split('/\R{2,}/', trim($post->body)) as $paragraph)
                    <p>{!! nl2br(e($paragraph)) !!}</p>
                @endforeach
            </div>

            <div class="mt-14 flex flex-col items-start gap-4 rounded-2xl bg-brand-50 p-6 ring-1 ring-brand-100 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="font-semibold text-slate-900">Need help with your website?</p>
                    <p class="text-sm text-slate-600">Get a free, fixed-price quote within one business day.</p>
                </div>
                <a href="{{ route('contact') }}" class="btn btn-primary shrink-0">Talk to us <x-icon name="arrow-right" class="size-4" /></a>
            </div>
        </div>
    </article>

    @if ($related->isNotEmpty())
        <section class="mx-auto max-w-3xl border-t border-slate-100 px-4 pt-12 sm:px-6">
            <h2 class="text-xl font-bold text-slate-900">Keep reading</h2>
            <div class="mt-6 grid gap-6 sm:grid-cols-2">
                @foreach ($related as $item)
                    @include('blog._card', ['post' => $item])
                @endforeach
            </div>
        </section>
    @endif
</x-layouts.site>
