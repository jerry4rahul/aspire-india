<?php

namespace Tests\Unit;

use App\Enums\LoanStatus;
use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;
use Laravel\Passport\Passport;
use Faker\Factory;

class CustomerTest extends TestCase
{
    const ACCEPT         = 'application/json';
    const CUSTOMER_SCOPE = 'customer';
    const ADMIN_EMAIL    = 'admin@gmail.com';
    const ADMIN_SCOPE    = 'admin';

    /**
     * @return void
     */
    public function test_api_customer_register()
    {
        $faker    = Factory::create();
        $response = $this->withHeaders(['accept' => self::ACCEPT])->post(env('APP_URL'). '/api/register', [
            'name'     => $faker->name(),
            'email'    =>  $faker->unique()->safeEmail,
            'password' => 'password'
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Registration Successfull.']);
    }

    /**
     * @return void
     */
    public function test_api_customer_login()
    {
        $customer = User::factory()->create();
        $response = $this->withHeaders(['accept' => self::ACCEPT])->post(env('APP_URL'). '/api/login', [
            'email'    => $customer->email,
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
    public function test_api_customer_submit_loan_request()
    {
        $customer = User::factory()->create();
        Passport::actingAs($customer, [self::CUSTOMER_SCOPE]);

        $response = $this->withHeaders(['accept' => self::ACCEPT])->post(env('APP_URL'). '/api/submit-loan-request', [
            'amount' => rand(1, 100),
            'term'   => rand(1, 5)
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'New Loan Request Submitted Successfully.']);
        $response->assertJsonStructure(['loan_details' => [
            'id', 'amount', 'term', 'status', 'repayments' => ['*' => [
                'id', 'amount', 'repayment_date', 'status'
        ]]]]);
    }

    /**
     * @return void
     */
    public function test_api_customer_get_all_associated_loans()
    {
        $customer = User::factory()->create();
        Passport::actingAs($customer, [self::CUSTOMER_SCOPE]);

        $response = $this->withHeaders(['accept' => self::ACCEPT])->get(env('APP_URL'). '/api/all-loans');

        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['*' => [
            'id', 'amount', 'term', 'status', 'repayments' => ['*' => [
                'id', 'amount', 'repayment_date', 'status'
            ]]
        ]]]);
    }

    /**
     * @return void
     */
    public function test_api_customer_add_repayments_for_the_loan()
    {
        $customer   = User::factory()->create();
        $loan       = $customer->loans()->create(['amount' => 100, 'term' => 2, 'status' => LoanStatus::APPROVED]);
        $repayments = $loan->loanRepayments()->createMany([
            ['amount' => 50.00, 'repayment_date' => Carbon::now()->addWeek(1)],
            ['amount' => 50.00, 'repayment_date' => Carbon::now()->addWeek(2)],
        ]);

        Passport::actingAs($customer, [self::CUSTOMER_SCOPE]);

        $response  = $this->withHeaders(['accept' => self::ACCEPT])->post(env('APP_URL'). '/api/loan/'. $loan->id . '/add-repayment', [
            'loan_repayment_id'  => $repayments[0]->id,
            'amount'             => $repayments[0]->amount
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Your Repayment is successfully completed. Thank you!']);
    }
}
