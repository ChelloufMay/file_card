<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\VerificationCode
 *
 * @property int $id
 * @property int|null $user_id
 * @property string|null $contact
 * @property string $code
 * @property string $method
 * @property string $purpose
 * @property string $token
 * @property Carbon $expires_at
 * @property bool $used
 * @mixin Model
 */
class VerificationCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','contact','code','method','purpose','token','expires_at','used'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
