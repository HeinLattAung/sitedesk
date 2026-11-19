<x-layouts.admin title="Posts">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex gap-2 text-sm">
            @foreach ([null => 'All', 'published' => 'Published', 'draft' => 'Drafts'] as $key => $label)
                <a href="{{ route('admin.posts.index', array_filter(['status' => $key])) }}"
                   class="rounded-full px-3 py-1 {{ $status == $key ? 'bg-stone-900 text-white' : 'bg-white text-stone-600 hover:bg-stone-200' }}">{{ $label }}</a>
            @endforeach
        </div>
        <a href="{{ route('admin.posts.create') }}" class="rounded-lg bg-teal-700 px-4 py-2 text-center text-sm font-medium text-white hover:bg-teal-800">New post</a>
    </div>

    <div class="mt-6 overflow-x-auto rounded-xl border border-stone-200 bg-white">
        <table class="w-full text-left text-sm">
            <thead class="bg-stone-50 text-xs uppercase text-stone-500">
                <tr><th class="px-4 py-3">Title</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Author</th><th class="px-4 py-3"></th></tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse ($posts as $post)
                    <tr>
                        <td class="px-4 py-3 font-medium text-stone-900">{{ $post->title }}</td>
                        <td class="px-4 py-3">
                            @if ($post->isPublished())
                                <span class="rounded-full bg-teal-100 px-2 py-0.5 text-xs text-teal-800">Published {{ $post->published_at->format('j M Y') }}</span>
                            @elseif ($post->published_at)
                                <span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs text-amber-800">Scheduled {{ $post->published_at->format('j M Y H:i') }}</span>
                            @else
                                <span class="rounded-full bg-stone-200 px-2 py-0.5 text-xs text-stone-700">Draft</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-stone-600">{{ $post->author->name }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-right">
                            @if ($post->isPublished())
                                <a href="{{ route('blog.show', $post) }}" class="text-stone-500 hover:underline" target="_blank">View</a>
                            @endif
                            <a href="{{ route('admin.posts.edit', $post) }}" class="ml-3 text-teal-700 hover:underline">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-6 text-stone-500">No posts.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $posts->links() }}</div>
</x-layouts.admin>
