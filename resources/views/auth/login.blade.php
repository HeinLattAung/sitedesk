<x-layouts.site title="Staff login">
    <section class="mx-auto max-w-md px-4 py-16">
        <h1 class="text-2xl font-bold text-stone-900">Staff login</h1>
        <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-5 rounded-xl border border-stone-200 bg-white p-6 shadow-sm">
            @csrf
            @include('partials.field', ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true])
            @include('partials.field', ['name' => 'password', 'label' => 'Password', 'type' => 'password', 'required' => true, 'value' => ''])
            <label class="flex items-center gap-2 text-sm text-stone-600">
                <input type="checkbox" name="remember" value="1" class="rounded border-stone-300"> Remember me
            </label>
            <button class="w-full rounded-lg bg-teal-700 px-6 py-3 font-medium text-white hover:bg-teal-800">Log in</button>
        </form>
    </section>
</x-layouts.site>
