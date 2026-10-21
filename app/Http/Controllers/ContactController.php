<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Models\Branch;
use App\Models\ContactMessage;
use App\Models\TeamMember;
use App\Services\AuditLogger;

class ContactController extends Controller
{
    public function index()
    {
        return view('contact', [
            'branches' => Branch::query()->active()->get(),
            'teamMembers' => TeamMember::query()->active()->get(),
        ]);
    }

    public function store(StoreContactRequest $request, AuditLogger $audit)
    {
        $message = ContactMessage::create($request->validated() + ['ip' => $request->ip()]);
        $audit->log('contact_message.received', $message, [], ['name' => $message->name, 'subject' => $message->subject]);

        return redirect()->route('contact')->with('success', 'आपका संदेश सफलतापूर्वक भेज दिया गया है। हम शीघ्र संपर्क करेंगे।');
    }
}
