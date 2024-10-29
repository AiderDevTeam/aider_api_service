<?php

namespace App\Http\Resources;

use App\Models\DeliveryPayment;
use App\Models\VASPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        //return parent::toArray($request);
        return [
                "externalId" => $this->external_id,
                "amount"=> $this->amount,
                "value"=> $this->paymentable?->value,
                "form"=> $this->getPaymentForm($this->paymentable_type),
                "type" => $this->type,
                "destinationAccountName"=> $this->destination_account_name,
                "destinationAccountNumber" =>  $this->destination_account_number,
                "destinationSortCode" => $this->destination_sort_code,
                "disbursementStatus" => $this->disbursement_status,
                "disbursementStatusUpdatedAt" => Carbon::parse($this->disbursement_status_updated_at)->toDateString('Y-m-d'),
                "collectionSortCode" => $this->collection_sort_code,
                "collectionAccountName" => $this->collection_account_name,
                "collectionAccountNumber"=> $this->collection_account_number,
                "collectionStatus" => $this->collection_status,
                "collectionStatusUpdatedAt" => $this->collection_status_updated_at,
                "createdAt"=>Carbon::parse($this->created_at)->toDateString('Y-m-d'),
                "updatedAt"=> Carbon::parse($this->updated_at)->toDateString('Y-m-d')
        ];
    }

    public function getPaymentForm(string $paymentableType): string
    {
        return match ($paymentableType) {
            VASPayment::class => 'vas',
            DeliveryPayment::class => 'delivery',
            default => 'payment'
        };
    }
}
