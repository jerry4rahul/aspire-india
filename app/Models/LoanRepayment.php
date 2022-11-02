<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanRepayment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_loan_id',
        'amount',
        'repayment_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'repayment_date' => 'datetime:Y-m-d',
    ];

    /**
     * Get the userLoan that owns the LoanRepayment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function userLoan(): BelongsTo
    {
        return $this->belongsTo(UserLoan::class);
    }
}
