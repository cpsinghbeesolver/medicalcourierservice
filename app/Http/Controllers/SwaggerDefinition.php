<?php

namespace App\Http\Controllers;

use OpenApi\Annotations as OA;

/**
 * @OA\OpenApi(
 *     @OA\Info(
 *         version="1.0.0",
 *         title="Medical Courier Services API",
 *         description="Complete API documentation for Medical Courier Services - Web Dashboard & Mobile Applications with dynamic pagination, role-based access control, and comprehensive delivery management",
 *         @OA\Contact(
 *             email="support@medicalcourier.com",
 *             name="Medical Courier Support"
 *         ),
 *         @OA\License(
 *             name="Proprietary",
 *             url="https://medicalcourier.com/license"
 *         )
 *     ),
 *     @OA\Server(
 *         url="http://172.16.123.74:8000",
 *         description="API Server"
 *     ),
 *     @OA\Components(
 *         @OA\SecurityScheme(
 *             securityScheme="sanctum",
 *             type="http",
 *             scheme="bearer",
 *             bearerFormat="JWT",
 *             description="Enter your bearer token (without 'Bearer' prefix)"
 *         )
 *     ),
 *     @OA\Tag(
 *         name="Authentication",
 *         description="Web API authentication endpoints for admin and dispatcher users"
 *     ),
 *     @OA\Tag(
 *         name="Password Reset",
 *         description="Password reset endpoints with email link and OTP methods for both web and mobile"
 *     ),
 *     @OA\Tag(
 *         name="Mobile - Authentication",
 *         description="Mobile API authentication endpoints for driver mobile app"
 *     ),
 *     @OA\Tag(
 *         name="Deliveries",
 *         description="Web API delivery management endpoints with full CRUD operations"
 *     ),
 *     @OA\Tag(
 *         name="Mobile - Deliveries",
 *         description="Mobile API delivery endpoints with pagination and filtering for drivers"
 *     ),
 *     @OA\Tag(
 *         name="Driver Operations",
 *         description="Web API driver operation endpoints"
 *     ),
 *     @OA\Tag(
 *         name="Mobile - Driver",
 *         description="Mobile API driver endpoints for GPS tracking and statistics"
 *     ),
 *     @OA\Tag(
 *         name="Driver Profiles",
 *         description="Driver profile management endpoints"
 *     ),
 *     @OA\Tag(
 *         name="Activity Logs",
 *         description="System activity logging and audit trail endpoints"
 *     ),
 *     @OA\Tag(
 *         name="Mobile - Safety Checklist",
 *         description="Mobile API pre-duty safety checklist endpoints for drivers"
 *     ),
 *     @OA\Tag(
 *         name="Mobile - Job Requests",
 *         description="Mobile API job request management endpoints (accept/decline deliveries)"
 *     )
 * )
 */
class SwaggerDefinition
{
    // This class exists solely to hold the OpenAPI annotations
}
