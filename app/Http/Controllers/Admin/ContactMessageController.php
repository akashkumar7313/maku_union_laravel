<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Services\AuditLogger;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.contact-messages.index', [
            'messages' => ContactMessage::query()
                ->when($request->filled('unread'), fn ($q) => $q->whereNull('read_at'))
                ->latest()
                ->paginate(config('maku.pagination.admin'))
                ->withQueryString(),
            'unreadCount' => ContactMessage::query()->whereNull('read_at')->count(),
        ]);
    }

    public function show(ContactMessage $message, AuditLogger $audit)
    {
        if (! $message->read_at) {
            $message->update(['read_at' => now()]);
            $audit->log('contact_message.read', $message, ['read_at' => null], ['read_at' => $message->read_at?->toIso8601String()]);
        }

        return view('admin.contact-messages.show', ['message' => $message]);
    }

    public function destroy(ContactMessage $message, AuditLogger $audit)
    {
        $audit->log('contact_message.deleted', $message, ['subject' => $message->subject], []);
        $message->delete();

        return redirect()->route('admin.contact-messages.index')->with('success', 'संदेश हटा दिया गया।');
    }
}
