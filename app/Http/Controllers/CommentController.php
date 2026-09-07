<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware(["auth", "agency"]);
    }

    public function index(Request $request)
    {
        $request->validate([
            'commentable_type' => 'nullable|string|max:255',
            'commentable_id' => 'nullable|integer',
        ]);

        $agency = $request->user()->agency;
        $commentableType = $request->input('commentable_type');
        $commentableId = $request->input('commentable_id');

        $query = Comment::where('agency_id', $agency->id)
            ->with('user')
            ->orderBy('created_at', 'desc');

        if ($commentableType && $commentableId) {
            $query->where('commentable_type', $commentableType)
                ->where('commentable_id', $commentableId);
        }

        $comments = $query->paginate(20);

        return response()->json($comments);
    }

    public function store(Request $request)
    {
        $request->validate([
            'commentable_type' => 'required|string',
            'commentable_id' => 'required|integer',
            'body' => 'required|string|max:5000',
            'parent_id' => 'nullable|integer|exists:comments,id',
        ]);

        $agency = $request->user()->agency;

        $comment = Comment::create([
            'agency_id' => $agency->id,
            'user_id' => $request->user()->id,
            'commentable_type' => $request->commentable_type,
            'commentable_id' => $request->commentable_id,
            'body' => $request->body,
            'parent_id' => $request->parent_id,
        ]);

        $comment->load('user');

        return response()->json([
            'success' => true,
            'comment' => $comment,
        ]);
    }

    public function destroy(Request $request, Comment $comment)
    {
        $agency = $request->user()->agency;
        if ($comment->agency_id !== $agency->id) {
            abort(403);
        }

        $comment->delete();

        return response()->json(['success' => true]);
    }
}
