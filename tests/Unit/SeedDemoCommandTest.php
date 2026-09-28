<?php

namespace Tests\Unit;

use App\Console\Commands\SeedDemoCommand;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Mockery;
use Tests\TestCase;

class SeedDemoCommandTest extends TestCase
{
    public function test_does_not_reseed_a_database_that_was_already_initialized()
    {
        $query = Mockery::mock();
        Schema::shouldReceive('hasTable')->once()->with('aimeos_demo_seed')->andReturn(true);
        DB::shouldReceive('table')->once()->with('aimeos_demo_seed')->andReturn($query);
        $query->shouldReceive('where')->once()->with('key', 'demo-v1')->andReturnSelf();
        $query->shouldReceive('exists')->once()->andReturn(true);

        $this->assertSame(SeedDemoCommand::SUCCESS, (new SeedDemoCommand())->handle());
    }
}
