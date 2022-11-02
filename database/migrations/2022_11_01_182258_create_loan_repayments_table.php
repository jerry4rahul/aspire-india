<?php

use App\Enums\LoanStatus;
use App\Models\UserLoan;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoanRepaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_repayments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(UserLoan::class)->constrained('user_loans')->cascadeOnDelete()->cascadeOnUpdate();
            $table->decimal('amount', 15, 2);
            $table->date('repayment_date');
            $table->integer('status')->default(LoanStatus::PENDING);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loan_repayments');
    }
}
