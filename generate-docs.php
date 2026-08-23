<?php
set_error_handler(function($errno, $errstr) {
    if (strpos($errstr, 'Required @OA\PathItem() not found') !== false) {
        return true;
    }
    return false;
});

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    Illuminate\Support\Facades\Artisan::call('l5-swagger:generate');
    echo "✅ Swagger documentation generated successfully!\n";
    echo "📄 Location: storage/api-docs/api-docs.json\n";
    echo "🌐 View at: http://localhost:8000/api/documentation\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
