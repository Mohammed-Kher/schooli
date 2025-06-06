<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\ParentStudent;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    /**
     * Get all conversations for the authenticated user
     */
    public function getConversations(Request $request)
    {
        $user = Auth::user();
        $userType = $this->getUserType($user);

        if (!$userType) {
            return response()->json(['error' => 'User type not found'], 400);
        }

        $conversations = Conversation::with([
            'parentStudent.user:id,name',
            'teacher.user:id,name',
            'messages' => function ($query) {
                $query->latest()->limit(1);
            }
        ])
            ->where(function ($query) use ($userType, $user) {
                if ($userType === 'parent') {
                    $parentStudent = ParentStudent::where('user_id', $user->id)->first();
                    $query->where('parent_student_id', $parentStudent->id);
                } elseif ($userType === 'teacher') {
                    $teacher = Teacher::where('user_id', $user->id)->first();
                    $query->where('teacher_id', $teacher->id);
                } else {
                    throw new \Exception('Invalid user type');
                }
            })
            ->orderBy('last_message_at', 'desc')
            ->get();

        return response()->json([
            'conversations' => $conversations->map(function ($conversation) use ($userType) {
                return [
                    'id' => $conversation->id,
                    'title' => $conversation->title,
                    'participant' => $userType === 'parent'
                        ? [
                            'id' => $conversation->teacher->id,
                            'name' => $conversation->teacher->name,
                            // 'image' => $conversation->teacher->user->image,
                            'type' => 'teacher'
                        ]
                        : [
                            'id' => $conversation->parentStudent->id,
                            'name' => $conversation->parentStudent->user->name,
                            // 'image' => $conversation->parentStudent->user->image,
                            'type' => 'parent'
                        ],
                    'last_message' => $conversation->messages->first() ? [
                        'content' => $conversation->messages->first()->content,
                        'created_at' => $conversation->messages->first()->created_at,
                        'is_mine' => $this->isMessageMine($conversation->messages->first(), $userType)
                    ] : null,
                    'unread_count' => $this->getUnreadCount(null, $conversation->id, $userType),
                    'last_message_at' => $conversation->last_message_at
                ];
            })
        ]);
    }

    /**
     * Start a new conversation or get existing one
     */
    public function startConversation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'recipient_id' => 'required|integer',
            'recipient_type' => 'required|in:parent,teacher',
            'title' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $userType = $this->getUserType($user);

        if (!$userType) {
            return response()->json(['error' => 'User type not found'], 400);
        }

        // Prevent starting conversation with same user type
        if ($userType === $request->recipient_type) {
            return response()->json(['error' => 'Cannot start conversation with same user type'], 400);
        }
        if ($request->recipient_type !== 'teacher' && $request->recipient_type !== 'parent') {
            return response()->json(['error' => 'Recipient type must be either parent or teacher'], 400);
        }


        try {
            DB::beginTransaction();
            // Get or create conversation
            if ($userType === 'parent') {
                $parentStudent = ParentStudent::where('user_id', $user->id)->first();
                $conversation = Conversation::firstOrCreate([
                    'parent_student_id' => $parentStudent->id,
                    'teacher_id' => $request->recipient_id,
                ], [
                    'title' => $request->title
                ]);
            } elseif ($userType === 'teacher') {
                $teacher = Teacher::where('user_id', $user->id)->first();
                $conversation = Conversation::firstOrCreate([
                    'parent_student_id' => $request->recipient_id,
                    'teacher_id' => $teacher->id,
                ], [
                    'title' => $request->title
                ]);
            }

            DB::commit();

            return response()->json([
                'conversation' => [
                    'id' => $conversation->id,
                    'title' => $conversation->title,
                    'created_at' => $conversation->created_at
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => "Failed to create conversation: {$e->getMessage()}"], 500);
        }
    }

    /**
     * Get messages for a specific conversation
     */
    public function getMessages(Request $request, $conversationId)
    {
        $user = Auth::user();
        $userType = $this->getUserType($user);

        if (!$userType) {
            return response()->json(['error' => 'User type not found'], 400);
        }

        // Verify user has access to this conversation
        $conversation = $this->getConversationForUser($conversationId, $userType, $user);
        if (!$conversation) {
            return response()->json(['error' => 'Conversation not found'], 404);
        }

        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 50);

        $messages = Message::with(['sender', 'sender.user:id,name'])
            ->where('conversation_id', $conversationId)
            ->orderBy('created_at', 'desc')->get();


        // ->paginate($perPage, ['*'], 'page', $page);

        // Mark messages as read
        $this->markMessagesAsRead($conversationId, $userType, $user);

        return response()->json([
            'messages' => $messages,
            // 'pagination' => [
            //         'current_page' => $messages->currentPage(),
            //         'total_pages' => $messages->lastPage(),
            //         'total' => $messages->total(),
            //         'per_page' => $messages->perPage()
            //     ]
        ]);
    }

    /**
     * Send a message
     */
    public function sendMessage(Request $request, $conversationId)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string|max:5000',
            // 'attachments' => 'nullable|array',
            // 'attachments.*' => 'file|max:10240' // 10MB max per file
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $userType = $this->getUserType($user);

        if (!$userType) {
            return response()->json(['error' => 'User type not found'], 400);
        }

        // Verify user has access to this conversation
        $conversation = $this->getConversationForUser($conversationId, $userType, $user);
        if (!$conversation) {
            return response()->json(['error' => 'Conversation not found'], 404);
        }

        DB::beginTransaction();
        try {
            // Handle attachments
            // $attachments = [];
            // if ($request->hasFile('attachments')) {
            //     foreach ($request->file('attachments') as $file) {
            //         $path = $file->store('chat-attachments', 'public');
            //         $attachments[] = [
            //             'name' => $file->getClientOriginalName(),
            //             'path' => $path,
            //             'size' => $file->getSize(),
            //             'mime_type' => $file->getMimeType()
            //         ];
            //     }
            // }

            // Get sender information
            $senderType = $userType === 'parent' ? 'App\Models\ParentStudent' : 'App\Models\Teacher';
            $senderId = $userType === 'parent'
                ? ParentStudent::where('user_id', $user->id)->first()->id
                : Teacher::where('user_id', $user->id)->first()->id;

            // Create message
            $message = Message::create([
                'conversation_id' => $conversationId,
                'sender_type' => $senderType,
                'sender_id' => $senderId,
                'content' => $request->content,
                // 'attachments' => !empty($attachments) ? $attachments : null
            ]);

            // Update conversation's last message time
            $conversation->update(['last_message_at' => now()]);

            DB::commit();

            return response()->json([
                'message' => [
                    'id' => $message->id,
                    'content' => $message->content,
                    // 'attachments' => $message->attachments,
                    'created_at' => $message->created_at,
                    'sender' => [
                        'name' => $user->name,
                        'type' => $userType
                    ]
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Failed to send message'], 500);
        }
    }

    /**
     * Mark messages as read
     */
    public function markAsRead(Request $request, $conversationId)
    {
        $user = Auth::user();
        $userType = $this->getUserType($user);

        if (!$userType) {
            return response()->json(['error' => 'User type not found'], 400);
        }

        // Verify user has access to this conversation
        $conversation = $this->getConversationForUser($conversationId, $userType, $user);
        if (!$conversation) {
            return response()->json(['error' => 'Conversation not found'], 404);
        }

        $this->markMessagesAsRead($conversationId, $userType, $user);

        return response()->json(['message' => 'Messages marked as read']);
    }

    /**
     * Delete a message
     */
    public function deleteMessage($messageId)
    {
        $user = Auth::user();
        $userType = $this->getUserType($user);

        if (!$userType) {
            return response()->json(['error' => 'User type not found'], 400);
        }

        $message = Message::find($messageId);
        if (!$message) {
            return response()->json(['error' => 'Message not found'], 404);
        }

        if ($userType === 'parent') {
            $parentStudent = ParentStudent::where('user_id', $user->id)->first();
            $isOwner = $message->sender_type === 'App\Models\ParentStudent' &&
                $message->sender_id === $parentStudent->id;
        } else {
            $teacher = Teacher::where('user_id', $user->id)->first();
            $isOwner = $message->sender_type === 'App\Models\Teacher' &&
                $message->sender_id === $teacher->id;
        }

        if (!$isOwner) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $message->delete();

        return response()->json(['message' => 'Message deleted successfully']);
    }

    // Helper methods

    private function getUserType($user)
    {
        if (ParentStudent::where('user_id', $user->id)->exists()) {
            return 'parent';
        }
        if (Teacher::where('user_id', $user->id)->exists()) {
            return 'teacher';
        }
        if (User::find($user->id)) {
            return 'admin';
        }

        return null;
    }

    private function getConversationForUser($conversationId, $userType, $user)
    {
        $query = Conversation::where('id', $conversationId);

        if ($userType === 'parent') {
            $parentStudent = ParentStudent::where('user_id', $user->id)->first();
            $query->where('parent_student_id', $parentStudent->id);
        } else {
            $teacher = Teacher::where('user_id', $user->id)->first();
            $query->where('teacher_id', $teacher->id);
        }

        return $query->first();
    }

    private function isMessageMine($message, $userType)
    {
        if ($userType === 'parent') {
            return $message->sender_type === 'App\Models\ParentStudent';
        } else {
            return $message->sender_type === 'App\Models\Teacher';
        }
    }

    public function getUnreadCount(?Request $request = null, ?int $conversationId = 0, ?string $userType = null)
    {
        if (!$userType) {
            $userType = $this->getUserType(auth()->user());
        }
        if (!$conversationId) {
            $conversationId = $request->validate([
                'conversation_id' => 'required|exists:conversations,id',
            ]);
        }
        $senderType = $userType === 'parent' ? 'App\Models\Teacher' : 'App\Models\ParentStudent';

        $unread = Message::where('conversation_id', $conversationId)
            ->where('sender_type', $senderType)
            ->whereNull('read_at')
            ->count();
            return $unread;
    }

    public function UnreadCount(?Request $request = null, ?int $conversationId = 0, ?string $userType = null)
    {
        if (!$userType) {
            $userType = $this->getUserType(auth()->user());
        }
        if (!$conversationId) {
            $conversationId = $request->validate([
                'conversation_id' => 'required|exists:conversations,id',
            ]);
        }
        $senderType = $userType === 'parent' ? 'App\Models\Teacher' : 'App\Models\ParentStudent';

        $unread = Message::where('conversation_id', $conversationId)
            ->where('sender_type', $senderType)
            ->whereNull('read_at')
            ->count();
            return response()->json([
                'data' => $unread,
                'status' => self::HTTP_OK,
                'message' => self::RETRIEVED
            ]
            );
    }

    private function markMessagesAsRead($conversationId, $userType, $user)
    {
        $senderType = $userType === 'parent' ? 'App\Models\Teacher' : 'App\Models\ParentStudent';

        Message::where('conversation_id', $conversationId)
            ->where('sender_type', $senderType)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }
}