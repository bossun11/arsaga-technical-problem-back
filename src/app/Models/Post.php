<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
  use HasFactory;

  protected $fillable = [
    "user_id",
    "title",
    "content",
    "image",
  ];

  public function user() {
    return $this->belongsTo(User::class);
  }

  public function tags() {
    return $this->belongsToMany(Tag::class);
  }

  public function getAllPosts() {
    return Post::with(["user:id,name", "tags:id,name"])->
      orderBy("created_at", "desc")->
      paginate(15);
  }

  public function getPostById($id) {
    return Post::with(["user:id,name", "tags:id,name"])->find($id);
  }

  public function createPost($postData) {
    return Post::create($postData);
  }

  public function updatePostById($id, $postData) {
    $post = Post::with("user:id,name")->find($id);
    $post->fill($postData)->save();
    return $post;
  }

  public function deletePostById($id) {
    return Post::find($id)->destroy($id);
  }

  public function findByTag($tagName) {
    return Post::whereHas("tags", function ($query) use ($tagName) {
      $query->where("name", "LIKE", "%{$tagName}%");
    })->with(["user:id,name", "tags:id,name"])->orderBy("created_at", "desc")->paginate(15);
  }
}
