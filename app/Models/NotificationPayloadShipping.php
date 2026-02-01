<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class NotificationPayloadShipping extends Model
{
    use HasFactory;

    protected $table = 'notification_payload_shippings';

    protected $fillable = [
        'egi_blockchain_id',
        'seller_id',
        'buyer_id',
        'shipping_address_snapshot',
        'carrier',
        'tracking_code',
        'shipped_at',
        'status',
    ];

    protected $casts = [
        'shipping_address_snapshot' => 'array',
        'shipped_at' => 'datetime',
    ];

    public function egiBlockchain()
    {
        return $this->belongsTo(EgiBlockchain::class, 'egi_blockchain_id');
    }
    
    // Compatibility helper
    public function getEgiAttribute()
    {
        return $this->egiBlockchain->egi;
    }
    
    // Compatibility helper
    public function getBuyerAttribute()
    {
        return $this->egiBlockchain->buyer;
    }
    
    // Helper accessors for view/notification logic
    public function getFormattedAmountAttribute()
    {
        return $this->egiBlockchain->formatted_amount;
    }

    public function notifications(): MorphMany
    {
        return $this->morphMany(CustomDatabaseNotification::class, 'model');
    }
}
