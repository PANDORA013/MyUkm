<?php

namespace App\Http\Controllers;

use App\Helpers\BroadcastHelper;
use App\Models\Chat;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use App\Events\ChatMessageSent;
use App\Jobs\ProcessChatMessage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Routing\Controller as BaseController;

/**
 * @method \App\Models\User user()
 */
class ChatController extends BaseController
{
    private const MESSAGE_HISTORY_LIMIT = 100;
    private const TYPING_TIMEOUT = 3; // seconds
    private const CACHE_TTL = 3600; // 1 hour

    public function __construct()
    {
        $this->middleware('auth');
        // Allow all authenticated users to access chat features
        // Role-based access will be controlled in individual methods based on group membership
    }

    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user instanceof \App\Models\User) {
            return redirect()->route('login');
        }

        $activeGroupId = Session::get("active_group_id");

        if ($request->has("join")) {
            return redirect()->route("ukm.index");
        }

        if ($user->groups()->count() === 0) {
            return redirect()->route("ukm.index")
                ->with("info", "Silahkan bergabung dengan UKM terlebih dahulu");
        }

        $group = Group::findOrFail($activeGroupId);
        // Ambil chat terbaru langsung dari relasi
        $chats = $group->chats()->with('user')->orderBy('created_at', 'desc')->limit(self::MESSAGE_HISTORY_LIMIT)->get()->reverse();
        
        // Check if user is muted
        $userMembership = $group->users()->where('user_id', $user->id)->first();
        $isMuted = $userMembership && $userMembership->pivot->is_muted;


        return view("chat", [
            "chats" => $chats,
            "groupName" => $group->name,
            "memberCount" => $group->members()->count(),
            "groupCode" => $group->referral_code,
            "groupId" => $group->id,
            "typingTimeout" => self::TYPING_TIMEOUT,
            "isMuted" => $isMuted,
        ]);
    }

    public function showChat($code)
    {
        $user = Auth::user();
        $group = Group::where("referral_code", $code)->firstOrFail();
        Session::put("active_group_id", $group->id);

        $chats = $group->chats()->with('user')->orderBy('created_at', 'desc')->limit(self::MESSAGE_HISTORY_LIMIT)->get()->reverse();
        
        // Check if user is muted
        $userMembership = $group->users()->where('user_id', $user->id)->first();
        $isMuted = $userMembership && $userMembership->pivot->is_muted;

        return view("chat", [
            "chats" => $chats,
            "groupName" => $group->name,
            "memberCount" => $group->members()->count(),
            "groupCode" => $code,
            "groupId" => $group->id,
            "typingTimeout" => self::TYPING_TIMEOUT,
            "isMuted" => $isMuted,
        ]);
    }

    public function sendChat(Request $request)
    {
        try {
            Log::info('Chat send attempt', [
                'user_id' => Auth::id(),
                'group_code' => $request->group_code
            ]);

            if (!Auth::check()) {
                Log::warning('Unauthorized chat attempt - user not authenticated');
                return response()->json([
                    "status" => "error",
                    "message" => "Sesi Anda telah berakhir. Silakan login kembali."
                ], 401);
            }

            $user = Auth::user();

            // Validasi input
            $request->validate([
                "message" => "required|string|min:1|max:1000",
                "group_code" => "required|string|exists:groups,referral_code"
            ], [
                "message.required" => "Pesan tidak boleh kosong",
                "message.max" => "Pesan terlalu panjang (maksimal 1000 karakter)",
                "message.min" => "Pesan tidak boleh kosong",
                "group_code.exists" => "Grup tidak ditemukan"
            ]);

            $group = Group::where("referral_code", $request->group_code)->first();
            if (!$group) {
                return response()->json([
                    "status" => "error",
                    "message" => "Grup tidak ditemukan"
                ], 404);
            }

            // Cek keanggotaan user pada grup
            $groupMembership = $group->users()->where("user_id", $user->id)->first();
            if (!$groupMembership) {
                return response()->json([
                    "status" => "error",
                    "message" => "Anda tidak tergabung dalam UKM ini"
                ], 403);
            }

            // Cek mute
            if ($groupMembership->pivot->is_muted) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Halo, kamu lagi di-mute dulu, biar suasana grup tetap adem kayak es kopi susu~ Balik ngobrol lagi nanti ya!'
                ], 403);
            }

            // Bersihkan pesan (XSS, emoji, link)
            $message = $this->filterMessage($request->message);

            // Simpan chat
            $chat = Chat::create([
                "user_id" => $user->id,
                "group_id" => $group->id,
                "message" => $message
            ]);

            Log::info('Chat message created', [
                'chat_id' => $chat->id,
                'user_id' => $user->id,
                'group_id' => $group->id
            ]);

            // Broadcast event (jika real-time) with safe broadcasting
            BroadcastHelper::safeBroadcast(new ChatMessageSent($chat));

            return response()->json([
                "status" => "success",
                "message" => "Pesan terkirim",
                "data" => [
                    "id" => $chat->id,
                    "message" => $chat->message,
                    "time" => $chat->created_at->format("H:i")
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Chat validation error', [
                'errors' => $e->validator->errors()->toArray()
            ]);
            return response()->json([
                "status" => "error",
                "message" => $e->validator->errors()->first()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Chat error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                "status" => "error",
                "message" => "Terjadi kesalahan. Silakan coba lagi."
            ], 500);
        }
    }

    /**
     * Filter pesan chat (XSS, emoji, link)
     */
    private function filterMessage(string $message): string
    {
        $message = htmlspecialchars($message, ENT_QUOTES, "UTF-8");
        $message = preg_replace(
            "/(https?:\/\/[^\s<]+)/i",
            "<a href=\"$1\" target=\"_blank\" rel=\"noopener noreferrer\" class=\"text-blue-500 hover:underline\">$1</a>",
            $message
        );
        $emojis = [":)" => "😊", ":(" => "😢", ":D" => "😀", ";)" => "😉", "<3" => "❤️", ":p" => "😛", ":P" => "😛", ":o" => "😮", ":O" => "😮"];
        return str_replace(array_keys($emojis), array_values($emojis), $message);
    }

    public function logoutGroup()
    {
        Session::forget("active_group_id");
        return redirect()->route("ukm.index");
    }

    public function getUnreadCount(Request $request)
    {
        try {
            $groupId = $request->input('group_id');
            $count = Chat::where('group_id', $groupId)
                ->where('user_id', '!=', Auth::id())
                ->whereNull('read_at')
                ->count();

            return response()->json([
                'status' => 'success',
                'count' => $count
            ]);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil jumlah pesan belum dibaca'
            ], 500);
        }
    }

    public function typing(Request $request)
    {
        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $groupId = $request->input('group_id');
            
            $group = Group::findOrFail($groupId);
            
            // Pastikan user adalah anggota grup
            if (!$user->groups()->where('group_id', $groupId)->exists()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda bukan anggota grup ini'
                ], 403);
            }
            
            // Broadcast typing event safely
            BroadcastHelper::safeBroadcast(new \App\Events\MessageTyping([
                'user_id' => $user->id,
                'name' => $user->name,
                'group_id' => $groupId,
                'group_code' => $group->referral_code
            ]));
            
            return response()->json([
                'status' => 'success'
            ]);
        } catch (\Exception $e) {
            Log::error('Typing indicator error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengirim indikator mengetik'
            ], 500);
        }
    }
    
    public function joinGroup(Request $request)
    {
        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $groupId = $request->input('group_id');
            
            $group = Group::findOrFail($groupId);
            
            // Pastikan user adalah anggota grup
            if (!$user->groups()->where('group_id', $groupId)->exists()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda bukan anggota grup ini'
                ], 403);
            }
            
            // Set session active group
            Session::put('active_group_id', $groupId);
            
            // Update online status
            $onlineUsers = $this->getOnlineUsers($groupId);
            $onlineCount = $onlineUsers ? $onlineUsers->count() : 0;
            $totalMembers = $group->users ? $group->users()->count() : 0;
            
            // Broadcast user joining safely
            BroadcastHelper::safeBroadcast(new \App\Events\UserOnline([
                'user_id' => $user->id,
                'name' => $user->name,
                'group_id' => $groupId,
                'group_code' => $group->referral_code,
                'online_count' => $onlineCount,
                'total_members' => $totalMembers
            ]));
            
            return response()->json([
                'status' => 'success',
                'online_count' => $onlineCount,
                'total_members' => $totalMembers
            ]);
        } catch (\Exception $e) {
            Log::error('Join group error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal bergabung dengan chat'
            ], 500);
        }
    }
    
    private function getOnlineUsers($groupId)
    {
        try {
            $group = Group::findOrFail($groupId);
            return $group->users()->where('last_seen_at', '>=', now()->subMinutes(5))->get();
        } catch (\Exception $e) {
            Log::error('Error getting online users', [
                'error' => $e->getMessage(),
                'group_id' => $groupId
            ]);
            return collect();
        }
    }
    
    /**
     * Get messages for a specific group
     *
     * @param string $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMessages($code)
    {
        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $group = Group::where('referral_code', $code)->firstOrFail();
            
            // Check if user is member of the group
            if (!$user->groups()->where('group_id', $group->id)->exists()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            $messages = Chat::where('group_id', $group->id)
                ->with('user:id,name')
                ->orderBy('created_at', 'desc')
                ->limit(self::MESSAGE_HISTORY_LIMIT)
                ->get()
                ->reverse()
                ->values();
            
            return response()->json([
                'messages' => $messages,
                'group_name' => $group->name
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting messages', [
                'error' => $e->getMessage(),
                'code' => $code
            ]);
            
            return response()->json(['error' => 'Failed to get messages'], 500);
        }
    }
    
    /**
     * Send message to a specific group
     *
     * @param Request $request
     * @param string $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMessage(Request $request, $code)
    {
        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $group = Group::where('referral_code', $code)->firstOrFail();
            
            // Check if user is member of the group
            if (!$user->groups()->where('group_id', $group->id)->exists()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            $request->validate([
                'message' => 'required|string|max:1000'
            ]);
            
            $chat = Chat::create([
                'user_id' => $user->id,
                'group_id' => $group->id,
                'message' => $request->message
            ]);
            
            $chat->load('user:id,name');
            
            // Broadcast the message
            event(new ChatMessageSent($chat, $group->referral_code));
            
            return response()->json([
                'status' => 'success',
                'message' => $chat
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error sending message', [
                'error' => $e->getMessage(),
                'code' => $code
            ]);
            
            return response()->json(['error' => 'Failed to send message'], 500);
        }
    }

    /**
     * Get messages for AJAX requests from chat.blade.php
     * This method handles requests with group_id parameter
     */
    public function getMessagesAjax(Request $request)
    {
        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            
            if (!$user instanceof \App\Models\User) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not authenticated'
                ], 401);
            }

            $groupId = $request->query('group_id');
            
            if (!$groupId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Group ID is required'
                ], 400);
            }

            $group = Group::findOrFail($groupId);
            
            // Check if user is member of the group
            if (!$user->groups()->where('group_id', $group->id)->exists()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized access to this group'
                ], 403);
            }
            
            $messages = Chat::where('group_id', $group->id)
                ->with('user:id,name')
                ->orderBy('created_at', 'desc')
                ->limit(self::MESSAGE_HISTORY_LIMIT)
                ->get()
                ->reverse()
                ->values()
                ->map(function ($message) {
                    return [
                        'id' => $message->id,
                        'message' => $message->message,
                        'user_id' => $message->user_id,
                        'name' => $message->user->name,
                        'created_at' => $message->created_at->toISOString(),
                    ];
                });
            
            return response()->json([
                'status' => 'success',
                'messages' => $messages,
                'group_name' => $group->name
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Group not found for messages request', [
                'group_id' => $request->query('group_id'),
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Group not found'
            ], 404);
            
        } catch (\Exception $e) {
            Log::error('Error loading messages via AJAX', [
                'error' => $e->getMessage(),
                'group_id' => $request->query('group_id'),
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to load messages'
            ], 500);
        }
    }

    /**
     * Get online members in current group
     */
    public function getOnlineMembers(Request $request)
    {
        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $groupId = Session::get('active_group_id');
            
            if (!$groupId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No active group'
                ], 400);
            }
            
            // Verify user is member of the group
            if (!$user->groups()->where('group_id', $groupId)->exists()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized access to group'
                ], 403);
            }
            
            $onlineMembers = User::getOnlineMembersInGroup($groupId);
            $group = Group::find($groupId);
            $totalMembers = $group ? $group->users()->count() : 0;
            
            return response()->json([
                'status' => 'success',
                'online_count' => $onlineMembers->count(),
                'total_members' => $totalMembers,
                'online_members' => $onlineMembers->map(function ($member) {
                    return [
                        'id' => $member->id,
                        'name' => $member->name,
                        'photo' => $member->photo,
                        'last_seen_at' => $member->last_seen_at->toISOString(),
                        'last_seen_human' => $member->last_seen_at->diffForHumans(),
                        'is_current_user' => $member->id === Auth::id()
                    ];
                }),
                'timestamp' => now()->toISOString()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting online members', [
                'error' => $e->getMessage(),
                'group_id' => Session::get('active_group_id'),
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get online members'
            ], 500);
        }
    }

    /**
     * Update user's online status dengan optimasi broadcasting
     */
    public function updateOnlineStatus(Request $request)
    {
        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $groupId = Session::get('active_group_id');
            
            if (!$groupId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No active group'
                ], 400);
            }
            
            // Check if user was previously offline (untuk menghindari broadcast berlebihan)
            $wasOnline = $user->isOnline();
            
            // Update last_seen_at
            $user->update(['last_seen_at' => now()]);
            
            // Get group info
            $group = Group::find($groupId);
            if (!$group) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Group not found'
                ], 404);
            }
            
            // Get updated online members and count
            $onlineMembers = User::getOnlineMembersInGroup($groupId);
            $totalMembers = $group->users()->count();
            
            // Broadcast hanya jika status berubah dari offline ke online atau setiap 2 menit
            $shouldBroadcast = !$wasOnline || 
                              (!$user->last_broadcast_at || 
                               $user->last_broadcast_at->diffInMinutes(now()) >= 2);
            
            if ($shouldBroadcast) {
                // Update last broadcast time
                $user->update(['last_broadcast_at' => now()]);
                
                // Broadcast dengan data yang lebih lengkap
                broadcast(new \App\Events\UserOnlineStatusChanged(
                    $user->id,
                    $user->name,
                    true,
                    $group->referral_code,
                    $onlineMembers->map(function ($member) {
                        return [
                            'id' => $member->id,
                            'name' => $member->name,
                            'last_seen_at' => $member->last_seen_at->toISOString()
                        ];
                    })->toArray(),
                    $totalMembers
                ));
                
                Log::info('Online status broadcasted', [
                    'user_id' => $user->id,
                    'group_code' => $group->referral_code,
                    'online_count' => $onlineMembers->count(),
                    'was_online' => $wasOnline
                ]);
            }
            
            return response()->json([
                'status' => 'success',
                'online_count' => $onlineMembers->count(),
                'total_members' => $totalMembers,
                'is_online' => true,
                'message' => 'Online status updated',
                'broadcast_sent' => $shouldBroadcast,
                'timestamp' => now()->toISOString()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error updating online status', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'group_id' => Session::get('active_group_id')
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update online status'
            ], 500);
        }
    }
}
