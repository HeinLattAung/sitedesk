<x-layouts.site :title="$post->title" :description="$post->excerpt">
    <article class="mx-auto max-w-3xl px-4 py-12">
        <a href="{{ route('blog.index') }}" class="text-sm text-teal-700 hover:underline">&larr; All posts</a>
        <h1 class="mt-4 text-3xl font-bold leading-tight text-stone-900 md:text-5xl">{{ $post->title }}</h1>
        <p class="mt-4 text-sm text-stone-500">
            By {{ $post->author->name }} &middot; {{ $post->published_at->format('j F Y') }} &middot; {{ $post->readingMinutes() }} min read
        </p>

        @if ($post->coverUrl())
            <img src="{{ $post->coverUrl() }}" alt="" class="mt-8 w-full rounded-xl object-cover">
        @endif

        {{-- Body is escaped, then paragraphs are split on blank lines: no raw HTML from the database reaches the page. --}}
        <div class="mt-8 space-y-5 text-lg leading-relaxed text-stone-700">
            @foreach (preg_split('/\R{2,}/', trim($post->body)) as $paragraph)
                <p>{!! nl2br(e($paragraph)) !!}</p>
            @endforeach
        </div>
    </article>

    @if ($related->isNotEmpty())
        <section class="mx-auto max-w-3xl border-t border-stone-200 px-4 pt-10">
            <h2 class="text-xl font-bold text-stone-900">Keep reading</h2>
            <div class="mt-6 grid gap-6 sm:grid-cols-2">
                @foreach ($related as $item)
                    @include('blog._card', ['post' => $item])
                @endforeach
            </div>
        </section>
    @endif
</x-layouts.site>
