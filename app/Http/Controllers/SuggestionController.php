<?php

namespace App\Http\Controllers;

use App\Models\Suggestion;
use Illuminate\Http\Request;
use App\Models\SuggestionReply;
class SuggestionController extends Controller
{
    public function index(Request $request)
    {
        $query = Suggestion::query();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $suggestions = $query->with('user')->latest()->paginate(10);
        
        return view('dashboard.suggestions.index', compact('suggestions'));
    }

    public function show($id)
    {
        $suggestion = \App\Models\Suggestion::findOrFail($id);
        $replies = $suggestion->replies()->with('user')->orderBy('created_at', 'asc')->get();

        return view('dashboard.suggestions.show', compact('suggestion', 'replies'));
    }

    public function update(Request $request, Suggestion $suggestion)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,reviewed,resolved,rejected'
        ]);

        $suggestion->update($validated);

        return redirect()->route('suggestions.index')
            ->with('success', 'تم تحديث حالة الاقتراح/الشكوى بنجاح');
    }

    public function destroy(Suggestion $suggestion)
    {
        $suggestion->delete();
        return redirect()->route('suggestions.index')
            ->with('success', 'تم حذف الاقتراح/الشكوى بنجاح');
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

    public function getReplies($suggestionId)
    {
        $replies = SuggestionReply::where('suggestion_id', $suggestionId)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();    

        return view('dashboard.suggestions.replies', compact('replies'));
    }

    
    
}