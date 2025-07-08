<?php

namespace App\Http\Controllers;

use App\Helpers\BroadcastHelper;
use App\Models\Chat;
use App\Models\Group;
use App\Models\User;
use App\Services\ChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use App\Events\ChatMessageSent;
use App\Jobs\ProcessChatMessage;
use App\Jobs\BroadcastChatMessage;
use App\Jobs\BroadcastOnlineStatus;
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

    public function __construct(
        private ChatService $chatService
    ) {
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
        
        // Use ChatService to get chat data
        $chatData = $this->chatService->getChatData($user, $group);
        
        return view("chat", array_merge($chatData, [
            "typingTimeout" => self::TYPING_TIMEOUT,
        ]));
    }

    public function showChat($code)
    {
        $user = Auth::user();
        $group = Group::where("referral_code", $code)->firstOrFail();
        Session::put("active_group_id", $group->id);

        // Use ChatService to get chat data
        $chatData = $this->chatService->getChatData($user, $group);
        
        return view("chat", array_merge($chatData, [
            "typingTimeout" => self::TYPING_TIMEOUT,
        ]));
    }

    /**
     * DEPRECATED: Old sendChat method replaced by sendMessage for better performance
     * This method is kept for backward compatibility but should not be used
     * 
     * @deprecated Use sendMessage() instead for asynchronous queue-based broadcasting
     */
    public function sendChat(Request $request)
    {
        // Redirect to new method for consistency
        Log::warning('DEPRECATED: sendChat method called - redirecting to sendMessage', [
            'user_id' => Auth::id(),
            'group_code' => $request->group_code
        ]);
        
        // Extract group code and call new method
        $groupCode = $request->group_code;
        if (!$groupCode) {
            return response()->json([
                'status' => 'error',
                'message' => 'Group code required'
            ], 400);
        }
        
        return $this->sendMessage($request, $groupCode);
    }

    public function logoutGroup()
    {
        Session::forget("active_group_id");
        return redirect()->route("ukm.index");
    }

    public function getUnreadCount(Request $request)
    {
        try {
            $user = Auth::user();
            $groupId = $request->input('group_id');
            
            $result = $this->chatService->getUnreadCount($user, $groupId);
            
            return response()->json([
                'status' => 'success',
                'count' => $result['count']
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting unread count', [
                'user_id' => Auth::id(),
                'group_id' => $request->input('group_id'),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get unread count'
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
            
            // OPTIMIZED: Cache untuk performa maksimal dan response cepat
            $cacheKey = "chat_messages_{$group->id}";
            
            $messages = Cache::remember($cacheKey, 30, function() use ($group) {
                return Chat::where('group_id', $group->id)
                    ->with('user:id,name')
                    ->orderBy('created_at', 'desc')
                    ->limit(self::MESSAGE_HISTORY_LIMIT)
                    ->get()
                    ->reverse()
                    ->values();
            });
            
            return response()->json([
                'messages' => $messages,
                'group_name' => $group->name
            ], 200, [], JSON_UNESCAPED_UNICODE);
            
        } catch (\Exception $e) {
            Log::error('Error getting messages', [
                'error' => $e->getMessage(),
                'code' => $code
            ]);
            
            return response()->json(['error' => 'Failed to get messages'], 500);
        }
    }
    
    /**
     * Send message via asynchronous queue-based broadcasting (RECOMMENDED)
     * This method provides better performance through background job processing
     *
     * @param Request $request
     * @param string $code Group referral code
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMessage(Request $request, $code)
    {
        try {
            Log::info('Chat send attempt (async)', [
                'user_id' => Auth::id(),
                'group_code' => $code
            ]);

            /** @var \App\Models\User $user */
            $user = Auth::user();
            
            if (!$user) {
                Log::warning('Unauthorized chat attempt - user not authenticated');
                return response()->json([
                    'status' => 'error',
                    'message' => 'Sesi Anda telah berakhir. Silakan login kembali.'
                ], 401);
            }

            // Validate input
            $request->validate([
                'message' => 'required|string|min:1|max:1000'
            ], [
                'message.required' => 'Pesan tidak boleh kosong',
                'message.max' => 'Pesan terlalu panjang (maksimal 1000 karakter)',
                'message.min' => 'Pesan tidak boleh kosong'
            ]);

            $group = Group::where('referral_code', $code)->first();
            if (!$group) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Grup tidak ditemukan'
                ], 404);
            }

            // Check if user is member of the group
            $groupMembership = $group->users()->where('user_id', $user->id)->first();
            if (!$groupMembership) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda tidak tergabung dalam UKM ini'
                ], 403);
            }

            // Check mute status
            if ($groupMembership->pivot->is_muted) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Halo, kamu lagi di-mute dulu, biar suasana grup tetap adem kayak es kopi susu~ Balik ngobrol lagi nanti ya!'
                ], 403);
            }

            // Filter message for security
            $message = $request->message;

            // Create chat message
            $chat = Chat::create([
                'user_id' => $user->id,
                'group_id' => $group->id,
                'message' => $message
            ]);
            
            // Load required relationships for broadcasting
            $chat->load(['user:id,name', 'group:id,referral_code']);

            Log::info('Chat message created (async)', [
                'chat_id' => $chat->id,
                'user_id' => $user->id,
                'group_id' => $group->id,
                'group_code' => $code
            ]);

            // Dispatch message broadcasting to queue for INSTANT responsiveness
            try {
                // ULTRA-OPTIMIZED: Dispatch dengan priority tinggi dan timeout minimal
                dispatch(new BroadcastChatMessage($chat))
                    ->onQueue('realtime') // Queue khusus real-time untuk prioritas tertinggi
                    ->delay(0); // No delay untuk instant processing
                
                Log::info('Chat broadcast job dispatched successfully (instant)', [
                    'chat_id' => $chat->id,
                    'queue' => 'realtime'
                ]);
                
                // TAMBAHAN: Instant response tanpa menunggu queue processing
                return response()->json([
                    'status' => 'success',
                    'message' => 'Pesan berhasil dikirim',
                    'data' => [
                        'id' => $chat->id,
                        'message' => $chat->message,
                        'user_id' => $chat->user_id,
                        'name' => $user->name,
                        'created_at' => $chat->created_at->toISOString(),
                        'timestamp' => $chat->created_at->timestamp
                    ]
                ], 200, [], JSON_UNESCAPED_UNICODE);
                
                Log::info('Chat message dispatched to queue', [
                    'chat_id' => $chat->id,
                    'user_id' => $user->id,
                    'group_code' => $group->referral_code
                ]);
            } catch (\Exception $queueException) {
                // If queue fails, log error but don't fail the request
                Log::error('Failed to dispatch chat message to queue', [
                    'chat_id' => $chat->id,
                    'error' => $queueException->getMessage()
                ]);
                
                // Fallback: broadcast directly (synchronous)
                try {
                    event(new ChatMessageSent($chat));
                    Log::info('Chat message broadcasted synchronously as fallback');
                } catch (\Exception $broadcastException) {
                    Log::error('Failed to broadcast chat message (fallback)', [
                        'chat_id' => $chat->id,
                        'error' => $broadcastException->getMessage()
                    ]);
                }
            }
            
            return response()->json([
                'status' => 'success',
                'message' => 'Pesan terkirim',
                'data' => [
                    'id' => $chat->id,
                    'message' => $chat->message,
                    'time' => $chat->created_at->format('H:i'),
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name
                    ]
                ],
                'timestamp' => now()->toISOString()
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Chat validation error (async)', [
                'errors' => $e->validator->errors()->toArray()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => $e->validator->errors()->first()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error sending message (async)', [
                'error' => $e->getMessage(),
                'code' => $code,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan. Silakan coba lagi.'
            ], 500);
        }
    }

    /**
     * Get messages for AJAX requests from chat.blade.php - OPTIMIZED
     * This method handles requests with group_id parameter and supports pagination
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
            $afterId = $request->query('after', 0); // TAMBAHAN: untuk load pesan setelah ID tertentu
            $limit = min($request->query('limit', self::MESSAGE_HISTORY_LIMIT), 50); // TAMBAHAN: limit maksimal 50
            
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
            
            // OPTIMIZED: Query dengan kondisi after untuk load pesan terbaru saja
            $query = Chat::where('group_id', $group->id)
                ->with('user:id,name');
            
            if ($afterId > 0) {
                // Load hanya pesan yang lebih baru dari afterId
                $query->where('id', '>', $afterId);
                $query->orderBy('created_at', 'asc'); // ASC untuk pesan baru
            } else {
                // Load pesan awal
                $query->orderBy('created_at', 'desc');
            }
            
            $messages = $query->limit($limit)
                ->get()
                ->map(function ($message) {
                    return [
                        'id' => $message->id,
                        'message' => $message->message,
                        'user_id' => $message->user_id,
                        'name' => $message->user->name,
                        'created_at' => $message->created_at->toISOString(),
                        'timestamp' => $message->created_at->timestamp
                    ];
                });
            
            // Reverse jika bukan query after (pesan awal)
            if ($afterId == 0) {
                $messages = $messages->reverse()->values();
            }
            
            return response()->json([
                'status' => 'success',
                'messages' => $messages,
                'group_name' => $group->name,
                'total_messages' => $messages->count(),
                'after_id' => $afterId
            ], 200, [], JSON_UNESCAPED_UNICODE);
            
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
                
                // Dispatch online status broadcasting to queue for better performance
                dispatch(new BroadcastOnlineStatus($user->id, true, $group->referral_code));
                
                Log::info('Online status dispatched to queue', [
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