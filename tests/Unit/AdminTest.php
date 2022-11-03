<?php

namespace Tests\Unit;

use App\Enums\LoanStatus;
use App\Models\User;
use Tests\TestCase;
use Laravel\Passport\Passport;

class AdminTest extends TestCase
{
    const ACCEPT         = 'application/json';
    const ADMIN_EMAIL    = 'admin@gmail.com';
    const ADMIN_SCOPE    = 'admin';

    /**
     * @return void
     */
    public function test_api_admin_login()
    {
        $response = $this->withHeaders(['accept' => self::ACCEPT])->post(env('APP_URL'). '/api/login', [
            'email'    => self::ADMIN_EMAIL,
            'password' => 'password'
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Login Successfull.']);
        $response->assertJsonStructure(['token' => [
            'token_type', 'expires_in', 'access_token', 'refresh_token'
        ]]);
    }

    /**
     * @return void
     */
    public function test_api_loan_change_status_by_admin()
    {
        $admin    = User::where('email', 'admin@gmail.com')->first();
        $customer = User::factory()->create();
        $loan     = $customer->loans()->create(['amount' => rand(1, 100), 'term' => rand(1, 5)]);

        Passport::actingAs($admin, [self::ADMIN_SCOPE]);

        $response = $this->withHeaders(['accept' => self::ACCEPT])->post(env('APP_URL'). '/api/loan/'. $loan->id . '/change-status', [
            'status' => LoanStatus::fromValue(LoanStatus::APPROVED)->key,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Loan Status Changed Successfully. Thank you!']);
    }
}
