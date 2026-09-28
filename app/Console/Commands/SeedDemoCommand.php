<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SeedDemoCommand extends Command
{
    protected $signature = 'aimeos:demo-seed';

    protected $description = 'Initializes the Aimeos demo data once for the demo database';

    public function handle(): int
    {
        if (Schema::hasTable('aimeos_demo_seed') && DB::table('aimeos_demo_seed')->where('key', 'demo-v1')->exists()) {
            return self::SUCCESS;
        }

        if (!Schema::hasTable('aimeos_demo_seed')) {
            Schema::create('aimeos_demo_seed', function (Blueprint $table) {
                $table->string('key')->primary();
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if ($this->call('migrate', ['--force' => true]) !== self::SUCCESS) {
            return self::FAILURE;
        }

        if ($this->call('aimeos:setup', ['--option' => ['setup/default/demo:1']]) !== self::SUCCESS) {
            return self::FAILURE;
        }

        if ($this->call('aimeos:account', [
            'email' => env('AIMEOS_ADMIN_EMAIL', 'admin@example.com'),
            '--password' => env('AIMEOS_ADMIN_PASSWORD', 'AimeosDemo2026'),
            '--super' => true,
            '--admin' => true,
        ]) !== self::SUCCESS) {
            return self::FAILURE;
        }

        DB::table('aimeos_demo_seed')->insert(['key' => 'demo-v1']);

        return self::SUCCESS;
    }
}
