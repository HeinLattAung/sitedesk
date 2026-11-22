@php
    // Posts without a cover image get a branded gradient so the grid still looks designed.
    $gradients = ['from-brand-500 to-brand-800', 'from-slate-700 to-slate-950', 'from-brand-700 to-slate-900', 'from-accent-400 to-brand-700'];
    $gradient = $gradients[$post->id % count($gradients)];
@endphp
<article class="group h-full">
    <a href="{{ route('blog.show', $post) }}"
       class="flex h-full flex-col overflow-hidden rounded-2xl bg-white ring-1 ring-slate-200 transition duration-300 hover:-translate-y-1 hover:shadow-lift hover:ring-brand-200">
        <div class="relative aspect-[16/9] overflow-hidden">
            @if ($post->coverUrl())
                <img src="{{ $post->coverUrl() }}" alt="" loading="lazy" class="size-full object-cover transition duration-500 group-hover:scale-105">
            @else
                <div class="relative flex size-full items-end bg-gradient-to-br {{ $gradient }} p-5 transition duration-500 group-hover:scale-105">
                    <div class="bg-grid absolute inset-0 opacity-30" aria-hidden="true"></div>
                    <x-icon name="document" class="absolute right-5 top-5 size-10 text-white/25" />
                    <p class="relative line-clamp-2 text-lg font-semibold leading-snug text-white">{{ $post->title }}</p>
                </div>
            @endif
        </div>
        <div class="flex flex-1 flex-col p-5">
            <p class="flex items-center gap-2 text-xs text-slate-500">
                <time datetime="{{ $post->published_at->toDateString() }}">{{ $post->published_at->format('j M Y') }}</time>
                <span class="size-1 rounded-full bg-slate-300"></span>
                {{ $post->readingMinutes() }} min read
            </p>
            <h3 class="mt-2 font-semibold leading-snug text-slate-900 transition group-hover:text-brand-700">{{ $post->title }}</h3>
            @if ($post->excerpt)
                <p class="mt-2 line-clamp-3 text-sm leading-relaxed text-slate-600">{{ $post->excerpt }}</p>
            @endif
            <span class="mt-auto inline-flex items-center gap-1 pt-4 text-sm font-semibold text-brand-700">
                Read article <x-icon name="arrow-right" class="size-4 transition group-hover:translate-x-1" />
            </span>
        </div>
    </a>
</article>
