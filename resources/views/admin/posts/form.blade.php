<x-layouts.admin :title="$post->exists ? 'Edit post' : 'New post'">
    <form method="POST" enctype="multipart/form-data"
          action="{{ $post->exists ? route('admin.posts.update', $post) : route('admin.posts.store') }}"
          class="grid gap-6 lg:grid-cols-3">
        @csrf
        @if ($post->exists) @method('PUT') @endif

        <div class="space-y-5 rounded-xl border border-stone-200 bg-white p-6 lg:col-span-2">
            @include('partials.field', ['name' => 'title', 'label' => 'Title', 'required' => true, 'value' => $post->title])
            @include('partials.field', ['name' => 'excerpt', 'label' => 'Excerpt', 'type' => 'textarea', 'rows' => 2, 'value' => $post->excerpt, 'hint' => 'Shown on cards and in search results. Max 300 characters.'])
            @include('partials.field', ['name' => 'body', 'label' => 'Body', 'type' => 'textarea', 'rows' => 16, 'required' => true, 'value' => $post->body, 'hint' => 'Plain text. Leave a blank line between paragraphs.'])
        </div>

        <div class="space-y-6">
            <div class="space-y-4 rounded-xl border border-stone-200 bg-white p-6">
                <h2 class="font-semibold text-stone-900">Publishing</h2>
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" name="publish_now" value="1" class="rounded border-stone-300" @checked(old('publish_now', $post->isPublished()))>
                    Published
                </label>
                @include('partials.field', [
                    'name' => 'published_at', 'label' => 'Or schedule for', 'type' => 'datetime-local',
                    'value' => $post->published_at && ! $post->isPublished() ? $post->published_at->format('Y-m-d\TH:i') : '',
                    'hint' => 'Leave both empty to keep this as a draft.',
                ])
                <button class="w-full rounded-lg bg-teal-700 px-4 py-2 font-medium text-white hover:bg-teal-800">
                    {{ $post->exists ? 'Save changes' : 'Create post' }}
                </button>
            </div>

            <div class="space-y-3 rounded-xl border border-stone-200 bg-white p-6">
                <h2 class="font-semibold text-stone-900">Cover image</h2>
                @if ($post->coverUrl())
                    <img src="{{ $post->coverUrl() }}" alt="" class="w-full rounded-lg">
                    <label class="flex items-center gap-2 text-sm text-stone-600">
                        <input type="checkbox" name="remove_cover" value="1" class="rounded border-stone-300"> Remove image
                    </label>
                @endif
                <input type="file" name="cover_image" accept="image/jpeg,image/png,image/webp" class="block w-full text-sm">
                <p class="text-xs text-stone-500">JPG, PNG or WebP, up to 2 MB.</p>
                @error('cover_image') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>
    </form>

    @if ($post->exists)
        <form method="POST" action="{{ route('admin.posts.destroy', $post) }}" class="mt-6"
              onsubmit="return confirm('Delete this post? This cannot be undone.')">
            @csrf @method('DELETE')
            <button class="text-sm text-red-600 hover:underline">Delete post</button>
        </form>
    @endif
</x-layouts.admin>
