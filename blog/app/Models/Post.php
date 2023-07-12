<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Http\Request;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'author_id',
    ];

    public function getAllPosts()
    {
        $posts = Post::with('author')->get();

        // Transform the posts to include the author's name instead of author_id
        $data = $posts->map(function ($post) {
            return [
                'id' => $post->id,
                'title' => $post->title,
                'content' => $post->content,
                'author_name' => $post->author->name,
            ];
        });

        return $data;
    }

    public function getPostById(Request $request,Post $id)
    {
        $post = Post::with('author')->findOrFail($id);

        $postData = [
            'id' => $post->id,
            'title' => $post->title,
            'content' => $post->content,
            'author_name' => $post->author->name,
        ];

        // Return the post data as JSON response
        return response()->json($postData);

    }

    public function createPost($data)
    {
        return $this->create($data);
    }

    public function updatePost($id, $data)
    {
      if (! Gate::allows('update-post', $post)) {
            abort(403);
      }
  
      $item = $this->findOrFail($id);
      $item->update($data);
      return $item;
    }

    public function deletePost($id)
    {
        $item = $this->findOrFail($id);
        $item->delete();
    }

    /*  Database Relationshpis */


    public function author()
    {
        return $this->belongsTo(User::class);
    }
}