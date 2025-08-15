<?php

namespace App\Models\User\UserAddresses;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'user_id',
        'full_name',
        'phone',
        'street',
        'city',
        'state',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
