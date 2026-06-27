<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ContactMessageController extends Controller
{
    public function index(Request $request): Response
    {
        $query = ContactMessage::query()->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            });
        }

        if ($request->filled('read')) {
            match ($request->read) {
                'unread' => $query->where('is_read', false),
                'read' => $query->where('is_read', true),
                default => null,
            };
        }

        return Inertia::render('Admin/Contact/Index', [
            'messages' => $query->paginate(15)->withQueryString(),
            'filters' => $request->only(['search', 'read']),
            'unreadCount' => ContactMessage::where('is_read', false)->count(),
        ]);
    }

    public function show(ContactMessage $message): Response
    {
        if (! $message->is_read) {
            $message->update(['is_read' => true]);
        }

        return Inertia::render('Admin/Contact/Show', [
            'message' => $message,
        ]);
    }

    public function markRead(ContactMessage $message): RedirectResponse
    {
        $message->update(['is_read' => true]);

        return back()->with('success', 'Marked as read.');
    }

    public function destroy(ContactMessage $message): RedirectResponse
    {
        $message->delete();

        return redirect()->route('admin.contact.index')->with('success', 'Message deleted.');
    }
}
