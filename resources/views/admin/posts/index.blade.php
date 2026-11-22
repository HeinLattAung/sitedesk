<x-layouts.admin title="Posts" subtitle="Write, schedule and publish articles for your blog.">
    <x-slot:actions>
        <a href="{{ route('admin.posts.create') }}" class="btn btn-primary"><x-icon name="plus" class="size-4" /> New post</a>
    </x-slot:actions>

    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <nav class="flex gap-1 overflow-x-auto rounded-xl bg-white p-1 ring-1 ring-slate-200" aria-label="Filter posts">
            @foreach (['' => 'All', 'published' => 'Published', 'scheduled' => 'Scheduled', 'draft' => 'Drafts'] as $key => $label)
                @php $active = (string) $status === $key; @endphp
                <a href="{{ route('admin.posts.index', array_filter(['status' => $key, 'q' => $search])) }}"
                   class="flex items-center gap-2 whitespace-nowrap rounded-lg px-3 py-1.5 text-sm font-medium transition {{ $active ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                    {{ $label }}
                    <span class="rounded-md px-1.5 text-xs tabular-nums {{ $active ? 'bg-white/20' : 'bg-slate-100 text-slate-500' }}">{{ $counts[$key] ?? 0 }}</span>
                </a>
            @endforeach
        </nav>
        <form method="GET" class="relative md:w-72" role="search">
            @if ($status) <input type="hidden" name="status" value="{{ $status }}"> @endif
            <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-slate-500" />
            <input type="search" name="q" value="{{ $search }}" placeholder="Search by title…" class="input py-2 pl-9 text-sm" aria-label="Search posts">
        </form>
    </div>

    <div class="card mt-6 overflow-hidden">
        @if ($posts->isEmpty())
            <div class="px-6 py-16 text-center">
                <span class="mx-auto grid size-14 place-items-center rounded-2xl bg-slate-100 text-slate-500"><x-icon name="document" class="size-7" /></span>
                <h2 class="mt-4 font-semibold text-slate-900">{{ $search !== '' || $status ? 'No posts match this filter' : 'No posts yet' }}</h2>
                <p class="mt-1 text-sm text-slate-500">Write your first article to start building your blog.</p>
                <a href="{{ route('admin.posts.create') }}" class="btn btn-primary mt-6"><x-icon name="plus" class="size-4" /> New post</a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-slate-100 bg-slate-50/70 text-xs font-medium uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3">Title</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="hidden px-5 py-3 md:table-cell">Author</th>
                            <th class="hidden px-5 py-3 lg:table-cell">Updated</th>
                            <th class="px-5 py-3"><span class="sr-only">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($posts as $post)
                            <tr class="group transition hover:bg-slate-50/70">
                                <td class="max-w-md px-5 py-4">
                                    <a href="{{ route('admin.posts.edit', $post) }}" class="font-medium text-slate-900 hover:text-brand-700">{{ $post->title }}</a>
                                    @if ($post->excerpt) <p class="mt-0.5 truncate text-xs text-slate-500">{{ $post->excerpt }}</p> @endif
                                </td>
                                <td class="whitespace-nowrap px-5 py-4">
                                    @include('admin.posts._status', ['post' => $post])
                                    @if ($post->published_at)
                                        <p class="mt-1 text-xs text-slate-500">{{ $post->published_at->format('j M Y') }}</p>
                                    @endif
                                </td>
                                <td class="hidden px-5 py-4 text-slate-600 md:table-cell">{{ $post->author->name }}</td>
                                <td class="hidden whitespace-nowrap px-5 py-4 text-slate-500 lg:table-cell">{{ $post->updated_at->diffForHumans() }}</td>
                                <td class="whitespace-nowrap px-5 py-4">
                                    <div class="flex justify-end gap-1">
                                        @if ($post->isPublished())
                                            <a href="{{ route('blog.show', $post) }}" target="_blank" class="rounded-lg p-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-700" title="View on website" aria-label="View on website">
                                                <x-icon name="eye" class="size-4" />
                                            </a>
                                        @endif
                                        <a href="{{ route('admin.posts.edit', $post) }}" class="rounded-lg p-2 text-slate-500 transition hover:bg-brand-50 hover:text-brand-700" title="Edit" aria-label="Edit">
                                            <x-icon name="pencil" class="size-4" />
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    <div class="mt-6">{{ $posts->links() }}</div>
</x-layouts.admin>
