<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Deck
 *
 * @property int $id
 * @property int $owner_id
 * @property string $title
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Deck whereOwnerId($value)
 * @mixin \Illuminate\Database\Eloquent\Model
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Card> $cards
 * @property-read int|null $cards_count
 * @property-read \App\Models\User|null $owner
 * @method static \Database\Factories\DeckFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deck newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deck newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deck query()
 * @mixin \Eloquent
 */

class Deck extends Model
{
    use HasFactory;

    protected $fillable = ['owner_id','title','description'];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function cards()
    {
        return $this->hasMany(Card::class);
    }
}
