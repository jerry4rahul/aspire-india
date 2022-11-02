<?php

namespace Tests\Unit;

use App\Models\User;
use Tests\TestCase;
use Laravel\Passport\Passport;


class AdminTest extends TestCase
{
    const ACCEPT         = 'application/json';

    /**
     * @return void
     */
    public function test_api_login()
    {
        $response = $this->withHeaders([
            'accept'   => self::ACCEPT])->post(env('APP_URL'). '/api/login', [
            'email'    => 'admin@gmail.com',
            'password' => 'password'
        ]);

        $response->assertStatus(200);
    }
}
