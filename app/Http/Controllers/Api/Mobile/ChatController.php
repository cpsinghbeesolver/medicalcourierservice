<?php

namespace App\Http\Controllers\Api\Mobile;

use Illuminate\Http\Request;
use App\Models\ChatConversation;
use App\Models\Delivery;
use App\Http\Controllers\Controller;
use App\Events\ChatMessageSent;
use App\Jobs\SendFirebaseNotificationJob;
use App\Models\User;

class ChatController extends Controller
{
    public function createJobConversation($id){
        $delivery = Delivery::find($id);
        if($delivery){
            $companyId = auth()->id();
            $conversation = ChatConversation::firstOrCreate(
                [
                    'company_id' => $companyId,
                    'driver_id' => $delivery->driver_id,
                    'delivery_id' => $delivery->id
                ]
            );
            return view('company.chat',compact('delivery'));
        }else{
            return back()->with('error', 'Delivery not found');
        }
    }
    public function createConversation(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:users,id',
        ]);

        $driverId = $request->user()->id;
        $companyId = $request->company_id;
        $delivery_id = $request->delivery_id;

        $conversation = ChatConversation::firstOrCreate(
            [
                'company_id' => $companyId,
                'driver_id' => $driverId,
                'delivery_id' => $delivery_id
            ]
        );

        return response()->json([
            'success' => true,
            'data' => $conversation,
        ]);
    }

    public function createConversationCompany(Request $request)
    {
        $request->validate([
            'driver_id' => 'required|exists:users,id',
        ]);

        $companyId = $request->user()->id;
        $driverId = $request->driver_id;
        $delivery_id = $request->delivery_id;

        $conversation = ChatConversation::firstOrCreate(
            [
                'company_id' => $companyId,
                'driver_id' => $driverId,
                'delivery_id' => $delivery_id
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
            'delivery:id,delivery_number,status'
        ]);
        // Only include conversations whose delivery is not delivered or failed
        $query->where(function ($q) {
            $q->whereNull('delivery_id')
            ->orWhereHas('delivery', function ($q) {
                $q->whereNotIn('status', ['delivered', 'failed']);
            });
        });
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
        // dd($conversations);
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
        $user = $request->user();
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
            ->with('sender:id,name,profile_photo')
            ->orderBy('created_at', 'asc')
            ->paginate(50);

        if ($user->role_id == 2) {
            $html = view('company.chat-messages', compact('messages'))->render();
            return response()->json([
                'success' => true,
                'html' => $html,
                'data' => $messages,
            ]);
        }else{
            return response()->json([
                'success' => true,
                'data' => $messages,
            ]);
        }
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
        broadcast(new ChatMessageSent($message))->toOthers();
        
        $driver = User::find($conversation->driver_id);
        $company = User::find($conversation->company_id);
        if($request->user()->role_id == '2'){
            //Send Notification to driver
            
            $title = "New message from Company";
            $body = "You have received a new message from Company. Please check it.";
            SendFirebaseNotificationJob::dispatch(
                $driver->device_token,
                $title,
                $body,
                'mobile',
                $conversation->driver_id,
                [
                    'type' => 'chat',
                    'company_id' => (string) $conversation->company_id,
                    'driver_id' => (string) $conversation->driver_id,
                    'conversation_id' => (string) $conversation->driver_id,
                    'delivery_id' => (string) $conversation->delivery_id
                ]
            );
        }

        if($request->user()->role_id == '4'){
            //Send Notification to Company
            
            $title = "New message from ".$driver->name;
            $body = "You have received a new message from ".$driver->name.". Please check it.";
            SendFirebaseNotificationJob::dispatch(
                $company->device_token,
                $title,
                $body,
                'web',
                $conversation->company_id,
                [
                    'type' => 'chat',
                    'company_id' => (string) $conversation->company_id,
                    'driver_id' => (string) $conversation->driver_id,
                    'conversation_id' => (string) $conversation->driver_id,
                    'delivery_id' => (string) $conversation->delivery_id
                ]
            );
        }

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
