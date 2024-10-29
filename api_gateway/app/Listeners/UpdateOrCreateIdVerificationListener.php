<?php

namespace App\Listeners;

use App\Events\UpdateOrCreateIdVerificationEvent;
use App\Models\IdVerification;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateOrCreateIdVerificationListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     */
    public function handle(UpdateOrCreateIdVerificationEvent $event): void
    {
        try {
            logger('### UPDATING/STORING VERIFICATION DATA ###');
            $data = $event->data;
            $idVerification = $event->idVerification;
            $userDetails = $data['details']['data']['person'];
            $userContact = $data['details']['data']['person']['contact'];
            $addressData = $data['details']['data']['person']['addresses'];

            $addressDetails = $this->getAddressDetails($addressData);

            IdVerification::query()->updateOrCreate([
                'id_number' => $idVerification?->id_number
            ], [
                'id_number' => $data['idNumber'],
                'type' => $data['idType'],
                'verified' => true,
                'card_id' => $userDetails['cardId'] ?? '',
                'card_valid_from' => $userDetails['cardValidFrom'] ?? '',
                'card_valid_to' => $userDetails['cardValidTo'] ?? '',
                'surname' => $userDetails['surname'] ?? '',
                'forenames' => $userDetails['forenames'] ?? '',
                'nationality' => $userDetails['nationality'] ?? '',
                'birth_date' => $userDetails['birthDate'] ?? '',
                'gender' => $userDetails['gender'] ?? '',
                'email' => $userContact['email'] ?? '',
                'phone_number' => $userContact['phoneNumbers'][0]['phoneNumber'] ?? '',
                'birth_country' => $userDetails['birthCountry'] ?? '',
                'birth_district' => $userDetails['birthDistrict'] ?? '',
                'birth_region' => $userDetails['birthRegion'] ?? '',
                'birth_town' => $userDetails['birthTown'] ?? '',
                'home_town' => $addressDetails['homeTown'] ?? '',
                'home_town_country' => $addressDetails['homeTownCountry'] ?? '',
                'home_town_district' => $addressDetails['homeTownDistrict'] ?? '',
                'home_town_region' => $addressDetails['homeTownRegion'] ?? '',
                'residence' => $addressDetails['residence'] ?? '',
                'residence_street' => $addressDetails['residenceStreet'] ?? '',
                'residence_district' => $addressDetails['residenceDistrict'] ?? '',
                'residence_postal_code' => $addressDetails['residencePostalCode'] ?? '',
                'residence_region' => $addressDetails['residenceRegion'] ?? '',
                'residence_digital_address' => $addressDetails['residenceDigitalAddress'] ?? '',
                'longitude' => $addressDetails['longitude'] ?? '',
                'latitude' => $addressDetails['latitude'] ?? '',
                'occupation' => $data['details']['data']['person']['occupations'][0]['name'] ?? '',
                'signature_url' => $data['signatureUrl'] ?? '',
                'photo_on_id_url' => $data['photoOnIdUrl'] ?? '',
            ]);

        } catch (Exception $exception) {
            report($exception);
        }
    }

    private function getAddressDetails(array $addressData): array
    {
        $addressDetails = [];
        $homeTownDetails = [];

        foreach ($addressData as $address) {
            if (isset($address['type']) && strtolower($address['type']) === IdVerification::HOME_TOWN) {
                $homeTownDetails =
                    [
                        'homeTown' => $address['town'],
                        'homeTownCountry' => $address['countryName'],
                        'homeTownDistrict' => $address['districtName'],
                        'homeTownRegion' => $address['region'],
                    ];
            }

            if (isset($address['type']) && strtolower($address['type']) === IdVerification::RESIDENCE) {
                $gpsDetails = $address['gpsAddressDetails'];
                $addressDetails =
                    [
                        ...$homeTownDetails,
                        'residence' => $address['town'],
                        'residenceStreet' => $gpsDetails['street'],
                        'residenceDistrict' => $address['region'],
                        'residencePostalCode' => $address['postalCode'],
                        'residenceRegion' => $address['region'],
                        'residenceDigitalAddress' => $address['addressDigital'],
                        'longitude' => $gpsDetails['longitude'],
                        'latitude' => $gpsDetails['latitude'],
                    ];
            }
        }
        return $addressDetails ?: $homeTownDetails;
    }
}
