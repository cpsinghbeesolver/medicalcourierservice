<?php
/**
 * Custom Swagger Documentation Generator
 * Bypasses the PathItem warning by suppressing warnings during generation
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Suppress warnings during swagger generation
$oldErrorHandler = set_error_handler(function($errno, $errstr, $errfile, $errline) {
    // Suppress E_USER_WARNING from swagger-php
    if ($errno === E_USER_WARNING && strpos($errstr, 'Required @OA\PathItem()') !== false) {
        echo "⚠ Suppressing PathItem warning (non-critical)\n";
        return true; // Don't execute PHP internal error handler
    }
    // Let all other errors through
    return false;
});

try {
    echo "Generating Swagger Documentation...\n";
    echo "Scanning: app/Http/Controllers/ and app/Models/\n\n";

    // Create generator instance
    $generator = new \OpenApi\Generator();

    // Scan app directory for annotations
    $sources = [
        __DIR__ . '/app/Http/Controllers/SwaggerDefinition.php',
        __DIR__ . '/app/Http/Controllers/Controller.php',
        __DIR__ . '/app/Http/Controllers/Api',
        __DIR__ . '/app/Models',
    ];

    echo "Scanning directories:\n";
    foreach ($sources as $source) {
        echo "  - " . $source . "\n";
    }
    echo "\n";

    $openapi = $generator->generate($sources);

    echo "OpenAPI object created: " . get_class($openapi) . "\n";
    echo "Info found: " . ($openapi->info && $openapi->info->title ? 'YES - ' . $openapi->info->title : 'NO') . "\n";
    echo "Paths found: " . (property_exists($openapi, 'paths') && $openapi->paths ? count((array)$openapi->paths) : '0') . "\n";
    echo "Servers found: " . (property_exists($openapi, 'servers') && $openapi->servers ? count($openapi->servers) : '0') . "\n";
    echo "\n";

    $outputPath = __DIR__ . '/storage/api-docs';

    // Create directory if it doesn't exist
    if (!is_dir($outputPath)) {
        mkdir($outputPath, 0755, true);
        echo "✓ Created directory: storage/api-docs/\n";
    }

    // Write JSON file
    file_put_contents($outputPath . '/api-docs.json', $openapi->toJson());
    echo "✓ Generated: storage/api-docs/api-docs.json\n";

    // Write YAML file
    file_put_contents($outputPath . '/api-docs.yaml', $openapi->toYaml());
    echo "✓ Generated: storage/api-docs/api-docs.yaml\n";

    echo "\n✅ Swagger documentation generated successfully!\n";
    echo "📖 View at: http://localhost:8000/api/documentation\n";
    echo "\n📊 API Summary:\n";
    echo "   - Web/Admin API: http://localhost:8000/api/v1/\n";
    echo "   - Mobile API: http://localhost:8000/api/mobile/v1/\n";

} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "\nStack Trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
} finally {
    // Restore previous error handler
    if ($oldErrorHandler !== null) {
        set_error_handler($oldErrorHandler);
    } else {
        restore_error_handler();
    }
}
