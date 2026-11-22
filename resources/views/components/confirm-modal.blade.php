{{-- One shared confirm dialog, opened by any <form data-confirm="..."> (see app.js). --}}
<dialog id="confirm-modal" class="modal" aria-labelledby="confirm-title">
    <div class="w-[26rem] max-w-full rounded-2xl bg-white p-6 shadow-lift">
        <div class="flex gap-4">
            <span class="grid size-11 shrink-0 place-items-center rounded-full bg-red-100 text-red-600">
                <x-icon name="warning" class="size-6" />
            </span>
            <div>
                <h2 id="confirm-title" data-confirm-title class="text-base font-semibold text-slate-900">Are you sure?</h2>
                <p data-confirm-message class="mt-1 text-sm text-slate-600"></p>
            </div>
        </div>
        <div class="mt-6 flex justify-end gap-2">
            <button type="button" data-confirm-cancel class="btn btn-secondary">Cancel</button>
            <button type="button" data-confirm-accept class="btn btn-danger">Delete</button>
        </div>
    </div>
</dialog>
