<?php

namespace App\Http\Controllers\Api\Common;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Http\Resources\ActivityLogResource;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * @OA\Get(
     *     path="/activity-logs",
     *     summary="Get all activity logs (Admin only)",
     *     tags={"Activity Logs"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         description="Filter by user ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="action",
     *         in="query",
     *         description="Filter by action type",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="model_type",
     *         in="query",
     *         description="Filter by model type",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="from_date",
     *         in="query",
     *         description="Filter logs from date",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="to_date",
     *         in="query",
     *         description="Filter logs to date",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=50)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Activity logs retrieved successfully"
     *     ),
     *     @OA\Response(response=403, description="Unauthorized - Admin only")
     * )
     */
    public function index(Request $request)
    {
        // Check if user is admin
        if (!$request->user()->isAdmin()) {
            return $this->errorResponse('Only administrators can view all activity logs', 403);
        }

        $query = ActivityLog::with('user')->orderBy('created_at', 'desc');

        // Filter by user
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by action
        if ($request->has('action')) {
            $query->where('action', $request->action);
        }

        // Filter by model type
        if ($request->has('model_type')) {
            $query->where('model_type', $request->model_type);
        }

        // Filter by model ID
        if ($request->has('model_id')) {
            $query->where('model_id', $request->model_id);
        }

        // Date range filter
        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Search in description
        if ($request->has('search')) {
            $query->where('description', 'LIKE', '%' . $request->search . '%');
        }

        // Paginate
        $perPage = $request->get('per_page', 50);
        $logs = $query->paginate($perPage);

        return $this->successResponse([
            'logs' => ActivityLogResource::collection($logs),
            'pagination' => [
                'total' => $logs->total(),
                'per_page' => $logs->perPage(),
                'current_page' => $logs->currentPage(),
                'last_page' => $logs->lastPage(),
                'from' => $logs->firstItem(),
                'to' => $logs->lastItem(),
            ]
        ], 'Activity logs retrieved successfully');
    }

    /**
     * @OA\Get(
     *     path="/activity-logs/my",
     *     summary="Get current user's activity logs",
     *     tags={"Activity Logs"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="action",
     *         in="query",
     *         description="Filter by action type",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="from_date",
     *         in="query",
     *         description="Filter logs from date",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="to_date",
     *         in="query",
     *         description="Filter logs to date",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=50)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User activity logs retrieved successfully"
     *     )
     * )
     */
    public function myLogs(Request $request)
    {
        $query = ActivityLog::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc');

        // Filter by action
        if ($request->has('action')) {
            $query->where('action', $request->action);
        }

        // Date range filter
        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Paginate
        $perPage = $request->get('per_page', 50);
        $logs = $query->paginate($perPage);

        // Get action summary
        $actionSummary = ActivityLog::where('user_id', $request->user()->id)
            ->selectRaw('action, COUNT(*) as count')
            ->groupBy('action')
            ->orderByDesc('count')
            ->limit(10)
            ->get()
            ->pluck('count', 'action')
            ->toArray();

        return $this->successResponse([
            'logs' => ActivityLogResource::collection($logs),
            'pagination' => [
                'total' => $logs->total(),
                'per_page' => $logs->perPage(),
                'current_page' => $logs->currentPage(),
                'last_page' => $logs->lastPage(),
                'from' => $logs->firstItem(),
                'to' => $logs->lastItem(),
            ],
            'summary' => [
                'total_actions' => $logs->total(),
                'top_actions' => $actionSummary,
            ]
        ], 'User activity logs retrieved successfully');
    }
}
