<x-layouts.site title="Blog">
    <section class="mx-auto max-w-6xl px-4 py-12">
        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-stone-900 md:text-4xl">Blog</h1>
                <p class="mt-2 text-stone-600">Notes on building and running websites.</p>
            </div>
            <form method="GET" action="{{ route('blog.index') }}" class="flex w-full gap-2 md:w-auto">
                <input type="search" name="q" value="{{ $search }}" placeholder="Search posts"
                       class="w-full rounded-lg border border-stone-300 bg-white px-4 py-2 focus:border-teal-600 focus:outline-none md:w-64">
                <button class="rounded-lg bg-stone-900 px-4 py-2 text-sm font-medium text-white">Search</button>
            </form>
        </div>

        @if ($search !== '')
            <p class="mt-6 text-sm text-stone-600">
                {{ $posts->total() }} result(s) for &ldquo;{{ $search }}&rdquo; &middot;
                <a href="{{ route('blog.index') }}" class="text-teal-700 hover:underline">Clear</a>
            </p>
        @endif

        <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($posts as $post)
                @include('blog._card', ['post' => $post])
            @empty
                <p class="text-stone-500">No posts found.</p>
            @endforelse
        </div>

        <div class="mt-10">{{ $posts->links() }}</div>
    </section>
</x-layouts.site>
