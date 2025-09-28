<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $notes = Auth::user()->notes;
        return response()->json(['data' => $notes]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $note = Auth::user()->notes()->create($validatedData);

        return response()->json(['data' => $note], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Note $note)
    {
        if (Auth::id() !== $note->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        return response()->json(['data' => $note]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Note $note)
    {
        if (Auth::id() !== $note->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validatedData = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
        ]);

        $note->update($validatedData);

        return response()->json(['data' => $note]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Note $note)
    {
        if (Auth::id() !== $note->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $note->delete();

        return response()->json(null, 204);
    }
}
