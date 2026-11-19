<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostRequest;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');

        $posts = Post::with('author')
            ->when($status === 'draft', fn ($q) => $q->whereNull('published_at'))
            ->when($status === 'published', fn ($q) => $q->published())
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.posts.index', compact('posts', 'status'));
    }

    public function create(): View
    {
        return view('admin.posts.form', ['post' => new Post]);
    }

    public function store(PostRequest $request): RedirectResponse
    {
        $post = $request->user()->posts()->create($this->attributes($request));

        return redirect()->route('admin.posts.edit', $post)->with('status', 'Post created.');
    }

    public function edit(Post $post): View
    {
        return view('admin.posts.form', compact('post'));
    }

    public function update(PostRequest $request, Post $post): RedirectResponse
    {
        $post->update($this->attributes($request, $post));

        return redirect()->route('admin.posts.edit', $post)->with('status', 'Post updated.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $post->delete();

        return redirect()->route('admin.posts.index')->with('status', 'Post deleted.');
    }

    /**
     * Turn the validated form into model attributes, handling the cover upload.
     */
    private function attributes(PostRequest $request, ?Post $post = null): array
    {
        $data = $request->safe()->only(['title', 'excerpt', 'body', 'published_at']);

        if ($request->boolean('publish_now')) {
            $data['published_at'] = $post?->isPublished() ? $post->published_at : now();
        }

        $old = $post?->cover_image;

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        } elseif ($request->boolean('remove_cover')) {
            $data['cover_image'] = null;
        }

        if ($old && array_key_exists('cover_image', $data)) {
            Storage::disk('public')->delete($old);
        }

        return $data;
    }
}
