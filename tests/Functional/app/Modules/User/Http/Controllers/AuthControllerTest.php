<?php

namespace Tests\Functional\app\Http\Controllers;

use App\Helpers\ErrorCode;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Mockery;
use JWTAuth;

class AuthControllerTest extends \TestCase
{
    use DatabaseTransactions;

    public function testLogin_withoutData()
    {
        $this->createUser();
        $this->post('/auth');

        $this->verifyValidationResponse(['email', 'password']);
    }

    public function testLogin_withMissingPassword()
    {
        $this->createUser();
        $this->post('/auth', [
            'email' => $this->userEmail,
        ]);

        $this->verifyValidationResponse(['password'], ['email']);
    }

    public function testLogin_withInvalidPassword()
    {
        $this->createUser();
        $data = [
            'email' => $this->userEmail,
            'password' => $this->userPassword . 'test',
        ];

        $this->post('/auth', $data);
        $this->verifyErrorResponse(401, ErrorCode::AUTH_INVALID_LOGIN_DATA);
    }

    public function testLogin_withValidPassword()
    {
        $this->createUser();
        $data = [
            'email' => $this->userEmail,
            'password' => $this->userPassword,
        ];

        $this->post('/auth', $data)
            ->seeStatusCode(201)
            ->seeJsonStructure(['data' => ['token']])
            ->isJson();

        // get token and verify if it's valid
        $json = $this->decodeResponseJson();
        $token = $json['data']['token'];
        $this->assertEquals($this->user->id, JWTAuth::authenticate($token)->id);

        $this->assertTrue(auth()->check());
    }

    public function testLogin_withValidPasswordWhenUserDeleted()
    {
        $this->createUser(1);
        $data = [
            'email' => $this->userEmail,
            'password' => $this->userPassword,
        ];

        $this->post('/auth', $data);
        $this->verifyErrorResponse(401, ErrorCode::AUTH_INVALID_LOGIN_DATA);

        $this->assertFalse(auth()->check());
    }

    public function testLogout_whenNotLoggedIn()
    {
        $this->createUser();
        $this->delete('/auth');

        $this->verifyErrorResponse(401, ErrorCode::AUTH_INVALID_TOKEN);
    }

    public function testLogout_whenLoggedIn()
    {
        $this->createUser();
        $token = JWTAuth::fromUser($this->user);

        $this->delete('/auth', [], ['Authorization' => 'Bearer ' . $token])
            ->seeStatusCode(204)
            ->isJson();
    }
}
