<?php

use App\Enums\UserScope;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Middleware\CheckUserLoan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::group(['as' => 'customer.', 'middleware' => ['auth:api', 'scope:'. array_keys(UserScope::TOKENS_CAN)[0]]], function () {

    Route::post('/submit-loan-request', [UserController::class, 'submitLoanRequest'])->name('submitLoanRequest');
    Route::get('/all-loans', [UserController::class, 'allLoans'])->name('allLoans');
    Route::post('/loan/{userloan}/add-repayment', [UserController::class, 'addLoanRepayment'])->name('addLoanRepayment')->middleware(CheckUserLoan::class);

});

Route::post('/loan/{userloan}/change-status', [UserController::class, 'changeLoanStatus'])->name('admin.changeLoanStatus')->middleware(['auth:api', 'scope:'. array_keys(UserScope::TOKENS_CAN)[1]]);
