<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReviewStoreRequest;
use App\Models\Card;
use App\Services\SRSService;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    protected SRSService $srs;

    public function __construct(SRSService $srs)
    {
        $this->srs = $srs;
    }

    public function due(Request $request)
    {
        $user = $request->user();
        $now = now();
        $cards = Card::whereHas('deck', function ($q) use ($user) {
            $q->where('owner_id', $user->id);
        })->where(function ($q) use ($now) {
            $q->whereNull('next_review_at')
                ->orWhere('next_review_at', '<=', $now);
        })->get();

        return response()->json($cards);
    }

    public function review(ReviewStoreRequest $request, Card $card)
    {
        $user = $request->user();
        if ($card->deck->owner_id !== $user->id) abort(403);

        $data = $request->validated();
        $result = $data['result'];
        $comment = $data['comment'] ?? null;

        $review = $this->srs->applyReview($card, $result, $user, $comment);

        return response()->json([
            'card' => $card->fresh(),
            'review' => $review,
        ], 201);
    }
}
