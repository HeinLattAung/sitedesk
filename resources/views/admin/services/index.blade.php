<x-layouts.admin title="Services" subtitle="The services and prices shown on your home page.">
    <x-slot:actions>
        <a href="{{ route('admin.services.create') }}" class="btn btn-primary"><x-icon name="plus" class="size-4" /> New service</a>
    </x-slot:actions>

    @if ($services->isEmpty())
        <div class="card px-6 py-16 text-center">
            <span class="mx-auto grid size-14 place-items-center rounded-2xl bg-slate-100 text-slate-400"><x-icon name="briefcase" class="size-7" /></span>
            <h2 class="mt-4 font-semibold text-slate-900">No services yet</h2>
            <p class="mt-1 text-sm text-slate-500">Add what you offer so visitors can choose it in the contact form.</p>
            <a href="{{ route('admin.services.create') }}" class="btn btn-primary mt-6"><x-icon name="plus" class="size-4" /> New service</a>
        </div>
    @else
        <div class="grid gap-4 md:grid-cols-2">
            @foreach ($services as $service)
                <a href="{{ route('admin.services.edit', $service) }}"
                   class="card group flex gap-4 p-5 transition hover:-translate-y-0.5 hover:shadow-lift {{ $service->is_active ? '' : 'opacity-70' }}">
                    <span class="grid size-11 shrink-0 place-items-center rounded-xl bg-slate-100 text-sm font-bold text-slate-500 tabular-nums" title="Display order">
                        {{ $service->sort_order }}
                    </span>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-start justify-between gap-3">
                            <h2 class="font-semibold text-slate-900 group-hover:text-brand-700">{{ $service->title }}</h2>
                            @if ($service->is_active)
                                <span class="badge shrink-0 bg-brand-50 text-brand-700 ring-1 ring-brand-200 ring-inset"><span class="size-1.5 rounded-full bg-brand-500"></span>Live</span>
                            @else
                                <span class="badge shrink-0 bg-slate-100 text-slate-600 ring-1 ring-slate-200 ring-inset">Hidden</span>
                            @endif
                        </div>
                        <p class="mt-1 line-clamp-2 text-sm text-slate-500">{{ $service->description }}</p>
                        <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-500">
                            <span>From <span class="font-semibold text-slate-800">{{ $service->price_from ?? '—' }}</span></span>
                            <span class="flex items-center gap-1"><x-icon name="inbox" class="size-3.5" /> {{ $service->inquiries_count }} {{ Str::plural('inquiry', $service->inquiries_count) }}</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</x-layouts.admin>
