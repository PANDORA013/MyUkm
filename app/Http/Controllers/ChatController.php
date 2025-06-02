<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Events\ChatMessageSent;

class ChatController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        $activeGroupId = Session::get('active_group_id');

        if (request()->has('join')) {
            return redirect()->route('ukm.index');
        }

        if ($user->groups()->count() === 0) {
            return redirect()->route('ukm.index')
                ->with('info', 'Silahkan bergabung dengan UKM terlebih dahulu');
        }

        if (!$activeGroupId) {
            return redirect()->route('ukm.index');
        }

        $group = Group::findOrFail($activeGroupId);
        
        if (!$user->groups()->where('group_id', $group->id)->exists()) {
            return redirect()->route('ukm.index')
                ->with('error', 'Anda tidak tergabung dalam UKM ini');
        }

        $chats = Chat::with('user')
            ->where('group_code', $group->referral_code)
            ->orderBy('created_at')
            ->get();

        return view('chat.index', [
            'chats' => $chats,
            'groupName' => $group->name,
            'groupCode' => $group->referral_code,
        ]);
    }

    public function showChat($code)
    {
        /** @var User $user */
        $user = Auth::user();
        $group = Group::where('referral_code', $code)->firstOrFail();
        
        if (!$user->groups()->where('group_id', $group->id)->exists()) {
            return redirect()->route('ukm.index')
                ->with('error', 'Anda belum tergabung dengan UKM ini');
        }

        // Store active group in session
        Session::put('active_group_id', $group->id);

        $chats = Chat::with('user')
            ->where('group_code', $code)
            ->orderBy('created_at')
            ->get();

        return view('chat.index', [
            'chats' => $chats,
            'groupName' => $group->name,
            'groupCode' => $code,
        ]);
    }

    public function sendChat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'group_code' => 'required|string'
        ]);

        /** @var User $user */
        $user = Auth::user();
        $group = Group::where('referral_code', $request->group_code)->firstOrFail();
        
        if (!$user->groups()->where('group_id', $group->id)->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak tergabung dalam UKM ini'
            ], 403);
        }

        $chat = Chat::create([
            'user_id' => Auth::id(),
            'group_code' => $request->group_code,
            'message' => $request->message
        ]);

        broadcast(new ChatMessageSent($chat))->toOthers();

        return response()->json([
            'status' => 'success',
            'message' => 'Pesan terkirim'
        ]);
    }

    public function logoutGroup()
    {
        Session::forget('active_group_id');
        return redirect()->route('ukm.index');
    }
}
