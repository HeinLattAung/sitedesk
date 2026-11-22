@php
    $type = $type ?? 'text';
    $value = old($name, $value ?? '');
    $hasError = $errors->has($name);
    $classes = 'input'.($hasError ? ' input-error' : '');
@endphp
<div>
    <div class="flex items-baseline justify-between">
        <label for="{{ $name }}" class="label">
            {{ $label }} @if (! empty($required))<span class="text-red-500">*</span>@endif
        </label>
        @if (! empty($maxlength))
            <span id="{{ $name }}-count" class="text-xs text-slate-500 tabular-nums"></span>
        @endif
    </div>
    @if ($type === 'textarea')
        <textarea id="{{ $name }}" name="{{ $name }}" rows="{{ $rows ?? 5 }}" class="{{ $classes }} resize-y leading-relaxed"
                  placeholder="{{ $placeholder ?? '' }}" @required(! empty($required))
                  @if (! empty($maxlength)) maxlength="{{ $maxlength }}" data-counter="{{ $name }}-count" @endif
                  @if (! empty($autosize)) data-autosize @endif
                  @if ($hasError) aria-invalid="true" aria-describedby="{{ $name }}-error" @endif>{{ $value }}</textarea>
    @else
        <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}" value="{{ $value }}" class="{{ $classes }}"
               placeholder="{{ $placeholder ?? '' }}" autocomplete="{{ $autocomplete ?? 'on' }}" @required(! empty($required))
               @if ($hasError) aria-invalid="true" aria-describedby="{{ $name }}-error" @endif>
    @endif
    @if (! empty($hint) && ! $hasError) <p class="hint">{{ $hint }}</p> @endif
    @error($name)
        <p id="{{ $name }}-error" class="error"><x-icon name="warning" class="size-4" /> {{ $message }}</p>
    @enderror
</div>
