<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = AuditLog::with('user');

        if ($action = $request->input('action')) {
            $query->where('action', 'like', "%{$action}%");
        }

        if ($userId = $request->input('user_id')) {
            $query->where('user_id', $userId);
        }

        $logs = $query->latest()->paginate(25)->withQueryString();

        return view('admin.audit-logs.index', compact('logs'));
    }
}
