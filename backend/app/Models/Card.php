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

    public mixed $fingerprint;
    protected $fillable = [
        'deck_id','question','answer','tags','fingerprint',
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
    // ---- MODIFICATION START: makeFingerprint helper (used for duplicate prevention) ----
    /**
     * Compute a stable fingerprint for duplication detection.
     * Normalizes question, answer and tags (sorted) before hashing.
     *
     * @param string $question
     * @param string $answer
     * @param array|string|null $tags
     * @return string
     */
    public static function makeFingerprint(string $question, string $answer, array|string $tags = null): string
    {
        $normalize = function ($s) {
            $s = mb_strtolower(trim((string)$s));
            $s = preg_replace('/[^\p{L}\p{N}\s]/u', '', $s);
            return preg_replace('/\s+/', ' ', $s);
        };

        $q = $normalize($question);
        $a = $normalize($answer);

        $t = '';
        if ($tags) {
            if (!is_array($tags)) {
                $decoded = json_decode($tags, true);
                $tagsArr = is_array($decoded) ? $decoded : (is_string($tags) ? [$tags] : []);
            } else {
                $tagsArr = $tags;
            }
            $tagsArr = array_map(fn($s) => $normalize($s), $tagsArr);
            sort($tagsArr);
            $t = implode('|', $tagsArr);
        }

        return hash('sha256', $q . '||' . $a . '||' . $t);
    }
    // ---- MODIFICATION END ----
}
