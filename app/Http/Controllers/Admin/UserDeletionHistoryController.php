<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserDeletionHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UserDeletionHistoryController extends Controller
{
    /**
     * Display a listing of deleted users.
     */
    public function index()
    {
        // Hanya admin website yang bisa melihat riwayat penghapusan
        if (!Gate::allows('isAdminWebsite')) {
            abort(403, 'Unauthorized action.');
        }

        $deletions = UserDeletionHistory::with('deletedBy')
            ->latest()
            ->paginate(20);

        return view('admin.user_deletions.index', compact('deletions'));
    }

    /**
     * Display the specified deleted user record.
     */
    public function show(string $id)
    {
        if (!Gate::allows('isAdminWebsite')) {
            abort(403, 'Unauthorized action.');
        }

        $deletion = UserDeletionHistory::with('deletedBy')->findOrFail($id);
        
        return view('admin.user_deletions.show', compact('deletion'));
    }

    // Method lain tidak digunakan untuk saat ini
    public function create() { abort(404); }
    public function store(Request $request) { abort(404); }
    public function edit(string $id) { abort(404); }
    public function update(Request $request, string $id) { abort(404); }
    public function destroy(string $id) { abort(404); }
}
