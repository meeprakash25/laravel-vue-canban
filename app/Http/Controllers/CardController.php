<?php

namespace App\Http\Controllers;

use App\Models\Card;
use Illuminate\Http\Request;

class CardController extends Controller
{

    public function store(Request $request)
    {
        try {
            $user = auth()->user();
            $card = $user->cards()->create($request->all());
            return response()->json(['status' => 200, 'message' => 'Card added successfully', 'card' => $card->toArray()]);
        } catch (\Exception $e) {
            return response()->json(['status' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function update(Request $request, Card $card)
    {
        try {
            $card->update($request->all());
            return response()->json(['status' => 200, 'message' => 'Card updated successfully', 'card' => $card->toArray()]);
        } catch (\Exception $e) {
            return response()->json(['status' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function destroy(Card $card)
    {
        try {
            $card->delete();
            return response()->json(['status' => 200, 'message' => 'Card deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => 500, 'message' => $e->getMessage()]);
        }
    }
}
