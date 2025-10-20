<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserWallet extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'type',
        'address',
        'secret_ciphertext',
        'secret_nonce',
        'secret_tag',
        'dek_encrypted',
        'iban_encrypted',
        'iban_hash',
        'iban_last4',
        'meta',
        'cipher_algo',
        'version',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
