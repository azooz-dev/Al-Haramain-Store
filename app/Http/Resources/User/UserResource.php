<?php

namespace App\Http\Resources\User;

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
            'identifier' => (int) $this->id,
            'firstName' => (string) $this->first_name,
            'lastName' => (string) $this->last_name,
            'email' => (string) $this->email,
            'phone' => (int) $this->phone,
            'isVerified'  => (bool) $this->isVerified() == 'true',
            'createdDate' => (string) $this->created_at,
            'lastChange'  => (string) $this->updated_at,
        ];
    }

    public static function transformAttribute($index)
    {
        $attribute = [
            'identifier'  => 'id',
            'firstName'   => 'first_name',
            'lastName'    => 'last_name',
            'email'       => 'email',
            'phone'       => 'phone',
            'password'    => 'password',
            'createdDate' => 'created_at',
            'lastChange'  => 'updated_at',
        ];

        return isset($attribute[$index]) ? $attribute[$index] : null;
    }
}
