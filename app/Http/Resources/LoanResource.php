<?php

namespace App\Http\Resources;

use App\Enums\LoanStatus;
use Illuminate\Http\Resources\Json\JsonResource;

class LoanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'amount'     => $this->amount,
            'term'       => $this->term,
            'status'     => LoanStatus::fromValue($this->status)->key,
            'repayments' => LoanRepaymentResource::collection($this->loanRepayments)
        ];
    }
}
