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

    // ---- store method with fingerprint check ----
    public function store(CardStoreRequest $request)
    {
        $data = $request->validated();
        $deck = Deck::findOrFail($data['deck_id']);
        if ($deck->owner_id !== $request->user()->id) abort(403);

        // compute fingerprint to prevent duplicates within same deck
        $fingerprint = Card::makeFingerprint($data['question'], $data['answer'] ?? '', $data['tags'] ?? []);
        $exists = Card::where('deck_id', $deck->id)->where('fingerprint', $fingerprint)->exists();
        if ($exists) {
            return response()->json(['message' => 'Duplicate card in this deck'], 422);
        }

        $card = Card::create([
            'deck_id' => $deck->id,
            'question' => $data['question'],
            'answer' => $data['answer'],
            'tags' => $data['tags'] ?? null,
            'fingerprint' => $fingerprint,
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

    // ---- update method with fingerprint check ----
    public function update(CardStoreRequest $request, Card $card)
    {
        $deck = $card->deck;
        if ($deck->owner_id !== $request->user()->id) abort(403);

        $data = $request->validated();

        $fingerprint = Card::makeFingerprint($data['question'] ?? $card->question, $data['answer'] ?? $card->answer, $data['tags'] ?? $card->tags);
        // if fingerprint changed, check for duplicates
        if ($fingerprint !== $card->fingerprint) {
            $exists = Card::where('deck_id', $deck->id)->where('fingerprint', $fingerprint)->exists();
            if ($exists) {
                return response()->json(['message' => 'Duplicate card in this deck'], 422);
            }
        }

        $card->update(array_merge($data, ['fingerprint' => $fingerprint]));

        return response()->json($card);
    }
// ---- MODIFICATION END ----

    public function destroy(Card $card, Request $request)
    {
        $deck = $card->deck;
        if ($deck->owner_id !== $request->user()->id) abort(403);
        $card->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
