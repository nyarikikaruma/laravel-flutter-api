<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response([
            'posts' => Post::orderBy('created_at', 'desc')->with('user:id,name,image')->withCount('comment', 'like')
                ->with('like', function ($like) {
                    return $like->where('user_id', auth()->user()->id)
                        ->select('id', 'user_id', 'post_id')->get();
                })
                ->get()
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $attributes = $request->validate([
            'body' => 'required|string',
            'image' => 'image'
        ]);
        $newImageName = time() . '-' . $request->name . '.' .
            $request->image->extension();
        $request->image->move(public_path('images'), $newImageName);
        $attributes['image'] = $newImageName;
        $post = Post::create([
            'body' => $attributes['body'],
            'user_id' => Auth::user()->id,
            'image' => $attributes['image']
        ]);
        return response([
            'message' => 'Post created successfuly',
            'post' => $post
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response([
            'post' => Post::where('id', $id)->withCount('comment', 'like')->get()
        ], 200);
    }

    /**
     * Update a post.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
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
        $attributes = $request->validate([
            'body' => 'required|string'
        ]);
        $post->update([
            'body' => $attributes['body']
        ]);
        return response([
            'message' => 'Post updated successfuly',
            'post' => $post
        ], 200);
    }

    /**
     * Delete a post from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
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
        $post->delete();
        return response([
            'message' => 'Post deleted successfully'
        ]);
    }
}
