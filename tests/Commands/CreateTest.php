<?php

namespace Spatie\DbSnapshots\Commands\Test;

use Carbon\Carbon;
use Spatie\DbSnapshots\Test\TestCase;
use Illuminate\Support\Facades\Artisan;

class CreateTest extends TestCase
{
    /** @test */
    public function it_can_create_a_snapshot_without_a_specific_name()
    {
        Artisan::call('snapshot:create');

        $fileName = Carbon::now()->format('Y-m-d_H-i-s').'.sql';

        $this->assertFileOnDiskPassesRegex($fileName, '/CREATE TABLE(?: IF NOT EXISTS){0,1} "models"/');
    }

    /** @test */
    public function it_can_create_a_snapshot_with_specific_name()
    {
        Artisan::call('snapshot:create', ['name' => 'test']);

        $this->assertFileOnDiskPassesRegex('test.sql', '/CREATE TABLE(?: IF NOT EXISTS){0,1} "models"/');
    }

    /** @test */
    public function it_can_create_a_compressed_snapshot()
    {
        Artisan::call('snapshot:create', ['--compress' => true]);

        $fileName = Carbon::now()->format('Y-m-d_H-i-s').'.sql.gz';

        $this->disk->assertExists($fileName);

        $this->assertNotEmpty(gzdecode($this->disk->get($fileName)));
    }
    
    /** @test */
    public function it_can_create_a_snpashot_with_exclude_tables_as_array()
    {
        Artisan::call('snapshot:create', ['--exclude' => ['models']]);
        
        $fileName = Carbon::now()->format('Y-m-d_H-i-s').'.sql';
        
        $this->disk->assertExists($fileName);
        
        $contents = $this->disk->get($fileName);
        
        $this->assertNotRegExp('/CREATE TABLE(?: IF NOT EXISTS){0,1} "models"/', $contents);
    }
}
