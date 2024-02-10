<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
  use HasFactory;

  const PAGINATION_COUNT = 15;

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
    return $this->with(["user:id,name", "tags:id,name"])->
      latest()->
      paginate(self::PAGINATION_COUNT);
  }

  public function getPostById($id) {
    return $this->with(["user:id,name", "tags:id,name"])->find($id);
  }

  public function createPost($postData, $tags = []) {
    $post = $this->create($postData);
    $this->syncTags($post, $tags);
    return $post;
  }

  public function updatePostById($id, $postData, $tags = []) {
    $post = $this->with("user:id,name")->find($id);
    $post->fill($postData)->save();
    $this->syncTags($post, $tags);
    $post = $this->with(["user:id,name", "tags:id,name"])->find($post->id);
    return $post;
  }

  public function deletePostById($id) {
    return $this->find($id)->destroy($id);
  }

  public function findByTag($tagName) {
    return $this->whereHas("tags", function ($query) use ($tagName) {
      $query->where("name", "LIKE", "%{$tagName}%");
    })->with(["user:id,name", "tags:id,name"])->latest()->paginate(self::PAGINATION_COUNT);
  }

  // 投稿とタグのリレーションを同期
  protected function syncTags($post, $tags) {
    $tagIds = [];
    foreach ($tags as $tagName) {
      $tag = Tag::firstOrCreate(["name" => $tagName]);
      $tagIds[] = $tag->id;
    }
    $post->tags()->sync($tagIds);
  }
}
