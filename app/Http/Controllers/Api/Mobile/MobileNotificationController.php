<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Services\FirebaseService;

class MobileNotificationController extends Controller
{
    /**
     * @OA\Get(
     *     path="/send-notification",
     *     summary="Get pending job requests",
     *     description="Returns all pending delivery requests for the current driver",
     *     tags={"Mobile - Send Notification"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page",
     *         required=false,
     *         @OA\Schema(type="integer", example=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Pending job requests",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="requests", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="pagination", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=403, description="Not a driver")
     * )
     */
    public function send(Request $request, FirebaseService $firebase)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'title'   => 'required|string',
            'body'    => 'required|string',
        ]);

        $user = User::findOrFail($request->user_id);

        if (!$user->device_token) {
            return response()->json([
                'success' => false,
                'message' => 'User has no registered device.',
            ], 404);
        }

        $firebase->sendToToken(
            $user->device_token,
            $request->title,
            $request->body,
            'mobile',
            $user->id,
            [
                'type' => 'general',
                'user_id' => (string) $user->id,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Notification sent successfully.',
        ]);
    }


    /**
     * @OA\Get(
     *     path="/get-notification",
     *     summary="Get notifications",
     *     description="",
     *     tags={"Mobile - Send Notification"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Pending job requests",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="requests", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="pagination", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=403, description="Not a driver")
     * )
     */
    function getNotifications(Request $request){
        $notifications = auth()->user()
        ->notifications()
        ->where('type', $request->get('type', 'mobile'))
        ->latest()
        ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'notifications' => $notifications,
        ]);
    }

    /**
     * @OA\Put
     *     path="/notifications/{notification}/read",
     *     summary="Mark notification as read",
     *     description="",
     *     tags={"Mobile - Send Notification"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Pending job requests",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="requests", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="pagination", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=403, description="Not a driver")
     * )
     */
    public function markAsRead($id, Request $request)
    {
        $notification = auth()->user()
        ->notifications()
        ->where('type', $request->get('type', 'mobile'))
        ->findOrFail($id);
        $notification->update(['is_read' => '1']);

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read.',
        ]);
    }

    /**
     * @OA\Get
     *     path="/notifications/read-all",
     *     summary="Mark notification as read all",
     *     description="",
     *     tags={"Mobile - Send Notification"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=403, description="Not a driver")
     * )
     */
    public function markAsReadAll()
    {
        auth()->user()->notifications()->update(['is_read' => '1']);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read.',
        ]);
    }

    /**
    /**
     * @OA\Get
     *     path="/notification/has-unread",
     *     summary="Check if there are unread notifications",
     *     description="",
     *     tags={"Mobile - Send Notification"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=403, description="Not a driver")
     * )
     */
    public function hasUnread()
    {
        $hasUnread = auth()->user()->notifications()->where('is_read', '0')->exists();

        return response()->json([
            'success' => true,
            'has_unread' => $hasUnread,
        ]);
    }

    public function saveToken(Request $request)
    {
        $request->validate([
            'device_token' => 'required|string',
        ]);

        $user = auth()->user();
        $user->device_token = $request->device_token;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Device token saved successfully.',
        ]);
    }
}
