<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\HipaaAuditLogger;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to log all PHI access attempts
 *
 * Ensures HIPAA compliance by tracking who accessed what data and when
 */
class LogPhiAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Routes that access PHI data
        $phiRoutes = [
            'deliveries',
            'delivery',
            'users',
            'driver-profiles',
            'delivery-verifications',
        ];

        $routeName = $request->route()?->getName() ?? '';
        $path = $request->path();

        // Check if this request accesses PHI
        $accessesPhi = false;
        foreach ($phiRoutes as $phiRoute) {
            if (str_contains($path, $phiRoute) || str_contains($routeName, $phiRoute)) {
                $accessesPhi = true;
                break;
            }
        }

        $response = $next($request);

        // Log after response to ensure we have the complete context
        if ($accessesPhi && $request->user()) {
            $this->logAccess($request, $response);
        }

        return $response;
    }

    /**
     * Log the PHI access
     *
     * @param Request $request
     * @param Response $response
     * @return void
     */
    private function logAccess(Request $request, Response $response): void
    {
        $method = $request->method();
        $path = $request->path();
        $statusCode = $response->getStatusCode();

        // Only log successful requests
        if ($statusCode >= 200 && $statusCode < 300) {
            $action = $this->mapMethodToAction($method);
            $entity = $this->extractEntityFromPath($path);
            $entityId = $this->extractEntityId($request);

            HipaaAuditLogger::logPhiAccess(
                $action,
                $entity,
                $entityId,
                [],
                [
                    'endpoint' => $path,
                    'method' => $method,
                    'status_code' => $statusCode,
                ]
            );
        }
    }

    /**
     * Extract entity ID from route parameters
     *
     * @param Request $request
     * @return int|null
     */
    private function extractEntityId(Request $request): ?int
    {
        // Try common route parameter names
        $paramNames = ['id', 'delivery', 'driver_profile', 'user', 'verification'];

        foreach ($paramNames as $paramName) {
            $value = $request->route($paramName);
            if ($value !== null) {
                // If it's an object (model binding), get the ID
                if (is_object($value) && method_exists($value, 'getKey')) {
                    return (int) $value->getKey();
                }
                // If it's a numeric value, return it
                if (is_numeric($value)) {
                    return (int) $value;
                }
            }
        }

        return null;
    }

    /**
     * Map HTTP method to action
     *
     * @param string $method
     * @return string
     */
    private function mapMethodToAction(string $method): string
    {
        return match ($method) {
            'GET' => 'viewed',
            'POST' => 'created',
            'PUT', 'PATCH' => 'updated',
            'DELETE' => 'deleted',
            default => 'accessed',
        };
    }

    /**
     * Extract entity name from path
     *
     * @param string $path
     * @return string
     */
    private function extractEntityFromPath(string $path): string
    {
        // Extract the main entity from the path
        $parts = explode('/', $path);

        // Skip 'api', 'v1', 'mobile' segments to get the actual entity
        $entity = 'unknown';
        foreach ($parts as $part) {
            if (!empty($part) && !in_array($part, ['api', 'v1', 'mobile'])) {
                $entity = $part;
                break;
            }
        }

        return str_replace('-', '_', $entity);
    }
}
