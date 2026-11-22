<x-layouts.admin :title="$post->exists ? 'Edit post' : 'New post'" :back="route('admin.posts.index')"
                 :subtitle="$post->exists ? 'Last saved '.$post->updated_at->diffForHumans() : 'Write a new article for your blog.'">
    @if ($post->exists && $post->isPublished())
        <x-slot:actions>
            <a href="{{ route('blog.show', $post) }}" target="_blank" class="btn btn-secondary"><x-icon name="eye" class="size-4" /> View live</a>
        </x-slot:actions>
    @endif

    <form method="POST" enctype="multipart/form-data" data-loading="Saving…"
          action="{{ $post->exists ? route('admin.posts.update', $post) : route('admin.posts.store') }}"
          class="grid gap-6 lg:grid-cols-3">
        @csrf
        @if ($post->exists) @method('PUT') @endif

        <div class="card space-y-6 p-6 lg:col-span-2">
            <div>
                <label for="title" class="label">Title <span class="text-red-500">*</span></label>
                <input id="title" name="title" value="{{ old('title', $post->title) }}" required placeholder="A clear, specific headline"
                       class="input py-3 text-lg font-semibold {{ $errors->has('title') ? 'input-error' : '' }}">
                @error('title') <p class="error"><x-icon name="warning" class="size-4" /> {{ $message }}</p> @enderror
                @if ($post->exists)
                    <p class="hint flex items-center gap-1"><x-icon name="link" class="size-3.5" /> /blog/{{ $post->slug }}</p>
                @endif
            </div>
            @include('partials.field', ['name' => 'excerpt', 'label' => 'Excerpt', 'type' => 'textarea', 'rows' => 2, 'maxlength' => 300, 'value' => $post->excerpt,
                'placeholder' => 'One or two sentences that make people want to read more.', 'hint' => 'Shown on blog cards and in search results.'])
            @include('partials.field', ['name' => 'body', 'label' => 'Content', 'type' => 'textarea', 'rows' => 14, 'required' => true, 'autosize' => true, 'value' => $post->body,
                'placeholder' => 'Start writing…', 'hint' => 'Plain text. Leave an empty line between paragraphs.'])
        </div>

        <div class="space-y-6">
            <div class="card p-6">
                <h2 class="font-semibold text-slate-900">Publishing</h2>
                <label class="mt-4 flex cursor-pointer items-center justify-between gap-4 rounded-xl bg-slate-50 p-3 ring-1 ring-slate-200">
                    <span>
                        <span class="block text-sm font-medium text-slate-900">Published</span>
                        <span class="block text-xs text-slate-500">Visible on the website now</span>
                    </span>
                    <input type="checkbox" name="publish_now" value="1" class="peer sr-only" @checked(old('publish_now', $post->isPublished()))>
                    <span class="relative h-6 w-11 shrink-0 rounded-full bg-slate-300 transition peer-checked:bg-brand-600 peer-focus-visible:ring-2 peer-focus-visible:ring-brand-500 peer-focus-visible:ring-offset-2
                                 after:absolute after:left-0.5 after:top-0.5 after:size-5 after:rounded-full after:bg-white after:shadow after:transition peer-checked:after:translate-x-5"></span>
                </label>
                <div class="mt-4">
                    @include('partials.field', [
                        'name' => 'published_at', 'label' => 'Or schedule for later', 'type' => 'datetime-local',
                        'value' => $post->published_at && ! $post->isPublished() ? $post->published_at->format('Y-m-d\TH:i') : '',
                        'hint' => 'Leave both off to keep this post as a draft.',
                    ])
                </div>
                <button type="submit" class="btn btn-primary mt-5 w-full">
                    <x-icon name="check" class="size-4" /> {{ $post->exists ? 'Save changes' : 'Create post' }}
                </button>
            </div>

            <div class="card p-6">
                <h2 class="font-semibold text-slate-900">Cover image</h2>
                <label data-dropzone class="mt-4 block cursor-pointer overflow-hidden rounded-xl bg-slate-50 ring-1 ring-dashed ring-slate-300 transition hover:bg-slate-100">
                    <input type="file" name="cover_image" accept="image/jpeg,image/png,image/webp" class="sr-only">
                    <img data-preview src="{{ $post->coverUrl() }}" alt="" class="{{ $post->coverUrl() ? '' : 'hidden' }} aspect-video w-full object-cover">
                    <span data-empty class="{{ $post->coverUrl() ? 'hidden' : '' }} flex flex-col items-center gap-2 px-4 py-10 text-center">
                        <span class="grid size-11 place-items-center rounded-full bg-white text-brand-600 shadow-soft"><x-icon name="upload" /></span>
                        <span class="text-sm font-medium text-slate-700">Click or drag an image here</span>
                        <span class="text-xs text-slate-500">JPG, PNG or WebP · up to 2 MB</span>
                    </span>
                </label>
                <p data-filename class="mt-2 truncate text-xs text-slate-500"></p>
                @error('cover_image') <p class="error"><x-icon name="warning" class="size-4" /> {{ $message }}</p> @enderror
                @if ($post->coverUrl())
                    <label class="mt-3 flex items-center gap-2 text-sm text-slate-600">
                        <input type="checkbox" name="remove_cover" value="1" class="size-4 rounded border-slate-300 text-red-600"> Remove current image
                    </label>
                @endif
            </div>
        </div>
    </form>

    @if ($post->exists)
        <div class="mt-6 flex flex-col gap-4 rounded-2xl bg-white p-6 ring-1 ring-red-200 sm:flex-row sm:items-center sm:justify-between lg:w-2/3">
            <div>
                <p class="font-semibold text-slate-900">Delete this post</p>
                <p class="text-sm text-slate-500">It will be removed from the website, along with its cover image.</p>
            </div>
            <form method="POST" action="{{ route('admin.posts.destroy', $post) }}"
                  data-confirm="“{{ $post->title }}” will be permanently deleted. This cannot be undone." data-confirm-title="Delete this post?" data-confirm-button="Delete post">
                @csrf @method('DELETE')
                <button class="btn btn-danger-ghost ring-1 ring-red-200"><x-icon name="trash" class="size-4" /> Delete post</button>
            </form>
        </div>
    @endif
</x-layouts.admin>
