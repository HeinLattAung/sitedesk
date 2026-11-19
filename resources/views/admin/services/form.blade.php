<x-layouts.admin :title="$service->exists ? 'Edit service' : 'New service'">
    <form method="POST" action="{{ $service->exists ? route('admin.services.update', $service) : route('admin.services.store') }}"
          class="max-w-2xl space-y-5 rounded-xl border border-stone-200 bg-white p-6">
        @csrf
        @if ($service->exists) @method('PUT') @endif

        @include('partials.field', ['name' => 'title', 'label' => 'Title', 'required' => true, 'value' => $service->title])
        @include('partials.field', ['name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'rows' => 4, 'required' => true, 'value' => $service->description])
        <div class="grid gap-5 sm:grid-cols-2">
            @include('partials.field', ['name' => 'price_from', 'label' => 'Price from', 'value' => $service->price_from, 'hint' => 'e.g. 25,000 THB'])
            @include('partials.field', ['name' => 'sort_order', 'label' => 'Display order', 'type' => 'number', 'required' => true, 'value' => $service->sort_order, 'hint' => 'Lower numbers show first.'])
        </div>
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_active" value="1" class="rounded border-stone-300" @checked(old('is_active', $service->is_active))>
            Show on website
        </label>
        <button class="rounded-lg bg-teal-700 px-6 py-2 font-medium text-white hover:bg-teal-800">Save</button>
    </form>

    @if ($service->exists)
        <form method="POST" action="{{ route('admin.services.destroy', $service) }}" class="mt-6"
              onsubmit="return confirm('Delete this service?')">
            @csrf @method('DELETE')
            <button class="text-sm text-red-600 hover:underline">Delete service</button>
        </form>
    @endif
</x-layouts.admin>
