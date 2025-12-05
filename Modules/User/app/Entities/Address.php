<?php

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\User\Database\Factories\AddressFactory;

class Address extends Model
{
    use HasFactory;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return AddressFactory::new();
    }

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

    public function getFullAddressAttribute()
    {
        return "{$this->street}, {$this->city}, {$this->state}, {$this->postal_code}, {$this->country}";
    }
}
