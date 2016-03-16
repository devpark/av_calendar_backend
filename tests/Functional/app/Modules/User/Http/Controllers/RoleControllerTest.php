<?php

namespace Tests\Functional\app\Http\Controllers;

use App\Models\Role;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class RoleControllerTest extends \TestCase
{
    use DatabaseTransactions;
    use WithoutMiddleware;

    public function testIndex()
    {
        $this->createUser();
        $this->get('/roles')
            ->seeStatusCode(200)
            ->seeJsonContains([
                'data' => Role::orderBy('id')->get(),
            ])->isJson();
    }
}
