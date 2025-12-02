<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeckStoreRequest;
use App\Models\Deck;
use Illuminate\Http\Request;

class DeckController extends Controller
{
    // GET /api/decks
    public function index(Request $request)
    {
        $user = $request->user();
        $decks = Deck::where('owner_id', $user->id)->withCount('cards')->get();
        return response()->json($decks);
    }

    // POST /api/decks
    public function store(DeckStoreRequest $request)
    {
        $user = $request->user();
        $data = $request->validated();

        $deck = Deck::create([
            'owner_id' => $user->id,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
        ]);

        return response()->json($deck, 201);
    }

    // GET /api/decks/{deck}
    public function show(Deck $deck, Request $request)
    {
        $this->authorizeDeckOwner($deck, $request->user());
        return response()->json($deck->load('cards'));
    }

    // PUT/PATCH /api/decks/{deck}
    public function update(DeckStoreRequest $request, Deck $deck)
    {
        $this->authorizeDeckOwner($deck, $request->user());
        $deck->update($request->validated());
        return response()->json($deck);
    }

    // DELETE /api/decks/{deck}
    public function destroy(Deck $deck, Request $request)
    {
        $this->authorizeDeckOwner($deck, $request->user());
        $deck->delete();
        return response()->json(['message' => 'Deleted']);
    }

    // simple owner check
    protected function authorizeDeckOwner(Deck $deck, $user)
    {
        if ($deck->owner_id !== $user->id) {
            abort(403, 'Forbidden');
        }
    }
}
