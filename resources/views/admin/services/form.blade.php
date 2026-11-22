<x-layouts.admin :title="$service->exists ? 'Edit service' : 'New service'" :back="route('admin.services.index')"
                 subtitle="Changes appear on the home page immediately.">
    <div class="grid gap-6 lg:grid-cols-3">
        <form method="POST" data-loading="Saving…" action="{{ $service->exists ? route('admin.services.update', $service) : route('admin.services.store') }}"
              class="card space-y-5 p-6 lg:col-span-2">
            @csrf
            @if ($service->exists) @method('PUT') @endif

            @include('partials.field', ['name' => 'title', 'label' => 'Service name', 'required' => true, 'value' => $service->title, 'placeholder' => 'e.g. Business websites'])
            @include('partials.field', ['name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'rows' => 4, 'required' => true, 'maxlength' => 1000, 'value' => $service->description,
                'placeholder' => 'What does the customer get? Keep it to two or three sentences.'])
            <div class="grid gap-5 sm:grid-cols-2">
                @include('partials.field', ['name' => 'price_from', 'label' => 'Starting price', 'value' => $service->price_from, 'placeholder' => '25,000 THB', 'hint' => 'Shown as “From …”'])
                @include('partials.field', ['name' => 'sort_order', 'label' => 'Display order', 'type' => 'number', 'required' => true, 'value' => $service->sort_order, 'hint' => 'Lower numbers appear first.'])
            </div>
            <label class="flex cursor-pointer items-center justify-between gap-4 rounded-xl bg-slate-50 p-3 ring-1 ring-slate-200">
                <span>
                    <span class="block text-sm font-medium text-slate-900">Show on website</span>
                    <span class="block text-xs text-slate-500">Hidden services can't be chosen in the contact form.</span>
                </span>
                <input type="checkbox" name="is_active" value="1" class="peer sr-only" @checked(old('is_active', $service->is_active))>
                <span class="relative h-6 w-11 shrink-0 rounded-full bg-slate-300 transition peer-checked:bg-brand-600 peer-focus-visible:ring-2 peer-focus-visible:ring-brand-500 peer-focus-visible:ring-offset-2
                             after:absolute after:left-0.5 after:top-0.5 after:size-5 after:rounded-full after:bg-white after:shadow after:transition peer-checked:after:translate-x-5"></span>
            </label>
            <div class="flex justify-end gap-2 border-t border-slate-100 pt-5">
                <a href="{{ route('admin.services.index') }}" class="btn btn-ghost">Cancel</a>
                <button type="submit" class="btn btn-primary"><x-icon name="check" class="size-4" /> Save service</button>
            </div>
        </form>

        @if ($service->exists)
            <div class="space-y-6">
                <div class="card p-6">
                    <h2 class="font-semibold text-slate-900">Stats</h2>
                    <dl class="mt-4 space-y-3 text-sm">
                        <div class="flex justify-between"><dt class="text-slate-500">Inquiries</dt><dd class="font-semibold text-slate-900">{{ $service->inquiries()->count() }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-500">Created</dt><dd class="text-slate-700">{{ $service->created_at->format('j M Y') }}</dd></div>
                    </dl>
                </div>
                <div class="rounded-2xl bg-white p-6 ring-1 ring-red-200">
                    <p class="font-semibold text-slate-900">Delete service</p>
                    <p class="mt-1 text-sm text-slate-500">Past inquiries are kept, but will no longer be linked to it.</p>
                    <form method="POST" action="{{ route('admin.services.destroy', $service) }}" class="mt-4"
                          data-confirm="“{{ $service->title }}” will be removed from the website." data-confirm-title="Delete this service?" data-confirm-button="Delete service">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger-ghost w-full ring-1 ring-red-200"><x-icon name="trash" class="size-4" /> Delete service</button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</x-layouts.admin>
