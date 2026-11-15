<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'action' => ['nullable', 'string', 'max:50', 'alpha_dash'],
            'q'      => ['nullable', 'string', 'max:100'],
        ]);

        $logs = ActivityLog::with('user')
            ->when($request->action, fn ($q, $a) => $q->where('action', $a))
            ->when($request->q, fn ($q, $s) => $q->where('description', 'like', '%' . $s . '%')) // parameter binding → aman dari SQLi
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $actions = ActivityLog::select('action')->distinct()->orderBy('action')->pluck('action');

        return view('admin.activity_log.index', compact('logs', 'actions'));
    }
}
