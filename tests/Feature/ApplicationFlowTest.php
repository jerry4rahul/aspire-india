<?php

namespace Tests\Feature;

use App\Enums\LoanStatus;
use App\Models\User;
use Tests\TestCase;
use Laravel\Passport\Passport;

class ApplicationFlowTest extends TestCase
{
    const ACCEPT         = 'application/json';
    const ADMIN_EMAIL    = 'admin@gmail.com';
    const LOAN_AMOUNT    = 100.00;
    const LOAN_TERM      = 5;
    const ADMIN_SCOPE    = 'admin';
    const CUSTOMER_SCOPE = 'customer';

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_application_flow_for_users_and_admin()
    {
        $customer = User::factory()->create();
        $admin    = User::where('email', self::ADMIN_EMAIL)->firstOrFail();

        /* Customer Loan Request */
        Passport::actingAs($customer, [self::CUSTOMER_SCOPE]);
        $response1 = $this->withHeaders(['accept' => self::ACCEPT])->post(env('APP_URL'). '/api/submit-loan-request', [
            'amount' => self::LOAN_AMOUNT,
            'term'   => self::LOAN_TERM
        ]);

        $response1->assertStatus(200);
        $response1->assertJson(['message' => 'New Loan Request Submitted Successfully.']);
        $response1->assertJsonStructure(['loan_details' => [
            'id', 'amount', 'term', 'status', 'repayments' => ['*' => [
                'id', 'amount', 'repayment_date', 'status'
        ]]]]);

        /* Admin approve the customer loan */
        Passport::actingAs($admin, [self::ADMIN_SCOPE]);
        $response2 = $this->withHeaders(['accept' => self::ACCEPT])->post(env('APP_URL'). '/api/loan/'. $response1->getData()->loan_details->id . '/change-status', [
            'status' => LoanStatus::fromValue(LoanStatus::APPROVED)->key,
        ]);

        $response2->assertStatus(200);
        $response2->assertJson(['message' => 'Loan Status Changed Successfully. Thank you!']);

        /* Customer repay the loans */
        Passport::actingAs($customer, [self::CUSTOMER_SCOPE]);
        $response3  = $this->withHeaders(['accept' => self::ACCEPT])->post(env('APP_URL'). '/api/loan/'. $response1->getData()->loan_details->id . '/add-repayment', [
            'loan_repayment_id'  => $response1->getData()->loan_details->repayments[0]->id,
            'amount'             => $response1->getData()->loan_details->repayments[0]->amount
        ]);

        $response3->assertStatus(200);
        $response3->assertJson(['message' => 'Your Repayment is successfully completed. Thank you!']);
    }
}
