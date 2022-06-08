<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    /**
     * Like or unlike a post
     *
     * @return \Illuminate\Http\Response
     */
    public function likeOrUnlike($id)
    {
        $post = Post::find($id);
        if (!$post) {
            return response([
                'message' => 'Post not found',
            ]);
        }

        $like = $post->like()->where('user_id', Auth::user()->id)->first();
        if (!$like) {
            Like::create([
                'post_id' => $id,
                'user_id' => Auth::user()->id
            ]);
            return response([
                'message' => 'Liked'
            ], 200);
        }
        // else dislike it
        $like->delete();

        return response([
            'message' => 'Disliked'
        ], 200);
    }
}
