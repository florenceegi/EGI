<?php

/**
 * @Oracode Verification Script: Dependency Injection Checker
 * 🎯 Purpose: Verify that a Controller can be instantiated by the Service Container.
 * 🛡️ Safety: Validates constructor injection without executing methods.
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$targetClass = $argv[1] ?? null;

if (!$targetClass) {
    echo "❌ Usage: php check_di.php [FullClassName]\n";
    exit(1);
}

// Normalize namespace
$targetClass = str_replace('/', '\\', $targetClass);
if (!class_exists($targetClass)) {
    // Try prepending default namespace if missing
    if (class_exists('App\\Http\\Controllers\\' . $targetClass)) {
        $targetClass = 'App\\Http\\Controllers\\' . $targetClass;
    } elseif (class_exists('App\\' . $targetClass)) {
        $targetClass = 'App\\' . $targetClass;
    } else {
        echo "❌ Class not found: $targetClass\n";
        exit(1);
    }
}

echo "🔍 Checking: $targetClass\n";

try {
    // Attempt resolution
    $instance = app()->make($targetClass);
    
    echo "✅ SUCCESS: Resolution successful.\n";
    
    // Check for required properties (OS3)
    $reflection = new ReflectionClass($instance);
    
    $requiredProps = ['logger', 'errorManager'];
    $missingProps = [];
    
    foreach ($requiredProps as $prop) {
        if ($reflection->hasProperty($prop)) {
            $p = $reflection->getProperty($prop);
            $p->setAccessible(true);
            if (!$p->isInitialized($instance) || $p->getValue($instance) === null) {
                $missingProps[] = "$prop (Not Initialized)";
            }
        } else {
            // Only warn if property is strictly expected, but methods might use $this->logger directly
            // Not strictly a failure of DI, but a failure of pattern implementation.
            // echo "⚠️ Warning: Property '$prop' not defined in class.\n";
        }
    }
    
    if (!empty($missingProps)) {
        echo "⚠️ WARNING: Properties found but null: " . implode(', ', $missingProps) . "\n";
    }

    exit(0);

} catch (\Throwable $e) {
    echo "❌ FAILED: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    exit(1);
}
