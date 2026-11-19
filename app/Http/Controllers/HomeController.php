<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Service;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        // Services rarely change, so cache them; the admin clears this key on save.
        // Cache plain arrays (Laravel 13 refuses to unserialize objects from the cache by default).
        $services = Service::hydrate(Cache::remember(
            'home.services',
            now()->addHour(),
            fn () => Service::active()->get()->map->getAttributes()->all(),
        ));

        $posts = Post::published()->with('author')->latest('published_at')->take(3)->get();

        return view('home', compact('services', 'posts'));
    }
}
