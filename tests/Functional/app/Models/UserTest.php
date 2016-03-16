<?php

namespace Tests\Functional\app\Models;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Project;
use App\Models\RoleType;
use App\Models\UserAvailability;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Modules\CalendarAvailability\Services\CalendarAvailability;

class UserTest extends \TestCase
{
    use DatabaseTransactions;

    /**
     * @var CalendarAvailability
     */
    protected $service;

    public function setUp()
    {
        parent::setUp();
        $this->service = $this->app->make(CalendarAvailability::class);
    }

    public function testFind_verifyUsers_forAdmin()
    {
        \DB::table('users')->delete();
        $this->createUser()->setRole(RoleType::ADMIN);
        auth()->loginUsingId($this->user->id);

        $usersNotDeleted = factory(User::class, 3)->create(['deleted' => 0]);
        $usersDeleted = factory(User::class, 2)->create(['deleted' => 1]);

        $users = $this->service->find(Carbon::parse('2016-02-10'), Carbon::parse('2016-02-10'));
        // himself + not deleted users
        $this->assertEquals(1 + 3, $users->count());

        // valid users
        $this->assertEquals($this->user->id, $users[0]->id);
        $this->assertEquals($usersNotDeleted[0]->id, $users[1]->id);
        $this->assertEquals($usersNotDeleted[1]->id, $users[2]->id);
        $this->assertEquals($usersNotDeleted[2]->id, $users[3]->id);

        // empty availabilities
        $this->assertEquals([], $users[0]->availabilities->toArray());
        $this->assertEquals([], $users[1]->availabilities->toArray());
        $this->assertEquals([], $users[2]->availabilities->toArray());
        $this->assertEquals([], $users[3]->availabilities->toArray());
    }

    public function testFind_verifyUsers_forDeveloper()
    {
        \DB::table('users')->delete();
        $this->createUser()->setRole(RoleType::DEVELOPER);
        auth()->loginUsingId($this->user->id);

        $usersNotDeleted = factory(User::class, 3)->create(['deleted' => 0]);
        $usersDeleted = factory(User::class, 2)->create(['deleted' => 1]);

        factory(Project::class)->create(['id' => 1]);
        factory(Project::class)->create(['id' => 5]);

        \DB::table('project_user')->insert([
            [
                'project_id' => 1,
                'user_id' => $this->user->id,
            ],
            [
                'project_id' => 1,
                'user_id' => $usersNotDeleted[0]->id,
            ],
            [
                'project_id' => 5,
                'user_id' => $this->user->id,
            ],
            [
                'project_id' => 5,
                'user_id' => $usersDeleted[0]->id,
            ],
        ]);

        $users = $this->service->find(Carbon::parse('2016-02-10'), Carbon::parse('2016-02-10'));

        //himself + user sharing same project
        $this->assertEquals(1 + 1, $users->count());

        $this->assertEquals($this->user->id, $users[0]->id);
        $this->assertEquals($usersNotDeleted[0]->id, $users[1]->id);

        // empty availabilities
        $this->assertEquals([], $users[0]->availabilities->toArray());
        $this->assertEquals([], $users[1]->availabilities->toArray());
    }

    public function testFind_verifyAvailabilitiesForAdminSingleDay()
    {
        \DB::table('users')->delete();
        $this->createUser()->setRole(RoleType::ADMIN);
        auth()->loginUsingId($this->user->id);

        list($day, $usersNotDeleted, $availabilities) =
            $this->createAvailabilities();

        $users = $this->service->find(Carbon::parse($day), Carbon::parse($day));

        $this->assertEquals(1 + 3, $users->count());

        // $this->user
        $this->assertEquals($this->user->id, $users[0]->id);
        $this->assertEquals(1, $users[0]->availabilities->count());
        $this->assertEquals($availabilities[0]['id'], $users[0]->availabilities[0]->id);

        // $usersNotDeleted[0]
        $this->assertEquals($usersNotDeleted[0]['id'], $users[1]->id);
        $this->assertEquals(0, $users[1]->availabilities->count());

        // $usersNotDeleted[1]
        $this->assertEquals($usersNotDeleted[1]['id'], $users[2]->id);
        $this->assertEquals(2, $users[2]->availabilities->count());
        $this->assertEquals($availabilities[4]['id'], $users[2]->availabilities[0]->id);
        $this->assertEquals($availabilities[3]['id'], $users[2]->availabilities[1]->id);

        // $usersNotDeleted[2]
        $this->assertEquals($usersNotDeleted[2]['id'], $users[3]->id);
        $this->assertEquals(1, $users[3]->availabilities->count());
        $this->assertEquals($availabilities[2]['id'], $users[3]->availabilities[0]->id);
    }

