<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(Request $request): View
    {
        // is_string guard: ?q[]=x would otherwise crash with "Array to string conversion".
        $search = is_string($q = $request->query('q')) ? trim($q) : '';

        $posts = Post::published()
            ->with('author')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(fn ($q) => $q
                    ->where('title', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%"));
            })
            ->latest('published_at')
            ->paginate(6)
            ->withQueryString();

        return view('blog.index', compact('posts', 'search'));
    }

    public function show(Post $post): View
    {
        // Drafts and scheduled posts are invisible to the public.
        abort_unless($post->isPublished(), 404);

        $related = Post::published()
            ->whereKeyNot($post->id)
            ->latest('published_at')
            ->take(2)
            ->get();

        return view('blog.show', compact('post', 'related'));
    }
}
