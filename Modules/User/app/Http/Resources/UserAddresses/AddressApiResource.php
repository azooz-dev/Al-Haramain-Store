<?php

namespace Modules\User\app\Http\Resources\UserAddresses;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressApiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'identifier' => (int) $this->id,
            'addressType' => (string) $this->address_type,
            "label" => (string) $this->label,
            'street' => (string) $this->street,
            "city" => (string) $this->city,
            "state" => (string) $this->state,
            "postalCode" => (string) $this->postal_code,
            "country" => (string) $this->country,
            "isDefault" => (bool) $this->is_default,
            "createdDate" => (string) $this->created_at,
            "lastChange" => (string) $this->updated_at
        ];
    }

    public static function transformAttribute($index)
    {
        $attribute = [
            'identifier'  => 'id',
            'addressType'   => 'address_type',
            'label'    => 'label',
            'street'       => 'street',
            'city'       => 'city',
            'state'    => 'state',
            'postalCode'    => 'postal_code',
            'country'    => 'country',
            'createdDate' => 'created_at',
            'lastChange'  => 'updated_at',
        ];

        return isset($attribute[$index]) ? $attribute[$index] : null;
    }
}
