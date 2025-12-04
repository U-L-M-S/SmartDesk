<?php

namespace Modules\Tickets\Controllers;

use App\Http\Controllers\Controller;
use Modules\Tickets\Models\Ticket;
use Modules\Tickets\Models\TicketComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketCommentController extends Controller
{
    public function store(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'comment' => 'required|string',
            'is_internal' => 'boolean',
        ]);

        $ticket->comments()->create([
            'user_id' => Auth::id(),
            'comment' => $validated['comment'],
            'is_internal' => $validated['is_internal'] ?? false,
        ]);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Comment added successfully.');
    }

    public function destroy(Ticket $ticket, TicketComment $comment)
    {
        if ($comment->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        $comment->delete();

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Comment deleted successfully.');
    }
}
