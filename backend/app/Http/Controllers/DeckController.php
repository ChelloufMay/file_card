<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DeckController extends Controller
{
    public function index()
    {
        // test simple : retourne JSON vide (remplace par ta logique réelle)
        return response()->json(['decks' => []], 200);
    }
}
