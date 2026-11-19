<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceRequest;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(): View
    {
        return view('admin.services.index', [
            'services' => Service::withCount('inquiries')->orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.services.form', ['service' => new Service(['sort_order' => 0, 'is_active' => true])]);
    }

    public function store(ServiceRequest $request): RedirectResponse
    {
        Service::create($this->attributes($request));
        Cache::forget('home.services');

        return redirect()->route('admin.services.index')->with('status', 'Service added.');
    }

    public function edit(Service $service): View
    {
        return view('admin.services.form', compact('service'));
    }

    public function update(ServiceRequest $request, Service $service): RedirectResponse
    {
        $service->update($this->attributes($request));
        Cache::forget('home.services');

        return redirect()->route('admin.services.index')->with('status', 'Service updated.');
    }

    public function destroy(Service $service): RedirectResponse
    {
        $service->delete();
        Cache::forget('home.services');

        return redirect()->route('admin.services.index')->with('status', 'Service deleted.');
    }

    private function attributes(ServiceRequest $request): array
    {
        return [...$request->safe()->except('is_active'), 'is_active' => $request->boolean('is_active')];
    }
}