    public function testFind_verifyAvailabilitiesForAdminMultipleDays()
    {
        \DB::table('users')->delete();
        $this->createUser()->setRole(RoleType::ADMIN);
        auth()->loginUsingId($this->user->id);

        list($day, $usersNotDeleted, $availabilities) =
            $this->createAvailabilities();

        $users = $this->service->find(Carbon::parse($day), Carbon::parse($day)->addDay(1));

        $this->assertEquals(1 + 3, $users->count());

        // $this->user
        $this->assertEquals($this->user->id, $users[0]->id);
        $this->assertEquals(2, $users[0]->availabilities->count());
        $this->assertEquals($availabilities[0]['id'], $users[0]->availabilities[0]->id);
        $this->assertEquals($availabilities[5]['id'], $users[0]->availabilities[1]->id);

        // $usersNotDeleted[0]
        $this->assertEquals($usersNotDeleted[0]['id'], $users[1]->id);
        $this->assertEquals(0, $users[1]->availabilities->count());

        // $usersNotDeleted[1]
        $this->assertEquals($usersNotDeleted[1]['id'], $users[2]->id);
        $this->assertEquals(4, $users[2]->availabilities->count());
        $this->assertEquals($availabilities[4]['id'], $users[2]->availabilities[0]->id);
        $this->assertEquals($availabilities[3]['id'], $users[2]->availabilities[1]->id);
        $this->assertEquals($availabilities[9]['id'], $users[2]->availabilities[2]->id);
        $this->assertEquals($availabilities[8]['id'], $users[2]->availabilities[3]->id);

        // $usersNotDeleted[2]
        $this->assertEquals($usersNotDeleted[2]['id'], $users[3]->id);
        $this->assertEquals(2, $users[3]->availabilities->count());
        $this->assertEquals($availabilities[2]['id'], $users[3]->availabilities[0]->id);
        $this->assertEquals($availabilities[7]['id'], $users[3]->availabilities[1]->id);
    }

    public function testFind_verifyAvailabilitiesForDeveloperSingleDay()
    {
        \DB::table('users')->delete();
        $this->createUser()->setRole(RoleType::DEVELOPER);
        auth()->loginUsingId($this->user->id);

        list($day, $usersNotDeleted, $availabilities, $usersDeleted) =
            $this->createAvailabilities();

        factory(Project::class)->create(['id' => 1]);
        factory(Project::class)->create(['id' => 5]);

        \DB::table('project_user')->insert([
            [
                'project_id' => 1,
                'user_id' => $this->user->id,
            ],
            [
                'project_id' => 1,
                'user_id' => $usersNotDeleted[1]->id,
            ],
            [
                'project_id' => 5,
                'user_id' => $this->user->id,
            ],
            [
                'project_id' => 5,
                'user_id' => $usersDeleted[0]->id,
            ],
        ]);

        $users = $this->service->find(Carbon::parse($day), Carbon::parse($day));

        $this->assertEquals(1 + 1, $users->count());

        // $this->user
        $this->assertEquals($this->user->id, $users[0]->id);
        $this->assertEquals(1, $users[0]->availabilities->count());
        $this->assertEquals($availabilities[0]['id'], $users[0]->availabilities[0]->id);

        // $usersNotDeleted[1]
        $this->assertEquals($usersNotDeleted[1]['id'], $users[1]->id);
        $this->assertEquals(2, $users[1]->availabilities->count());
        $this->assertEquals($availabilities[4]['id'], $users[1]->availabilities[0]->id);
        $this->assertEquals($availabilities[3]['id'], $users[1]->availabilities[1]->id);
    }

