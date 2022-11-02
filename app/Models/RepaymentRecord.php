<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RepaymentRecord extends Model
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
        'paid_on',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'paid_on' => 'datetime:Y-m-d',
    ];

    /**
     * Get the userLoan that owns the RepaymentRecord
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function userLoan(): BelongsTo
    {
        return $this->belongsTo(UserLoan::class);
    }
}
