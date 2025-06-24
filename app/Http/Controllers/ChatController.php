<?php

namespace App\Http\Controllers;

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
        $this->middleware('CheckGroupMembership')->only(['index', 'showChat', 'sendChat']);
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
        $chats = $this->getGroupChats($group);
        $this->markMessagesAsRead($group->id);

        return view("chat", [
            "chats" => $chats,
            "groupName" => $group->name,
            "memberCount" => $group->users()->count(),
            "groupCode" => $group->referral_code,
            "groupId" => $group->id,
            "typingTimeout" => self::TYPING_TIMEOUT,
        ]);
    }

    public function showChat($code)
    {
        $group = Group::where("referral_code", $code)->firstOrFail();
        Session::put("active_group_id", $group->id);

        $chats = $this->getGroupChats($group);
        $this->markMessagesAsRead($group->id);

        return view("chat", [
            "chats" => $chats,
            "groupName" => $group->name,
            "memberCount" => $group->users()->count(),
            "groupCode" => $code,
            "groupId" => $group->id,
            "typingTimeout" => self::TYPING_TIMEOUT,
        ]);
    }

    private function markMessagesAsRead($groupId)
    {
        Chat::where('group_id', $groupId)
            ->where('user_id', '!=', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
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

            // Get user and validate session
            $user = Auth::user();

            // Ensure chat session (active_group_id) exists, otherwise derive from group_code
            if (!session()->has('active_group_id')) {
                if ($request->filled('group_code')) {
                    $possibleGroup = Group::where('referral_code', $request->group_code)->first();
                    if ($possibleGroup && $user->groups()->where('group_id', $possibleGroup->id)->exists()) {
                        session(['active_group_id' => $possibleGroup->id]);
                    } else {
                        return response()->json([
                            'status'  => 'error',
                            'message' => 'Sesi chat tidak valid. Silakan refresh halaman.'
                        ], 403);
                    }
                } else {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'Sesi chat tidak valid. Silakan refresh halaman.'
                    ], 403);
                }
            }

            // Rate limiting: 30 messages per minute
            if (!$this->checkRateLimit()) {
                return response()->json([
                    "status" => "error",
                    "message" => "Terlalu banyak pesan. Mohon tunggu beberapa saat."
                ], 429);
            }

            $request->validate([
                "message" => "required|string|min:1|max:1000",
                "group_code" => "required|string|exists:groups,referral_code"
            ], [
                "message.required" => "Pesan tidak boleh kosong",
                "message.max" => "Pesan terlalu panjang (maksimal 1000 karakter)",
                "message.min" => "Pesan tidak boleh kosong",
                "group_code.exists" => "Grup tidak ditemukan"
            ]);

            /** @var \App\Models\User $user */
            $user = Auth::user();
            $group = Group::where("referral_code", $request->group_code)->first();
            
            if (!$group || !$user->groups()->where("group_id", $group->id)->exists()) {
                return response()->json([
                    "status" => "error",
                    "message" => "Anda tidak tergabung dalam UKM ini"
                ], 403);
            }

            // Check if user is muted in this group
            $membership = $user->groups()->where('group_id', $group->id)->first()->pivot;
            if ($membership && $membership->is_muted) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Anda sedang dimute oleh admin grup dan tidak dapat mengirim pesan.'
                ], 403);
            }

            // Create chat record with original message
            $chat = Chat::create([
                "user_id" => $user->id,
                "group_id" => $group->id,
                "message" => $request->message
            ]);

            // Log chat creation
            Log::info('Chat message created', [
                'chat_id' => $chat->id,
                'user_id' => $user->id,
                'group_id' => $group->id
            ]);


            // Process message in background job
            ProcessChatMessage::dispatch($chat, $request->message);

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

    public function logoutGroup()
    {
        Session::forget("active_group_id");
        return redirect()->route("ukm.index");
    }

    /**
     * Check if user has exceeded rate limit
     */
    private function checkRateLimit(): bool
    {
        $key = "chat_rate_limit_" . Auth::id();
        $limit = 30; // messages
        $timeWindow = 60; // seconds

        $currentCount = cache()->get($key, 0);
        
        if ($currentCount >= $limit) {
            return false;
        }

        cache()->put($key, $currentCount + 1, $timeWindow);
        return true;
    }

    /**
     * Filter inappropriate content from message
     */
    private function filterMessage(string $message): string
    {
        // Basic XSS prevention
        $message = htmlspecialchars($message, ENT_QUOTES, "UTF-8");
        
        // Convert URLs to clickable links
        $message = preg_replace(
            "/(https?:\/\/[^\s<]+)/i",
            "<a href=\"$1\" target=\"_blank\" rel=\"noopener noreferrer\" class=\"text-blue-500 hover:underline\">$1</a>",
            $message
        );

        // Basic emoji support
        $emojis = [
            ":)" => "😊",
            ":(" => "😢",
            ":D" => "😀",
            ";)" => "😉",
            "<3" => "❤️",
            ":p" => "😛",
            ":P" => "😛",
            ":o" => "😮",
            ":O" => "😮",
        ];

        return str_replace(array_keys($emojis), array_values($emojis), $message);
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

    private function checkGroupMembership(User $user, int $groupId): bool
    {
        $cacheKey = "group_membership:{$user->id}:{$groupId}";
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($user, $groupId) {
            return $user->groups()->where('group_id', $groupId)->exists();
        });
    }

    private function getGroupChats(Group $group)
    {
        $cacheKey = "group_chats:{$group->id}";
        
        return Cache::remember($cacheKey, 30, function () use ($group) {
            return $group->chats()
                ->with('user')
                ->orderBy("created_at", "desc")
                ->limit(self::MESSAGE_HISTORY_LIMIT)
                ->get()
                ->reverse();
        });
    }

    private function clearGroupChatsCache(int $groupId)
    {
        Cache::forget("group_chats:{$groupId}");
    }
}
