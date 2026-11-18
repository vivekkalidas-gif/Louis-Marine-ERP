<?php
require __DIR__.'/../vendor/autoload.php';

// 1. Create EmployeeLedgerEntry Model
$modelContent = <<<'EOD'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeLedgerEntry extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'user_id',
        'date',
        'type',
        'amount',
        'debit',
        'credit',
        'description',
        'reference',
        'created_by'
    ];

    protected $casts = [
        'date' => 'date',
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
EOD;

file_put_contents(__DIR__ . '/../app/Models/EmployeeLedgerEntry.php', $modelContent);
echo "Created EmployeeLedgerEntry Model.\n";

// 2. Also ensure PayrollDeductions Model exists (just in case)
$deductionModelContent = <<<'EOD'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PayrollDeduction extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'user_id',
        'month',
        'amount',
        'description',
        'created_by'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
EOD;

file_put_contents(__DIR__ . '/../app/Models/PayrollDeduction.php', $deductionModelContent);
echo "Created PayrollDeduction Model.\n";
