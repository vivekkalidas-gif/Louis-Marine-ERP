<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Permission;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $actions = ['view', 'create', 'edit', 'delete'];
        $module = 'vessels';

        foreach ($actions as $action) {
            Permission::firstOrCreate(
                ['slug' => "{$module}.{$action}"],
                ['name' => Str::title("{$action} {$module}")]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Don't delete on rollback to be safe, or delete if strict.
        // For now, leave likely.
    }
};
