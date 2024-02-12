<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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
    $post = $this->find($id);
    if ($post->image) {
      $this->deleteImageFromS3($post->image);
    }
    return $post->destroy($id);
  }

  public function findByTag($tagName) {
    return $this->whereHas("tags", function ($query) use ($tagName) {
      $query->where("name", "LIKE", "%{$tagName}%");
    })->with(["user:id,name", "tags:id,name"])->latest()->paginate(self::PAGINATION_COUNT);
  }

  public function uploadImageToS3($image) {
    $path = $image->store('images', 's3');
    return Storage::disk('s3')->url($path);
}

  public function deleteImageFromS3($imageUrl) {
      // 画像のURLからファイルパスを抽出
    $imagePath = parse_url($imageUrl, PHP_URL_PATH);
    Storage::disk("s3")->delete($imagePath);}

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
