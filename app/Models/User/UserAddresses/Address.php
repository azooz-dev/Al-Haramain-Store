<?php

namespace App\Models\User\UserAddresses;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use HasFactory;

    const ADDRESS_TYPE_HOME = 'Home';
    const ADDRESS_TYPE_WORK = 'Work';
    const ADDRESS_TYPE_OTHER = "Other";

    protected $table = 'addresses';
    protected $fillable = [
        'user_id',
        'address_type',
        'label',
        'street',
        'city',
        'state',
        'postal_code',
        'country',
        'is_default',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
