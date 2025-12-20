<?php

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\User\Database\Factories\AddressFactory;
use Modules\User\Enums\AddressType;

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

    protected $casts = [
        'address_type' => AddressType::class,
        'is_default' => 'boolean',
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
