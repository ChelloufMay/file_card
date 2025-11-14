<?php

namespace App\Models;

use Database\Factories\CardFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Card
 *
 * @property int $id
 * @property int $deck_id
 * @property string $question
 * @property string $answer
 * @property array|null $tags
 * @property int $box_level
 * @property int $repetitions
 * @property float $easiness_factor
 * @property int $interval_days
 * @property Carbon|null $next_review_at
 * @property Carbon|null $last_reviewed_at
 * @method static Builder|Card whereDeckId($value)
 * @mixin Model
 * @property-read Deck|null $deck
 * @property-read Collection<int, Review> $reviews
 * @property-read int|null $reviews_count
 * @method static CardFactory factory($count = null, $state = [])
 * @method static Builder<static>|Card newModelQuery()
 * @method static Builder<static>|Card newQuery()
 * @method static Builder<static>|Card query()
 * @mixin Eloquent
 */

class Card extends Model
{
    use HasFactory;

    protected $fillable = [
        'deck_id','question','answer','tags',
        'box_level','repetitions','easiness_factor',
        'interval_days','next_review_at','last_reviewed_at'
    ];

    protected $casts = [
        'tags' => 'array',
        'next_review_at' => 'datetime',
        'last_reviewed_at' => 'datetime',
    ];

    public function deck(): BelongsTo
    {
        return $this->belongsTo(Deck::class);
    }

    public function reviews(): Builder|HasMany
    {
        return $this->hasMany(Review::class);
    }
}
