<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Models\Inquiry;
use App\Models\Service;
use App\Notifications\NewInquiryReceived;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function create(): View
    {
        return view('contact', ['services' => Service::active()->get()]);
    }

    public function store(ContactRequest $request): RedirectResponse
    {
        $inquiry = Inquiry::create([
            ...$request->safe()->except('website'),
            'ip_address' => $request->ip(),
        ]);

        if ($to = config('sitedesk.notify_email')) {
            Notification::route('mail', $to)->notify(new NewInquiryReceived($inquiry));
        }

        return redirect()->route('contact')
            ->with('status', 'Thanks! We received your message and will reply within one business day.');
    }
}
