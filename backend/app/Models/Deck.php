<?php

namespace App\Models;

use Database\Factories\DeckFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Deck
 *
 * @property int $id
 * @property int $owner_id
 * @property string $title
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Deck whereOwnerId($value)
 * @mixin Model
 * @property-read Collection<int, Card> $cards
 * @property-read int|null $cards_count
 * @property-read User|null $owner
 * @method static DeckFactory factory($count = null, $state = [])
 * @method static Builder<static>|Deck newModelQuery()
 * @method static Builder<static>|Deck newQuery()
 * @method static Builder<static>|Deck query()
 * @mixin Eloquent
 */

class Deck extends Model
{
    use HasFactory;

    protected $fillable = ['owner_id','title','description'];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function cards(): Builder|HasMany
    {
        return $this->hasMany(Card::class);
    }
}
