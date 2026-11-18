<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$file = __DIR__ . '/../resources/views/admin/staff/show.blade.php';

// SIMPLIFIED HARDCODED VIEW
$content = <<<'EOD'
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Staff Profile
        </h2>
    </x-slot>

    @php
        $routeParam = request()->route('staff');
        $userObj = is_object($routeParam) ? $routeParam : \App\Models\User::find($routeParam);
        if (!$userObj && request()->route('user')) {
            $userObj = \App\Models\User::find(request()->route('user'));
        }
    @endphp

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(!$userObj)
            <div class="bg-red-100 p-4">User not found</div>
            @else
            
            <div class="bg-white dark:bg-gray-800 p-6 shadow sm:rounded-lg">
                <h1 class="text-2xl font-bold mb-4">{{ $userObj->name }}</h1>
                
                <button onclick="document.getElementById('modal1').classList.remove('hidden')" class="bg-blue-600 text-white px-4 py-2 rounded">
                    Add Transaction
                </button>
                
                <div class="mt-8">
                    <h3 class="text-xl font-bold">History</h3>
                    @php
                        $entries = \App\Models\EmployeeLedgerEntry::where('user_id', $userObj->id)->orderBy('date','desc')->get();
                    @endphp
                    <table class="min-w-full mt-4">
                        <thead><tr><th>Date</th><th>Amount</th><th>Action</th></tr></thead>
                        <tbody>
                        @foreach($entries as $entry)
                            <tr>
                                <td>{{ $entry->date }}</td>
                                <td>{{ $entry->amount }}</td>
                                <td>
                                    <a href="/admin/staff/ledger/{{$entry->id}}/print" target="_blank" class="text-blue-500">Print</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="modal1" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-500 bg-opacity-75">
                <div class="flex items-center justify-center min-h-screen">
                    <div class="bg-white p-6 rounded max-w-md w-full">
                        <form action="/admin/staff/{{ $userObj->id }}/ledger" method="POST">
                            @csrf
                            <h3 class="text-lg font-bold mb-4">New Transaction</h3>
                            <select name="type" class="w-full border p-2 mb-2"><option value="advance">Advance</option><option value="repayment">Repayment</option></select>
                            <input type="number" name="amount" step="0.01" class="w-full border p-2 mb-2" placeholder="Amount" required>
                            <input type="date" name="date" value="{{ date('Y-m-d') }}" class="w-full border p-2 mb-2">
                            <input type="text" name="description" class="w-full border p-2 mb-4" placeholder="Description">
                            <div class="flex justify-end space-x-2">
                                <button type="button" onclick="document.getElementById('modal1').classList.add('hidden')" class="border px-4 py-2">Cancel</button>
                                <button type="submit" class="bg-blue-600 text-white px-4 py-2">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
EOD;

@unlink($file);
file_put_contents($file, $content);
echo "FIXED at " . date('H:i:s');
