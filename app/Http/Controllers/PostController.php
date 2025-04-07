<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'message' => 'Posts retrieved successfully',
            'posts' => PostResource::collection(Post::all())
        ],200);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(PostRequest $request)
    {
        $post = Post::create([
            'title' => $request->title,
            'content' => $request->content,
            'user_id' => Auth::id()
        ]);
        return response()->json([
            'message' => 'Post created successfully',
            'post' => new PostResource($post)
        ],200);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
       return response()->json([
           'message' => 'Post retrieved successfully',
           'post' => new PostResource(Post::findOrFail($id))
       ],200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostRequest $request, int $id)
    {
        // Check if the post Exists and belongs to the user
        $post = Post::find($id);
        if (!$this->postExists($post)){
            return response()->json(['message' => 'Post not found'], 404);
        }
        if (!$this->postOwner($post)){
            return response()->json(['message' => 'The user can only make changes to his own posts'], 403);
        }

        // Update the post
        $post->update([
            'title' => $request->title,
            'content' => $request->content
        ]);
        return response()->json([
            'message' => 'Post updated successfully',
            'post' => new PostResource($post)
        ],200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        // Check if the post Exists and belongs to the user
        $post = Post::find($id);
        if (!$this->postExists($post)){
            return response()->json(['message' => 'Post not found'], 404);
        }
        if (!$this->postOwner($post)){
            return response()->json(['message' => 'The user can only make changes to his own posts'], 403);
        }
        //Delete the post
        Post::destroy($id);
        return response()->json([
            'message' => 'Post deleted successfully',
        ],200);
    }



    private function postExists(?Post $post =null)
    {
        // Check if the post exists
        if (!$post){
            return false;
        }
        return true;
    }

    private function postOwner(Post $post)
    {
        // Check if the post belongs to the user
        if ($post->user_id !== Auth::id()) {
            return false;
        }
        return true;
    }
}
