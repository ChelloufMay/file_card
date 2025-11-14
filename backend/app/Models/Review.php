<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Review
 *
 * @property int $id
 * @property int $card_id
 * @property int $user_id
 * @property string $result
 * @property string|null $comment
 * @method static Builder|Review whereCardId($value)
 * @mixin Model
 * @property-read Card|null $card
 * @property-read User|null $user
 * @method static \Database\Factories\ReviewFactory factory($count = null, $state = [])
 * @method static Builder<static>|Review newModelQuery()
 * @method static Builder<static>|Review newQuery()
 * @method static Builder<static>|Review query()
 * @mixin Eloquent
 */

class Review extends Model
{
    use HasFactory;

    protected $fillable = ['card_id','user_id','result','comment'];

    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
