<?php

namespace Tests\Unit\app\Modules\CalendarAvailability\Services;

use Mockery;
use Carbon\Carbon;
use App\Modules\CalendarAvailability\Services\CalendarAvailability;

class CalendarAvailabilityTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function it_calls_eloquent_scopes_to_find_available_users()
    {
        $model = Mockery::mock(\App\Models\User::class);
        $query = Mockery::mock(\Illuminate\Database\Eloquent\Builder::class);
        $from = Carbon::parse('2016-02-22');
        $till = Carbon::parse('2016-02-29');

        $service = new CalendarAvailability($model);

        $model->shouldReceive('newQuery')->andReturn($query);
        $query->shouldReceive('active')->andReturn($query);
        $query->shouldReceive('allowed')->andReturn($query);
        $query->shouldReceive('orderBy')->with('id', 'asc')->andReturn($query);
        $query->shouldReceive('withAvailabilities')->with($from, $till)->andReturn($query);
        $query->shouldReceive('get');

        $service->find($from, $till);
    }

    public function tearDown()
    {
        Mockery::close();
        parent::tearDown();
    }
}
