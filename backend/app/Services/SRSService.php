<?php

namespace App\Services;

use App\Models\Card;
use App\Models\Review;
use Carbon\Carbon;

class SRSService
{
    protected array $intervals = [1,3,7,14,30];

    public function __construct(array $intervals = null)
    {
        if ($intervals) $this->intervals = $intervals;
    }

    public function applyReview(Card $card, string $result, $user, ?string $comment = null): Review
    {
        $now = Carbon::now();

        $box = $card->box_level ?? 1;
        if ($result === 'easy') {
            $box = min(5, $box + 1);
        } elseif ($result === 'hard') {
            $box = max(1, $box - 1);
        }

        $intervalIndex = max(0, min(count($this->intervals) - 1, $box - 1));
        $intervalDays = $this->intervals[$intervalIndex];

        $card->box_level = $box;
        $card->interval_days = $intervalDays;
        $card->next_review_at = $now->copy()->addDays($intervalDays);
        $card->last_reviewed_at = $now;
        $card->repetitions = ($card->repetitions ?? 0) + 1;
        $card->save();

        $review = Review::create([
            'card_id' => $card->id,
            'user_id' => $user->id,
            'result' => $result,
            'comment' => $comment,
        ]);

        return $review;
    }
}
