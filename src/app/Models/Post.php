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

  public function getAllPosts() {
    return Post::with("user")->
      orderBy("created_at", "desc")->
      paginate(15);
  }

  public function getPostById($id) {
    return Post::with("user")->find($id);
  }

  public function createPost($postData) {
    return Post::create($postData);
  }

  public function deletePostById($id) {
    return Post::find($id)->destroy($id);
  }
}
