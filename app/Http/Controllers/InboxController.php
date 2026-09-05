<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use App\Models\InboxMessage;
use App\Models\SocialAccount;
use App\Enums\InboxMessageStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InboxController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'agency']);
    }

    public function index(Request $request)
    {
        $agency = $request->user()->agency;

        $query = InboxMessage::where('agency_id', $agency->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('platform')) {
            $query->where('platform', $request->platform);
        }
        if ($request->filled('type')) {
            $query->where('message_type', $request->type);
        }

        $messages = $query->orderBy('received_at', 'desc')->paginate(20);

        $unreadCount = InboxMessage::where('agency_id', $agency->id)->unread()->count();

        return view('inbox.index', compact('agency', 'messages', 'unreadCount'));
    }

    public function show(Request $request, InboxMessage $message)
    {
        $agency = $request->user()->agency;

        if ((int)$message->agency_id !== (int)$agency->id) {
            abort(403);
        }

        if ($message->status === InboxMessageStatus::UNREAD->value) {
            InboxMessage::markRead($message);
        }

        return view('inbox.show', compact('agency', 'message'));
    }

    public function triage(Request $request, InboxMessage $message)
    {
        $agency = $request->user()->agency;

        if ((int)$message->agency_id !== (int)$agency->id) {
            abort(403);
        }

        $validated = $request->validate([
            'action' => 'required|in:auto_reply,auto_triage,escalate,mark_read,mark_important,ignore',
            'sentiment' => 'nullable|in:positive,negative,neutral',
            'category' => 'nullable|in:feedback,question,complaint,praise,spam,inquiry',
            'reply_content' => 'nullable|string|max:2000',
        ]);

        // Update triage
        $message->triage()->updateOrCreate(
            ['inbox_message_id' => $message->id],
            [
                'action' => $validated['action'],
                'sentiment' => $validated['sentiment'] ?? null,
                'category' => $validated['category'] ?? null,
                'triage_at' => now(),
            ]
        );

        // Update message status
        $message->update(['status' => InboxMessageStatus::TRIAGED->value]);

        // If auto-reply, mark as replied
        if ($validated['action'] === 'auto_reply' && !empty($validated['reply_content'])) {
            InboxMessage::markReplied($message, $validated['reply_content'], $request->user());
        }

        return back()->with('success', 'Message triaged successfully.');
    }

    public function reply(Request $request, InboxMessage $message)
    {
        $agency = $request->user()->agency;

        if ((int)$message->agency_id !== (int)$agency->id) {
            abort(403);
        }

        $validated = $request->validate([
            'reply_content' => 'required|string|max:2000',
        ]);

        InboxMessage::markReplied($message, $validated['reply_content'], $request->user());

        return back()->with('success', 'Reply sent successfully.');
    }

    public function destroy(Request $request, InboxMessage $message)
    {
        $agency = $request->user()->agency;

        if ((int)$message->agency_id !== (int)$agency->id) {
            abort(403);
        }

        $message->delete();

        return redirect()->route('inbox.index')
            ->with('success', 'Message deleted.');
    }
}
