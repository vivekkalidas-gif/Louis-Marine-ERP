<?php

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Google Drive Backup: File Upload Test</h1>";
echo "<pre>";

// Bootstrap Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

// Load settings manually if needed
if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
    $settings = \App\Models\Setting::getAllCached();
    config(['settings' => $settings->toArray()]);
}

$targetFolderName = \Illuminate\Support\Str::slug(env('APP_NAME', 'laravel-backup')); 
echo "Target Folder Name: [<strong>" . $targetFolderName . "</strong>]\n";

try {
    echo "Attempting to access Google Drive...\n";
    $disk = \Illuminate\Support\Facades\Storage::disk('google');
    
    // 1. Check Directory
    if (!$disk->exists($targetFolderName)) {
        echo "Creating directory '$targetFolderName'...\n";
        $disk->makeDirectory($targetFolderName);
    } else {
        echo "Directory '$targetFolderName' exists.\n";
    }

    // 2. Upload Test File
    $testFileName = $targetFolderName . '/test_connection_' . time() . '.txt';
    echo "Attempting to upload test file: $testFileName ...\n";
    
    $content = "This is a test file to verify Google Drive write access. Created at: " . date('Y-m-d H:i:s');
    $success = $disk->put($testFileName, $content);
    
    if ($success) {
        echo "✅ <span style='color:green'>UPLOAD SUCCESSFUL!</span>\n";
        
        // 3. Get Metadata to find the link
        try {
            // Get the adapter to access specific Google Drive methods if possible, 
            // or just list contents to find our file
            $files = $disk->listContents($targetFolderName, false);
            
            $found = false;
            foreach ($files as $file) {
                if ($file->path() === $testFileName) {
                    $found = true;
                    $extraMetadata = $file->extraMetadata();
                    echo "File Path: " . $file->path() . "\n";
                    // Google Drive adapter often provides 'alternateLink' or 'webViewLink' in extra metadata
                    // But standard Flysystem might obscure it. 
                    // Let's try to get the URL via the disk method if supported.
                    try {
                        $url = $disk->url($testFileName);
                        echo "<a href='$url' target='_blank'>CLICK HERE TO VIEW UPLOADED FILE</a>\n";
                    } catch (\Exception $e) {
                        echo "Could not generate direct URL: " . $e->getMessage() . "\n";
                    }
                    break;
                }
            }
            
            if (!$found) {
                echo "File uploaded but not found in listing immediately (might be propagation delay).\n";
            }
            
        } catch (\Exception $e) {
            echo "Could not listed contents for metadata: " . $e->getMessage() . "\n";
        }
        
    } else {
        echo "❌ <span style='color:red'>UPLOAD FAILED.</span>\n";
    }

} catch (\Exception $e) {
    echo "\n<strong style='color:red'>ERROR:</strong> " . $e->getMessage() . "\n";
    echo "Trace:\n" . $e->getTraceAsString();
}

echo "</pre>";
