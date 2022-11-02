<?php

use App\Models\UserLoan;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRepaymentRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('repayment_records', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(UserLoan::class)->constrained('user_loans')->cascadeOnDelete()->cascadeOnUpdate();
            $table->decimal('amount', 15, 2);
            $table->date('paid_on');
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
        Schema::dropIfExists('repayment_records');
    }
}
