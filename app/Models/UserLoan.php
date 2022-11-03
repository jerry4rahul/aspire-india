<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserLoan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'amount',
        'term',
        'status'
    ];

    /**
     * Get the user that owns the UserLoan
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all of the loanRepayments for the UserLoan
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function loanRepayments(): HasMany
    {
        return $this->hasMany(LoanRepayment::class);
    }

    /**
     * Get all of the repaymentRecords for the UserLoan
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function repaymentRecords(): HasMany
    {
        return $this->hasMany(RepaymentRecord::class);
    }

    /**
     * @return int|float
     */
    public function remainingRepayment()
    {
        return round($this->loanRepayments->sum('amount') - $this->repaymentRecords->sum('amount'), 2);
    }
}
