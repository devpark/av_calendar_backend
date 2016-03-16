<?php

namespace Tests\Unit\app\Modules\User\Traits;

use Mockery;
use StdClass;
use App\Modules\User\Traits\Allowed;

class AllowedTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function zero_results_for_non_existing_user()
    {
        // Arrange
        $model = new class{
            use Allowed;
            public static function find($id) { return null; }
        };
        $query = Mockery::mock('StdClass');
        // Assert (set expectations)
        $query->shouldReceive('whereRaw')->once()->with('1 = 0')->andReturn($query);

        // Act and Assert
        $this->assertSame($query, $model->scopeAllowed($query, 'wrong_id'));
    }

    /** @test */
    public function dont_filter_for_admin_user()
    {
        // Arrange
        $model = new class{ use Allowed; };
        $query = new StdClass;
        $admin = Mockery::mock(\App\Models\Model::class);
        // Assert (expect)
        $admin->shouldReceive('isAdmin')->once()->andReturn(true);

        // Act and Assert
        $this->assertSame($query, $model->scopeAllowed($query, $admin));
    }

    /** @test */
    public function apply_filters_for_ordinary_user()
    {
        // Arrange and expect
        $model = new class{ use Allowed; };
        $user = Mockery::mock(\App\Models\Model::class);
        $user->shouldReceive('isAdmin')->once()->andReturn(false);
        $user->shouldReceive('getAttribute')->times(2)->with('id')->andReturn('user_id');

        $query = Mockery::mock('StdClass');

        // these 2 are tricky - due to the fact that php never ever
        // evaluates Closure == Closure to true, we need to work
        // it around, with Mockery::on calls for expectations.
        $nestedWhere = Mockery::on(function ($callback) use ($query) {
            call_user_func($callback, $query);
            return true;
        });
        $orWhereHas = Mockery::on(function ($callback) use ($query) {
            call_user_func($callback, $query);
            return true;
        });

        // here are the lines from trait that are tested:

        // return $query->where(function ($q) use ($user) {
        $query->shouldReceive('where') ->with($nestedWhere)->andReturn($query);
            // $q->where('id', $user->id)
        $query->shouldReceive('where')->with('id', 'user_id')->andReturn($query);
                // ->orWhereHas('projects.users', function ($q) use ($user) {
        $query->shouldReceive('orWhereHas')->with('projects.users', $orWhereHas);
                    // $q->where('project_user.user_id', $user->id);
        $query->shouldReceive('where')->with('project_user.user_id', 'user_id');

        $this->assertSame($query, $model->scopeAllowed($query, $user));
    }

    public function tearDown()
    {
        Mockery::close();
    }
}
