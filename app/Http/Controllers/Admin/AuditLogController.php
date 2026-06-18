<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function __invoke(Request $request): View
    {
        $validated = $request->validate([
            'category' => ['nullable', 'in:financial,security,operational,master_data'],
        ]);

        return view('admin.audit-logs', [
            'auditLogs' => AuditLog::query()
                ->with('user')
                ->when(
                    $validated['category'] ?? null,
                    fn ($query, string $category) => $query->where('category', $category),
                )
                ->latest('created_at')
                ->paginate(30)
                ->withQueryString(),
        ]);
    }
}
