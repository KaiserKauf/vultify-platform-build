<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// Coolify's own ProductionSeeder writes these strings into the database on
// first boot (not template code, so the usual rebrand.php text patches never
// touch them). A migration is the only reliable, deploy-safe way to fix them
// since it runs automatically via the existing db-migration s6 service.
return new class extends Migration
{
    public function up(): void
    {
        DB::table('servers')
            ->where('description', "This is the server where Coolify is running on. Don't delete this!")
            ->update(['description' => "This is the server where Vultify is running on. Don't delete this!"]);

        DB::table('standalone_dockers')
            ->where('name', 'localhost-coolify')
            ->update(['name' => 'localhost-vultify']);
    }

    public function down(): void
    {
        DB::table('servers')
            ->where('description', "This is the server where Vultify is running on. Don't delete this!")
            ->update(['description' => "This is the server where Coolify is running on. Don't delete this!"]);

        DB::table('standalone_dockers')
            ->where('name', 'localhost-vultify')
            ->update(['name' => 'localhost-coolify']);
    }
};
