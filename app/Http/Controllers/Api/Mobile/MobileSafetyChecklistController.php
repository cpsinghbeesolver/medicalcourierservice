<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\SafetyChecklist;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class MobileSafetyChecklistController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/mobile/v1/safety-checklist/today",
     *     summary="Get today's safety checklist",
     *     description="Returns the safety checklist for the current driver for today",
     *     tags={"Mobile - Safety Checklist"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Today's safety checklist",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object", nullable=true)
     *         )
     *     ),
     *     @OA\Response(response=403, description="Not a driver")
     * )
     */
    public function today(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'driver') {
            return response()->json([
                'success' => false,
                'message' => 'This endpoint is only for drivers'
            ], 403);
        }

        $checklist = SafetyChecklist::where('driver_id', $user->id)
            ->whereDate('checklist_date', today())
            ->first();

        return response()->json([
            'success' => true,
            'data' => $checklist ? [
                'id' => $checklist->id,
                'checklist_date' => $checklist->checklist_date->toDateString(),
                'checklist_type' => $checklist->checklist_type,
                'vehicle_checks' => [
                    'lights_functional' => $checklist->lights_functional,
                    'tire_pressure_checked' => $checklist->tire_pressure_checked,
                    'windshield_cleaned' => $checklist->windshield_cleaned,
                    'vehicle_locked' => $checklist->vehicle_locked,
                ],
                'etiquette_checks' => [
                    'id_badge_visible' => $checklist->id_badge_visible,
                ],
                'compliance_checks' => [
                    'secure_phi_containers' => $checklist->secure_phi_containers,
                    'secure_transport_containers' => $checklist->secure_transport_containers,
                ],
                'equipment_checks' => [
                    'biohazard_bags_available' => $checklist->biohazard_bags_available,
                    'gloves_available' => $checklist->gloves_available,
                    'extra_leakproof_bags' => $checklist->extra_leakproof_bags,
                ],
                'all_checks_passed' => $checklist->all_checks_passed,
                'completed_at' => $checklist->completed_at?->toIso8601String(),
                'created_at' => $checklist->created_at->toIso8601String(),
            ] : null
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/mobile/v1/safety-checklist",
     *     summary="Submit safety checklist",
     *     description="Creates or updates the safety checklist for today",
     *     tags={"Mobile - Safety Checklist"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="checklist_type", type="string", enum={"pre_duty", "post_duty"}, example="pre_duty"),
     *             @OA\Property(property="lights_functional", type="boolean"),
     *             @OA\Property(property="tire_pressure_checked", type="boolean"),
     *             @OA\Property(property="windshield_cleaned", type="boolean"),
     *             @OA\Property(property="vehicle_locked", type="boolean"),
     *             @OA\Property(property="secure_phi_containers", type="boolean"),
     *             @OA\Property(property="id_badge_visible", type="boolean"),
     *             @OA\Property(property="biohazard_bags_available", type="boolean"),
     *             @OA\Property(property="secure_transport_containers", type="boolean"),
     *             @OA\Property(property="gloves_available", type="boolean"),
     *             @OA\Property(property="extra_leakproof_bags", type="boolean"),
     *             @OA\Property(property="latitude", type="number", format="float"),
     *             @OA\Property(property="longitude", type="number", format="float")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Safety checklist submitted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request)
    {
        // print_r($request->all());return;
        // dd($request->all());
        $user = $request->user();
        if ($user->role_id != '4') {
            return response()->json([
                'success' => false,
                'message' => 'This endpoint is only for drivers'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'checklist_type' => 'required|in:pre_duty,post_duty',
            'lights_functional' => 'required|boolean',
            'tire_pressure_checked' => 'required|boolean',
            'windshield_cleaned' => 'required|boolean',
            'vehicle_locked' => 'required|boolean',
            'secure_phi_containers' => 'required|boolean',
            'id_badge_visible' => 'required|boolean',
            'biohazard_bags_available' => 'required|boolean',
            'secure_transport_containers' => 'required|boolean',
            'gloves_available' => 'required|boolean',
            'extra_leakproof_bags' => 'required|boolean',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Check if checklist for today already exists
            // $checklist = SafetyChecklist::where('driver_id', $user->id)
            //     ->whereDate('checklist_date', today())
            //     ->first();

            $data = $request->except(['_token']);
            $data['driver_id'] = $user->id;
            $data['checklist_date'] = today();
            $data['completed_at'] = now();
            $data['vehicle_locked'] = 1;
            // var_dump($data['vehicle_locked']);
            // print_r($data);return;
            /*if ($checklist) {
                $checklist->update($data);
                $message = 'Safety checklist updated successfully';
            } else {
                $checklist = SafetyChecklist::create($data);
                $message = 'Safety checklist submitted successfully';
            }*/

            $checklist = SafetyChecklist::create($data);
            $message = 'Safety checklist submitted successfully';

            // Calculate if all checks passed
            $checklist->all_checks_passed = $checklist->calculateAllChecksPassed();
            $checklist->save();

            // $user->driverProfile()->update(['availability_status' => 'available']);
            $driverProfile = $user->driverProfile;
            $driverProfile->current_latitude = $request->latitude;
            $driverProfile->current_longitude = $request->longitude;
            $driverProfile->availability_status = 'available';
            $driverProfile->save();

            // Log activity
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => $checklist->wasRecentlyCreated ? 'created' : 'updated',
                'model_type' => 'App\Models\SafetyChecklist',
                'model_id' => $checklist->id,
                'description' => "Safety checklist {$checklist->checklist_type} completed",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'id' => $checklist->id,
                    'all_checks_passed' => $checklist->all_checks_passed,
                    'completed_at' => $checklist->completed_at->toIso8601String()
                ]
            ], $checklist->wasRecentlyCreated ? 201 : 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit safety checklist',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/mobile/v1/safety-checklist/history",
     *     summary="Get safety checklist history",
     *     description="Returns paginated history of safety checklists",
     *     tags={"Mobile - Safety Checklist"},
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
     *         description="Safety checklist history",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function history(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'driver') {
            return response()->json([
                'success' => false,
                'message' => 'This endpoint is only for drivers'
            ], 403);
        }

        $perPage = min($request->input('per_page', 15), 50);

        $checklists = SafetyChecklist::where('driver_id', $user->id)
            ->orderBy('checklist_date', 'desc')
            ->paginate($perPage);

        $transformedChecklists = $checklists->map(function($checklist) {
            return [
                'id' => $checklist->id,
                'checklist_date' => $checklist->checklist_date->toDateString(),
                'checklist_type' => $checklist->checklist_type,
                'all_checks_passed' => $checklist->all_checks_passed,
                'completed_at' => $checklist->completed_at?->toIso8601String(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'checklists' => $transformedChecklists,
                'pagination' => [
                    'total' => $checklists->total(),
                    'per_page' => $checklists->perPage(),
                    'current_page' => $checklists->currentPage(),
                    'last_page' => $checklists->lastPage(),
                    'from' => $checklists->firstItem(),
                    'to' => $checklists->lastItem()
                ]
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/mobile/v1/safety-checklist/{id}",
     *     summary="Get specific safety checklist",
     *     description="Returns detailed information for a specific safety checklist",
     *     tags={"Mobile - Safety Checklist"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Checklist ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Safety checklist details",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Checklist not found")
     * )
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();

        $checklist = SafetyChecklist::where('id', $id)
            ->where('driver_id', $user->id)
            ->first();

        if (!$checklist) {
            return response()->json([
                'success' => false,
                'message' => 'Safety checklist not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $checklist->id,
                'checklist_date' => $checklist->checklist_date->toDateString(),
                'checklist_type' => $checklist->checklist_type,
                'vehicle_checks' => [
                    'lights_functional' => $checklist->lights_functional,
                    'tire_pressure_checked' => $checklist->tire_pressure_checked,
                    'windshield_cleaned' => $checklist->windshield_cleaned,
                    'vehicle_locked' => $checklist->vehicle_locked,
                ],
                'etiquette_checks' => [
                    'id_badge_visible' => $checklist->id_badge_visible,
                ],
                'compliance_checks' => [
                    'secure_phi_containers' => $checklist->secure_phi_containers,
                    'secure_transport_containers' => $checklist->secure_transport_containers,
                ],
                'equipment_checks' => [
                    'biohazard_bags_available' => $checklist->biohazard_bags_available,
                    'gloves_available' => $checklist->gloves_available,
                    'extra_leakproof_bags' => $checklist->extra_leakproof_bags,
                ],
                'all_checks_passed' => $checklist->all_checks_passed,
                'completed_at' => $checklist->completed_at?->toIso8601String(),
                'signature_image' => $checklist->signature_image,
                'location' => [
                    'latitude' => $checklist->latitude,
                    'longitude' => $checklist->longitude,
                ],
                'created_at' => $checklist->created_at->toIso8601String(),
            ]
        ]);
    }
}
