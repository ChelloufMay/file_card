<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CardStoreRequest;
use App\Models\Card;
use App\Models\Deck;
use Illuminate\Http\Request;

class CardController extends Controller
{
    public function index(Deck $deck, Request $request)
    {
        $user = $request->user();
        if ($deck->owner_id !== $user->id) abort(403);
        $cards = $deck->cards()->get();
        return response()->json($cards);
    }

    public function store(CardStoreRequest $request)
    {
        $data = $request->validated();
        $deck = Deck::findOrFail($data['deck_id']);
        if ($deck->owner_id !== $request->user()->id) abort(403);

        $card = Card::create([
            'deck_id' => $deck->id,
            'question' => $data['question'],
            'answer' => $data['answer'],
            'tags' => $data['tags'] ?? null,
            'box_level' => 1,
            'repetitions' => 0,
            'easiness_factor' => 2.5,
            'interval_days' => 0,
        ]);

        return response()->json($card, 201);
    }

    public function show(Card $card, Request $request)
    {
        $deck = $card->deck;
        if ($deck->owner_id !== $request->user()->id) abort(403);
        return response()->json($card);
    }

    public function update(CardStoreRequest $request, Card $card)
    {
        $deck = $card->deck;
        if ($deck->owner_id !== $request->user()->id) abort(403);
        $card->update($request->validated());
        return response()->json($card);
    }

    public function destroy(Card $card, Request $request)
    {
        $deck = $card->deck;
        if ($deck->owner_id !== $request->user()->id) abort(403);
        $card->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
