<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$userId = '019ba332-f6d0-7334-9727-fbc876f4956d'; // From User URL
$user = \App\Models\User::find($userId);

echo "<pre>";
if (!$user) {
    echo "User not found with ID: $userId\n";
    // List some users to verify ID
    $u = \App\Models\User::where('name', 'like', '%Tripati%')->get();
    echo "Found Users matching 'Tripati':\n";
    print_r($u->toArray());
} else {
    echo "User Found: " . $user->name . " (ID: " . $user->id . ")\n";
    
    // Check Relation
    $detail = $user->employeeDetail;
    echo "EmployeeDetail Relation: ";
    if ($detail) {
        echo "FOUND\n";
        print_r($detail->toArray());
    } else {
        echo "NULL\n";
        
        // Manual Query
        $manual = \App\Models\EmployeeDetail::where('user_id', $user->id)->first();
        echo "Manual Query for EmployeeDetail (user_id = $userId): ";
        if ($manual) {
            echo "FOUND (But relation failed?)\n";
            print_r($manual->toArray());
        } else {
            echo "NOT FOUND IN DB\n";
            
            // Check if there are ANY details
            $any = \App\Models\EmployeeDetail::first();
            if ($any) {
                echo "Sample existing Detail: UserID=" . $any->user_id . "\n";
            }
        }
    }
}
echo "</pre>";