    public function testFind_verifyAvailabilitiesForDeveloperMultipleDays()
    {
        \DB::table('users')->delete();
        $this->createUser()->setRole(RoleType::DEVELOPER);
        auth()->loginUsingId($this->user->id);

        list($day, $usersNotDeleted, $availabilities, $usersDeleted) =
            $this->createAvailabilities();

        factory(Project::class)->create(['id' => 1]);
        factory(Project::class)->create(['id' => 5]);

        \DB::table('project_user')->insert([
            [
                'project_id' => 1,
                'user_id' => $this->user->id,
            ],
            [
                'project_id' => 1,
                'user_id' => $usersNotDeleted[1]->id,
            ],
            [
                'project_id' => 5,
                'user_id' => $this->user->id,
            ],
            [
                'project_id' => 5,
                'user_id' => $usersDeleted[0]->id,
            ],
        ]);

        $users = $this->service->find(Carbon::parse($day), Carbon::parse($day)->addDays(1));

        $this->assertEquals(1 + 1, $users->count());

        // $this->user
        $this->assertEquals($this->user->id, $users[0]->id);
        $this->assertEquals(2, $users[0]->availabilities->count());
        $this->assertEquals($availabilities[0]['id'], $users[0]->availabilities[0]->id);
        $this->assertEquals($availabilities[5]['id'], $users[0]->availabilities[1]->id);

        // $usersNotDeleted[1]
        $this->assertEquals($usersNotDeleted[1]['id'], $users[1]->id);
        $this->assertEquals(4, $users[1]->availabilities->count());
        $this->assertEquals($availabilities[4]['id'], $users[1]->availabilities[0]->id);
        $this->assertEquals($availabilities[3]['id'], $users[1]->availabilities[1]->id);
        $this->assertEquals($availabilities[9]['id'], $users[1]->availabilities[2]->id);
        $this->assertEquals($availabilities[8]['id'], $users[1]->availabilities[3]->id);
    }

    protected function createAvailabilities()
    {
        $usersNotDeleted = factory(User::class, 3)->create(['deleted' => 0]);
        $usersDeleted = factory(User::class, 2)->create(['deleted' => 1]);

        $day = '2016-02-10';
        $tomorrow = '2016-02-11';

        $availabilities = [
            [
                'time_start' => '12:00:00',
                'time_stop' => '13:00:30',
                'available' => 1,
                'description' => 'Sample description own',
                'user_id' => $this->user->id,
                'day' => $day,
            ],
            [
                'time_start' => '15:00:00',
                'time_stop' => '16:00:00',
                'available' => 1,
                'description' => 'Sample description test',
                'user_id' => $usersDeleted[0]->id,
                'day' => $day,
            ],
            [
                'time_start' => '15:00:00',
                'time_stop' => '16:00:00',
                'available' => 1,
                'description' => 'Sample description test',
                'user_id' => $usersNotDeleted[2]->id,
                'day' => $day,
            ],
            [
                'time_start' => '16:00:00',
                'time_stop' => '18:00:00',
                'available' => 1,
                'description' => 'Sample description test',
                'user_id' => $usersNotDeleted[1]->id,
                'day' => $day,
            ],
            [
                'time_start' => '14:00:00',
                'time_stop' => '15:00:00',
                'available' => 0,
                'description' => 'Sample description test',
                'user_id' => $usersNotDeleted[1]->id,
                'day' => $day,
            ],

            [
                'time_start' => '12:00:00',
                'time_stop' => '13:00:30',
                'available' => 1,
                'description' => 'Sample description own',
                'user_id' => $this->user->id,
                'day' => $tomorrow,
            ],
            [
                'time_start' => '15:00:00',
                'time_stop' => '16:00:00',
                'available' => 1,
                'description' => 'Sample description test',
                'user_id' => $usersDeleted[0]->id,
                'day' => $tomorrow,
            ],
            [
                'time_start' => '15:00:00',
                'time_stop' => '16:00:00',
                'available' => 1,
                'description' => 'Sample description test',
                'user_id' => $usersNotDeleted[2]->id,
                'day' => $tomorrow,
            ],
            [
                'time_start' => '16:00:00',
                'time_stop' => '18:00:00',
                'available' => 1,
                'description' => 'Sample description test',
                'user_id' => $usersNotDeleted[1]->id,
                'day' => $tomorrow,
            ],
            [
                'time_start' => '14:00:00',
                'time_stop' => '15:00:00',
                'available' => 0,
                'description' => 'Sample description test',
                'user_id' => $usersNotDeleted[1]->id,
                'day' => $tomorrow,
            ],
        ];

        foreach ($availabilities as $key => $av) {
            $avO = UserAvailability::forceCreate($av);
            $availabilities[$key]['id'] = $avO->id;
        }

        return [$day, $usersNotDeleted, $availabilities, $usersDeleted];
    }
}
