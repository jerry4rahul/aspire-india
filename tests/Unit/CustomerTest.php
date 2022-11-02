<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\UserLoan;
use Tests\TestCase;
use Laravel\Passport\Passport;

class CustomerTest extends TestCase
{
    const CUSTOMER_EMAIL = 'rahul@gmail.com';
    const ACCEPT         = 'application/json';

    /**
     * @return void
     */
    public function test_api_register()
    {
        $response = $this->withHeaders([
            'accept'   => self::ACCEPT])->post(env('APP_URL'). '/api/register', [
            'name'     => 'Rahul Kumar Sharma',
            'email'    => self::CUSTOMER_EMAIL,
            'password' => 'password'
        ]);

        $response->assertStatus(200);
    }

    /**
     * @return void
     */
    public function test_api_login()
    {
        $response = $this->withHeaders([
            'accept'   => self::ACCEPT])->post(env('APP_URL'). '/api/login', [
            'email'    => self::CUSTOMER_EMAIL,
            'password' => 'password'
        ]);

        $response->assertStatus(200);
    }

    /**
     * @return void
     */
    public function test_api_user_submit_loan_request()
    {
        Passport::actingAs(User::where('email', self::CUSTOMER_EMAIL)->first(), ['customer']);

        $response = $this->withHeaders([
            'accept' => self::ACCEPT])->post(env('APP_URL'). '/api/submit-loan-request', [
            'amount' => 100,
            'term'   => 5
        ]);

        $response->assertStatus(200);
    }

    /**
     * @return void
     */
    public function test_api_user_get_all_associated_loans()
    {
        Passport::actingAs(User::where('email', self::CUSTOMER_EMAIL)->first(), ['customer']);

        $response = $this->withHeaders([
            'accept' => self::ACCEPT])->get(env('APP_URL'). '/api/all-loans', [
            'amount' => 100,
            'term'   => 5
        ]);

        $response->assertStatus(200);
    }

    /**
     * @return void
     */
    public function test_api_loan_change_status_by_admin()
    {
        $user = User::where('email', self::CUSTOMER_EMAIL)->first();
        Passport::actingAs(User::where('email', 'admin@gmail.com')->first(), ['admin']);

        $response = $this->withHeaders([
            'accept'   => self::ACCEPT])->post(env('APP_URL'). '/api/loan/'. $user->loans->first()->id . '/change-status', [
            'status'   => 'APPROVED',
        ]);

        $response->assertStatus(200);
    }

    /**
     * @return void
     */
    public function test_api_user_add_repayments_for_the_loan()
    {
        $user = User::where('email', self::CUSTOMER_EMAIL)->first();
        Passport::actingAs($user, ['customer']);

        echo $user->loans->first()->id;

        $response = $this->withHeaders([
            'accept'             => self::ACCEPT])->post(env('APP_URL'). '/api/loan/'. $user->loans->first()->id . '/add-repayment', [
            'loan_repayment_id'  => $user->loans->first()->loanRepayments->first()->id,
            'amount'             => $user->loans->first()->loanRepayments->first()->amount
        ]);

        $response->assertStatus(200);
    }
}
