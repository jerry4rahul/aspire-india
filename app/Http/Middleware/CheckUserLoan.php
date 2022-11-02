<?php

namespace App\Http\Middleware;

use App\Enums\LoanStatus;
use App\Models\UserLoan;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class CheckUserLoan
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::user()->loans->contains($request->route('userloan'))) {
            return new JsonResponse(['message' => 'This loan is not available for the user.'], Response::HTTP_BAD_REQUEST);
        }

        if ($request->route('userloan')->status !== LoanStatus::APPROVED) {
            return new JsonResponse(['message' => 'This loan is '. LoanStatus::fromValue($request->route('userloan')->status)->key], Response::HTTP_BAD_REQUEST);
        }

        return $next($request);
    }
}
