<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "Total Users in DB: " . \App\Models\User::count() . "\n";

// Emulate a logged-in user (e.g., the first one)
$currentUser = \App\Models\User::first();
if (!$currentUser) {
    echo "No users found in database!\n";
    exit;
}

echo "Emulating User: " . $currentUser->name . " (ID: " . $currentUser->id . ")\n";
\Illuminate\Support\Facades\Auth::login($currentUser);

$users = \App\Models\User::where('id', '!=', \Illuminate\Support\Facades\Auth::id())
    ->withCount(['sentMessages as unread_count' => function($query) {
        $query->where('receiver_id', \Illuminate\Support\Facades\Auth::id())
              ->where('is_read', false);
    }])
    ->get(['id', 'name', 'email']);

echo "Users fetched by chat logic: " . $users->count() . "\n";

foreach ($users as $user) {
    echo "- " . $user->name . " (ID: " . $user->id . ") Unread: " . $user->unread_count . "\n";
}
