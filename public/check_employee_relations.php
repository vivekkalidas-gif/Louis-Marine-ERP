<?php
$userId = request()->get('id') ?? '019ba332-f6d0-7334-9727-fbc876f4956d';
$user = \App\Models\User::find($userId);

if (!$user) {
    echo "User not found: $userId";
    exit;
}

echo "<h3>User: {$user->name}</h3>";
echo "<h4>Checking Available Relationships:</h4>";

$relationships = [
    'employeeDetail',
    'employeeSalary', 
    'salaryDetail',
    'payrollDetail',
    'employee',
    'salary'
];

foreach ($relationships as $rel) {
    try {
        $data = $user->$rel;
        echo "<p style='color:green'><strong>✓ {$rel}:</strong> " . ($data ? 'EXISTS' : 'NULL') . "</p>";
        if ($data) {
            echo "<pre>" . print_r($data->toArray(), true) . "</pre>";
        }
    } catch (\Exception $e) {
        echo "<p style='color:red'><strong>✗ {$rel}:</strong> Not found</p>";
    }
}

echo "<hr><h4>All Loaded Relations:</h4>";
echo "<pre>" . print_r(array_keys($user->getRelations()), true) . "</pre>";
