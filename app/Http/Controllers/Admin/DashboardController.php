<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use App\Models\Post;
use App\Models\Service;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'stats' => [
                'Published posts' => Post::published()->count(),
                'Drafts' => Post::whereNull('published_at')->count(),
                'Active services' => Service::where('is_active', true)->count(),
                'Unread inquiries' => Inquiry::unread()->count(),
            ],
            'inquiries' => Inquiry::with('service')->latest()->take(5)->get(),
            'posts' => Post::latest('updated_at')->take(4)->get(),
        ]);
    }
}
