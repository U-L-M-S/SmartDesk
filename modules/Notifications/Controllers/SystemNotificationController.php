<?php

namespace Modules\Notifications\Controllers;

use App\Http\Controllers\Controller;
use Modules\Notifications\Models\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SystemNotificationController extends Controller
{
    public function index()
    {
        $notifications = SystemNotification::with('creator')
            ->latest()
            ->paginate(20);

        return view('system-notifications.index', compact('notifications'));
    }

    public function create()
    {
        return view('system-notifications.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:info,success,warning,error',
            'target' => 'required|in:all,role,specific',
            'target_data' => 'nullable|array',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
            'is_active' => 'boolean',
        ]);

        $validated['created_by'] = Auth::id();

        SystemNotification::create($validated);

        return redirect()->route('system-notifications.index')
            ->with('success', 'System notification created successfully.');
    }

    public function show(SystemNotification $systemNotification)
    {
        $systemNotification->load('creator');
        return view('system-notifications.show', compact('systemNotification'));
    }

    public function edit(SystemNotification $systemNotification)
    {
        return view('system-notifications.edit', compact('systemNotification'));
    }

    public function update(Request $request, SystemNotification $systemNotification)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:info,success,warning,error',
            'target' => 'required|in:all,role,specific',
            'target_data' => 'nullable|array',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
            'is_active' => 'boolean',
        ]);

        $systemNotification->update($validated);

        return redirect()->route('system-notifications.show', $systemNotification)
            ->with('success', 'System notification updated successfully.');
    }

    public function destroy(SystemNotification $systemNotification)
    {
        $systemNotification->delete();

        return redirect()->route('system-notifications.index')
            ->with('success', 'System notification deleted successfully.');
    }

    public function active()
    {
        $notifications = SystemNotification::active()
            ->forUser(Auth::user())
            ->get();

        return response()->json($notifications);
    }
}
