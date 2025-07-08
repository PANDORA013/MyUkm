<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\UKM;
use Illuminate\Http\Request;

class GroupsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $groups = Group::with('ukm')->paginate(15);
        return view('admin.groups.index', compact('groups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $ukms = UKM::all();
        return view('admin.groups.create', compact('ukms'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'referral_code' => 'required|string|unique:groups',
            'description' => 'nullable|string',
            'ukm_id' => 'required|exists:ukms,id',
        ]);

        Group::create([
            'name' => $request->name,
            'referral_code' => $request->referral_code,
            'description' => $request->description,
            'ukm_id' => $request->ukm_id,
            'is_active' => true,
        ]);

        return redirect()->route('admin.admin.groups.index')
                         ->with('success', 'Group created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $group = Group::with(['ukm', 'users'])->findOrFail($id);
        return view('admin.groups.show', compact('group'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $group = Group::findOrFail($id);
        $ukms = UKM::all();
        return view('admin.groups.edit', compact('group', 'ukms'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $group = Group::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'referral_code' => 'required|string|unique:groups,referral_code,' . $group->id,
            'description' => 'nullable|string',
            'ukm_id' => 'required|exists:ukms,id',
        ]);

        $group->update([
            'name' => $request->name,
            'referral_code' => $request->referral_code,
            'description' => $request->description,
            'ukm_id' => $request->ukm_id,
        ]);

        return redirect()->route('admin.admin.groups.index')
                         ->with('success', 'Group updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $group = Group::findOrFail($id);
        $group->delete();

        return redirect()->route('admin.admin.groups.index')
                         ->with('success', 'Group deleted successfully.');
    }
}
