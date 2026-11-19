@php
    $type = $type ?? 'text';
    $value = old($name, $value ?? '');
    $classes = 'mt-1 w-full rounded-lg border bg-white px-3 py-2 focus:border-teal-600 focus:outline-none '
        .($errors->has($name) ? 'border-red-400' : 'border-stone-300');
@endphp
<div>
    <label for="{{ $name }}" class="block text-sm font-medium text-stone-700">
        {{ $label }} @if (! empty($required))<span class="text-red-500">*</span>@endif
    </label>
    @if ($type === 'textarea')
        <textarea id="{{ $name }}" name="{{ $name }}" rows="{{ $rows ?? 5 }}" class="{{ $classes }}" @required(! empty($required))>{{ $value }}</textarea>
    @else
        <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}" value="{{ $value }}" class="{{ $classes }}" @required(! empty($required))>
    @endif
    @if (! empty($hint)) <p class="mt-1 text-xs text-stone-500">{{ $hint }}</p> @endif
    @error($name) <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>
