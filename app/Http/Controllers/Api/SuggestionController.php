<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Suggestion;
use Illuminate\Http\Request;
use App\Models\SuggestionReply;
class SuggestionController extends Controller
{
    /**
     * Store a newly created suggestion or complaint.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:suggestion,complaint',
            'content' => 'required|string',
        ]);

        $suggestion = Suggestion::create([
            'user_id' => $request->user()->id,
            'type' => $validated['type'],
            'content' => $validated['content'],
            'status' => 'pending'
        ]);

        return response()->json([
            'message' => 'Your ' . $validated['type'] . ' has been submitted successfully',
            'data' => $suggestion
        ], 201);
    }

    /**
     * Get all suggestions/complaints for the authenticated user.
     */
    public function index(Request $request)
    {
        $suggestions = Suggestion::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($suggestions);
    }

    /**
     * Get a specific suggestion/complaint.
     */
    public function show($id)
    {
        $suggestion = Suggestion::where('user_id', auth()->id())
            ->findOrFail($id);

        return response()->json($suggestion);
    }

    /**
     * Update the status of a suggestion/complaint (for admin use).
     */
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,reviewed,resolved,rejected'
        ]);

        $suggestion = Suggestion::findOrFail($id);
        $suggestion->update(['status' => $validated['status']]);

        return response()->json([
            'message' => 'Status updated successfully',
            'data' => $suggestion
        ]);
    }
    public function getByUser($userId)
    {
        // Get all suggestions and complaints for the user
        $items = Suggestion::where('user_id', $userId)
            ->whereIn('type', ['suggestion', 'complaint'])
            ->orderBy('created_at', 'desc')
            ->get();
    
        return response()->json([
            'data' => $items
        ]);
    }

    public function getReplies($suggestionId)
    {
        $replies =SuggestionReply::where('suggestion_id', $suggestionId)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'data' => $replies
        ]);
    }

    public function addReply(Request $request, $suggestionId)
    {
        $request->validate([
            'reply' => 'required|string',
        ]);

        // Optionally, check if the suggestion exists
        $suggestion = Suggestion::findOrFail($suggestionId);

        $reply = SuggestionReply::create([
            'suggestion_id' => $suggestionId,
            'reply' => $request->reply,
            'user_id' => auth()->id(), // assumes user is authenticated
        ]);

        return response()->json([
            'message' => 'Reply added successfully',
            'data' => $reply
        ], 201);
    }

    public function storeReply(Request $request, $id)
    {
        $request->validate([
            'reply' => 'required|string',
        ]);

        $suggestion = \App\Models\Suggestion::findOrFail($id);

        \App\Models\SuggestionReply::create([
            'suggestion_id' => $suggestion->id,
            'reply' => $request->reply,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('suggestions.show', $id)->with('success', 'تم إضافة الرد بنجاح');
    }

}