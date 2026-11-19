<article class="group overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm">
    <a href="{{ route('blog.show', $post) }}" class="block">
        @if ($post->coverUrl())
            <img src="{{ $post->coverUrl() }}" alt="" loading="lazy" class="aspect-[16/9] w-full object-cover">
        @else
            <div class="grid aspect-[16/9] w-full place-items-center bg-gradient-to-br from-teal-600 to-teal-900 p-6 text-center text-lg font-semibold text-white">
                {{ $post->title }}
            </div>
        @endif
        <div class="p-5">
            <p class="text-xs text-stone-500">
                {{ $post->published_at->format('j M Y') }} &middot; {{ $post->readingMinutes() }} min read
            </p>
            <h3 class="mt-2 font-semibold text-stone-900 group-hover:text-teal-700">{{ $post->title }}</h3>
            @if ($post->excerpt)
                <p class="mt-2 line-clamp-3 text-sm text-stone-600">{{ $post->excerpt }}</p>
            @endif
        </div>
    </a>
</article>
