<?php

namespace Tests\Functional\app\Http\Controllers;

use App\Helpers\ErrorCode;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use JWTAuth;
use Tests\Helpers\MailTrap;

class PasswordControllerTest extends \TestCase
{
    use DatabaseTransactions;
    use MailTrap;

    protected $testUrl = 'http://example.com/:token/?email=:email';

    public function testSendResetEmail_withoutData()
    {
        $this->cleanEmails();
        $this->createUser();
        $this->post('/password/reset');

        $this->verifyValidationResponse(['email', 'url']);

        $messages = $this->getEmails();
        $this->assertEquals(0, count($messages));
    }

    public function testSendResetEmail_withInvalidEmail()
    {
        $this->cleanEmails();
        $this->createUser();
        $this->post('/password/reset', [
            'email' => $this->userEmail . 'xxx',
            'url' => $this->testUrl,
        ]);

        $this->verifyErrorResponse(404, ErrorCode::PASSWORD_NO_USER_FOUND);

        $messages = $this->getEmails();
        $this->assertEquals(0, count($messages));
    }

    public function testSendResetEmail_withValidEmail()
    {
        $this->mailtraipInbox = env('MAILTRAP_API_INBOX');
        $this->cleanEmails();
        $this->createUser();

        $this->post('/password/reset', [
            'email' => $this->userEmail,
            'url' => $this->testUrl,
        ])->seeStatusCode(201);

        $token = \DB::table('password_resets')->first()->token;

        $messages = $this->getEmails();
        $this->assertEquals(1, count($messages));
        $message = $messages[0];
        $this->assertEquals(trans('emails.password_reset.subject'),
            $message->subject);
        $this->assertEquals(env('EMAIL_FROM_ADDRESS'), $message->from_email);
        $this->assertEquals(env('EMAIL_FROM_NAME'), $message->from_name);
        $this->assertEquals($this->userEmail, $message->to_email);
        $this->assertContains(str_replace([':email', ':token'],
            [urlencode($this->userEmail), $token], $this->testUrl),
            $message->html_body);
    }

    public function testReset_withNoData()
    {
        $this->createUser();

        $this->put('/password/reset', []);

        $this->verifyValidationResponse(['token', 'email', 'password']);
    }

    public function testReset_withValidData()
    {
        $this->createUser();
        $token = $this->createPasswordToken();

        $newPassword = 'test00';

        $this->put('/password/reset', [
            'email' => $this->userEmail,
            'token' => $token,
            'password' => $newPassword,
            'password_confirmation' => 'test00',
        ])->seeStatusCode(200)->isJson();

        // make sure password was really saved and user can use it
        $this->assertFalse(auth()->check());
        auth()->attempt([
            'email' => $this->userEmail,
            'password' => $newPassword,
        ]);
        $this->assertTrue(auth()->check());
    }

    public function testReset_withExpiredToken()
    {
        $this->createUser();
        $token = $this->createPasswordToken(true);

        $newPassword = 'test00';

        $this->put('/password/reset', [
            'email' => $this->userEmail,
            'token' => $token,
            'password' => $newPassword,
            'password_confirmation' => 'test00',
        ]);

        $this->verifyErrorResponse(422, ErrorCode::PASSWORD_INVALID_TOKEN);
    }

    public function testReset_withInvalidEmail()
    {
        $this->createUser();
        $token = $this->createPasswordToken();

        $newPassword = 'test00';

        $this->put('/password/reset', [
            'email' => $this->userEmail . 'a',
            'token' => $token,
            'password' => $newPassword,
            'password_confirmation' => 'test00',
        ]);

        $this->verifyErrorResponse(404, ErrorCode::PASSWORD_NO_USER_FOUND);
    }

    public function testReset_withInvalidPassword()
    {
        $this->createUser();
        $token = $this->createPasswordToken();

        $newPassword = 'test00';

        $this->put('/password/reset', [
            'email' => $this->userEmail . 'a',
            'token' => $token,
            'password' => $newPassword,
            'password_confirmation' => 'test00',
        ]);

        $this->verifyErrorResponse(404, ErrorCode::PASSWORD_NO_USER_FOUND);
    }

    protected function createPasswordToken($expired = false)
    {
        $token = str_random();
        $date = Carbon::now();
        if ($expired) {
            $date->subMinutes(config('auth.passwords.users.expire') + 1);
        }

        \DB::table('password_resets')->insert([
            'email' => $this->userEmail,
            'token' => $token,
            'created_at' => $date->format('Y-m-d H:i:s'),
        ]);

        return $token;
    }
}
