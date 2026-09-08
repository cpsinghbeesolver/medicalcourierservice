<?php

namespace App\Http\Controllers\Api\Mobile;

use Illuminate\Http\Request;
use App\Models\ChatConversation;
use App\Http\Controllers\Controller;

class ChatController extends Controller
{
    public function createConversation(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:users,id',
        ]);

        $driverId = $request->user()->id;
        $companyId = $request->company_id;

        $conversation = ChatConversation::firstOrCreate(
            [
                'company_id' => $companyId,
                'driver_id' => $driverId,
            ]
        );

        return response()->json([
            'success' => true,
            'data' => $conversation,
        ]);
    }


    public function conversations(Request $request)
    {
        // dd($request->user());
        $user = $request->user();

        $query = ChatConversation::with([
            'company:id,name',
            'driver:id,name',
            'latestMessage.sender:id,name',
        ]);
        // dd($user->id);
        if ($user->role_id == 2) {
            // Company
            $query->where('company_id', $user->id);
        } elseif ($user->role_id == 4) {
            // Driver
            $query->where('driver_id', $user->id);
        }

        $conversations = $query
            ->orderByDesc('last_message_at')
            ->get();
        if ($user->role_id == 2) {
            $html = view('company.chat-conversations', compact('conversations'))->render();
            return response()->json([
                'success' => true,
                'html' => $html,
                'data' => $conversations,
            ]);
        }else{
            return response()->json([
                'success' => true,
                'data' => $conversations,
            ]);
        }
    }

    public function messages(Request $request, ChatConversation $conversation)
    {
        $userId = $request->user()->id;

        if (
            $conversation->company_id !== $userId &&
            $conversation->driver_id !== $userId
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $messages = $conversation->messages()
            ->with('sender:id,name')
            ->orderBy('created_at', 'asc')
            ->paginate(50);

        return response()->json([
            'success' => true,
            'data' => $messages,
        ]);
    }

    public function sendMessage(Request $request,ChatConversation $conversation) {
        $request->validate([
            'message' => [
                'required',
                'string',
                'max:5000',
            ],
        ]);

        $userId = $request->user()->id;

        if (
            $conversation->company_id !== $userId &&
            $conversation->driver_id !== $userId
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $message = $conversation->messages()->create([
            'sender_id' => $userId,
            'message' => trim($request->message),
        ]);

        $conversation->update([
            'last_message_at' => now(),
        ]);

        $message->load('sender:id,name');

        return response()->json([
            'success' => true,
            'data' => $message,
        ], 201);
    }

    public function markAsRead( Request $request,ChatConversation $conversation) {
        $userId = $request->user()->id;

        if (
            $conversation->company_id !== $userId &&
            $conversation->driver_id !== $userId
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $conversation->messages()
            ->where('sender_id', '!=', $userId)
            ->whereNull('read_at')
            ->update([
                'read_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Messages marked as read.',
        ]);
    }
}
