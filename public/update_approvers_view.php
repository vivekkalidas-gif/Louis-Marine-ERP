<?php
$viewDir = __DIR__ . '/../resources/views/admin/accounts/petty_cash';
$viewFile = $viewDir . '/index.blade.php';

// Read current content
$content = file_get_contents($viewFile);

// Replace the "Approved By" dropdown section
$oldApprovedBy = <<<'OLD'
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Approved By</label>
                                <select name="approved_by" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white">
                                    <option value="">-- Select Staff --</option>
                                    @foreach($staff as $member)
                                        <option value="{{ $member->name }}">{{ $member->name }}</option>
                                    @endforeach
                                </select>
                            </div>
OLD;

$newApprovedBy = <<<'NEW'
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Approved By (Accountant/Admin)</label>
                                <select name="approved_by" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white">
                                    <option value="">-- Select Approver --</option>
                                    @foreach($approvers as $approver)
                                        <option value="{{ $approver->name }}">{{ $approver->name }}</option>
                                    @endforeach
                                </select>
                            </div>
NEW;

$content = str_replace($oldApprovedBy, $newApprovedBy, $content);

file_put_contents($viewFile, $content);
echo "<p style='color:green'>✓ View updated - Approved By now uses \$approvers list</p>";
echo "<p>Run: <a href='/clear_views_now.php'>clear_views_now.php</a></p>";
