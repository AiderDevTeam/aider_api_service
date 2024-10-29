<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'externalId' => $this->external_id,
            'firstName' => $this->first_name,
            'lastName' => $this->last_name,
            'displayName' => $this->display_name,
            'email' => $this->email,
            'birthday' => $this->birthday ?? '',
            'status' => $this->status,
            'gender' => $this->gender,
            'phone' => $this->phone,
            'callingCode' => $this->calling_code,
            'profilePhotoUrl' => $this->profile_photo_url,
            'referralCode' => $this->referral_code ?? '',
            'referralUrl' => $this->referral_url ?? '',
            'termsAndConditions' => $this->terms_and_conditions,
            'idVerified' => (boolean)$this->id_verified,
            'idVerificationStatus' => $this->id_verification_status,
            'canReceiveEmailUpdates' => $this->can_receive_email_updates,
            'canReceivePushNotifications' => $this->can_receive_push_notifications,
            'canReceiveSMS' => $this->can_receive_sms,
            'userIdentifications' => UserIdentificationResource::collection($this->whenLoaded('identifications')),
            'joinedAt' => Carbon::parse($this->created_at)->format('jS F, Y'),
            'ts' => Carbon::parse($this->created_at)->timestamp,
            'addresses' => $this->when($this->addresses, AddressResource::collection($this->whenLoaded('addresses'))),
            'bearer' => $this->when($this->token, [
                'token' => $this->token,
                'expiresIn' => $this->expiresIn
            ]),

        ];
    }
}
