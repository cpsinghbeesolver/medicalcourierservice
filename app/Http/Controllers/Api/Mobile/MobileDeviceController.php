<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\UserDevice;
use Illuminate\Http\Request;

class MobileDeviceController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/mobile/v1/device/register",
     *     summary="Save Device Token",
     *     description="",
     *     tags={"Mobile - Driver"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"fcm_token", "device_type"},
     *             @OA\Property(property="fcm_token", type="string", format="", example="device_token_here", description=""),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Device registered successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Device registered successfully."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="latitude", type="number"),
     *                 @OA\Property(property="longitude", type="number"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=403, description="Not a driver"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function register(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
            'device_type' => 'required|in:android,ios',
        ]);

        UserDevice::updateOrCreate(
            [
                'fcm_token' => $request->fcm_token,
            ],
            [
                'user_id'     => auth()->id(),
                'device_type' => $request->device_type,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Device registered successfully.'
        ]);
    }


     /**
     * @OA\Post(
     *     path="/api/mobile/v1/device/unregister",
     *     summary="Save Device Token",
     *     description="",
     *     tags={"Mobile - Driver"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"fcm_token", "device_type"},
     *             @OA\Property(property="fcm_token", type="string", format="", example="device_token_here", description=""),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Device removed successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Device removed successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="latitude", type="number"),
     *                 @OA\Property(property="longitude", type="number"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=403, description="Not a driver"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function unregister(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
        ]);

        UserDevice::where('fcm_token', $request->fcm_token)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Device removed successfully.'
        ]);
    }
}
