<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $post = Post::find($id);
        if (!$post) {
            return response([
                'message' => 'Post not found',
            ]);
            if ($post->user_id != Auth::user()->id) {
                return response([
                    'message' => 'Permission denied.',
                ], 403);
            }
        }
        return response([
            'posts' => $post->comment()->with('user:id,name,image')->get()
        ], 200);
    }

    /**
     * Store a newly created comment.
     *
     * @param  \Illuminate\Http\Request  $request, $id
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {
        $post = Post::find($id);
        if (!$post) {
            return response([
                'message' => 'Post not found',
            ]);
        }
        $attributes = $request->validate([
            'comment' => 'required|string'
        ]);
        $comment = Comment::create([
            'comment' => $attributes['comment'],
            'post_id' => $post->id,
            'user_id' => Auth::user()->id,
        ]);
        return response([
            'message' => 'Comment created successfuly',
            'comment' => $comment,
        ]);
    }


    /**
     * Update a comment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $comment = Comment::find($id);
        if (!$comment) {
            return response([
                'message' => 'Comment not found',
            ]);
        } elseif ($comment->user_id != Auth::user()->id) {
            return response([
                'message' => 'Permission denied',
            ]);
        }
        $attributes = $request->validate([
            'comment' => 'required|string'
        ]);
        $comment->update([
            'comment' => $attributes['comment'],
        ]);
        return response([
            'message' => 'Comment updated successfuly',
            'comment' => $comment,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $comment = Comment::find($id);
        if (!$comment) {
            return response([
                'message' => 'Comment not found',
            ]);
        } elseif ($comment->user_id != Auth::user()->id) {
            return response([
                'message' => 'Permission denied',
            ]);
        }
        $comment->delete();
        return response([
            'message' => 'Comment deleted successfuly',
        ]);
    }
}
