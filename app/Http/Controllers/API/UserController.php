<?php

namespace App\Http\Controllers\API;

use App\Enums\LoanStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\LoanRepaymentResource;
use App\Http\Resources\LoanResource;
use App\Models\LoanRepayment;
use App\Models\UserLoan;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use BenSampo\Enum\Rules\EnumKey;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return JsonResponse
     */
    public function submitLoanRequest(Request $request): JsonResponse
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'term'   => ['required', 'numeric', 'min:1']
        ]);

        try {
            $loan = Auth::user()->loans()->create([
                'amount' => $data['amount'],
                'term'   => $data['term']
            ]);

            for ($i = 1; $i <= $data['term']; $i++) {
                $loan->loanRepayments()->create([
                    'amount'         => round($data['amount'] / $data['term'], 2),
                    'repayment_date' => Carbon::now()->addWeek($i),
                ]);
            }

            return new JsonResponse([
                'message'   => 'New Loan Request Submitted Successfully.',
                'status'    => LoanStatus::fromValue(LoanStatus::PENDING)->key,
                'repayment' => LoanRepaymentResource::collection($loan->loanRepayments) ], Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::channel('user')->critical($e->getMessage(), ['data' => $data, 'method' => __METHOD__]);
            return new JsonResponse(['message' => "We are unable to submit your request. Please try after sometime."], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return JsonResponse
     */
    public function allLoans(): JsonResponse
    {
        try {
            $loans = Auth::user()->loans;
            return new JsonResponse(['data' => LoanResource::collection($loans)], Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::channel('user')->critical($e->getMessage(), ['method' => __METHOD__]);
            return new JsonResponse(['message' => "We are unable to get loans. Please try after sometime."], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * @param \App\Models\UserLoan $userloan
     * @param \Illuminate\Http\Request $request
     *
     * @return JsonResponse
     */
    public function changeLoanStatus(UserLoan $userloan, Request $request): JsonResponse
    {
        $data = $request->validate([
            'status' => ['required', new EnumKey(LoanStatus::class)],
        ]);

        try {
            $userloan->status = LoanStatus::fromKey($data['status'])->value;
            $userloan->save();

            return new JsonResponse(['message' => 'Loan Status Changed Successfully. Thank you!'], Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::channel('user')->critical($e->getMessage(), ['method' => __METHOD__]);
            return new JsonResponse(['message' => "We are unable to change loan status. Please try after sometime."], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * @param \App\Models\UserLoan $userloan
     * @param \Illuminate\Http\Request $request
     *
     * @return JsonResponse
     */
    public function addLoanRepayment(UserLoan $userloan, Request $request): JsonResponse
    {
        $data = $request->validate([
            'loan_repayment_id' => ['required', 'exists:loan_repayments,id', Rule::in($userloan->loanRepayments->pluck('id'))],
            'amount'            => ['required', 'numeric', 'max:' . $userloan->amount],
        ]);

        try {
            $repayment = LoanRepayment::findOrFail($data['loan_repayment_id']);

            switch ($repayment) {
                case $repayment->status === LoanStatus::PAID:
                    return new JsonResponse(['message' => 'This repayment is already paid.'], Response::HTTP_UNPROCESSABLE_ENTITY);
                    break;

                case $data['amount'] > $userloan->remainingRepayment():
                    return new JsonResponse(['message' => 'Your remaining loan amount is '. $userloan->remainingRepayment() .'. Please input exact or below amount.'], Response::HTTP_UNPROCESSABLE_ENTITY);
                    break;

                case $data['amount'] < $repayment->amount && $data['amount'] !== $userloan->remainingRepayment():
                    return new JsonResponse(['message' => 'Amount should be greater or equal to repayment amount.'], Response::HTTP_UNPROCESSABLE_ENTITY);
                    break;

                default:
                    $repayment->status = LoanStatus::PAID;
                    $repayment->save();
                    $userloan->repaymentRecords()->create(['amount' => $data['amount'], 'paid_on' => Carbon::now()]);

                    if ($userloan->refresh()->loanRepayments->whereIn('status', LoanStatus::PENDING)->count() == 0) {
                        $userloan->status = LoanStatus::PAID;
                        $userloan->save();
                    }

                    break;
            }

            return new JsonResponse(['message' => 'Your Repayment is successfully completed. Thank you!'], Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::channel('user')->critical($e->getMessage(), ['method' => __METHOD__]);
            return new JsonResponse(['message' => "We are unable to repay your loan. Please try after sometime."], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
