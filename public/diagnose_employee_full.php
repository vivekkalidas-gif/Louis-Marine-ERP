<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$userId = request()->get('id') ?? '019ba332-f6d0-7334-9727-fbc876f4956d';

echo "<h3>Diagnostic Report for User: $userId</h3>";

// 1. Get User
$user = \App\Models\User::find($userId);
if (!$user) {
    echo "<p style='color:red'>User not found!</p>";
    exit;
}
echo "<p><strong>User Found:</strong> {$user->name} ({$user->email})</p>";

// 2. Check EmployeeDetail table directly
echo "<h4>Direct Database Query:</h4>";
$empDetail = \App\Models\EmployeeDetail::where('user_id', $userId)->first();
if ($empDetail) {
    echo "<p style='color:green'><strong>✓ EmployeeDetail EXISTS in database</strong></p>";
    echo "<pre>" . print_r($empDetail->toArray(), true) . "</pre>";
} else {
    echo "<p style='color:red'><strong>✗ NO EmployeeDetail record found for user_id: $userId</strong></p>";
}

// 3. Check via relationship
echo "<h4>Via Relationship:</h4>";
$user->load('employeeDetail');
if ($user->employeeDetail) {
    echo "<p style='color:green'><strong>✓ Relationship Loaded</strong></p>";
    echo "<pre>" . print_r($user->employeeDetail->toArray(), true) . "</pre>";
} else {
    echo "<p style='color:red'><strong>✗ Relationship returned NULL</strong></p>";
}

// 4. Show all employee_details records (to verify table has data)
echo "<h4>All EmployeeDetail Records:</h4>";
$allDetails = \App\Models\EmployeeDetail::all();
echo "<p>Total records: " . $allDetails->count() . "</p>";
foreach ($allDetails as $detail) {
    echo "<p>User ID: {$detail->user_id} | Code: {$detail->employee_code} | Designation: {$detail->designation}</p>";
}
