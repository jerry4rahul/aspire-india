<?php

namespace App\Http\Resources;

use App\Enums\LoanStatus;
use Illuminate\Http\Resources\Json\JsonResource;

class LoanRepaymentResource extends JsonResource
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
            'id'             => $this->id,
            'amount'         => $this->amount,
            'repayment_date' => $this->repayment_date->format('d M, Y'),
            'status'         => LoanStatus::fromValue($this->status)->key
        ];
    }
}
