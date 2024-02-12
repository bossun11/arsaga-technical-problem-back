<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PostRequest;
use App\Http\Requests\SearchPostsByTagRequest;

class PostController extends Controller
{
  private $post;

  public function __construct() {
    $this->post = new Post();
  }

  public function index() {
    $posts = $this->post->getAllPosts();
    return response()->json($posts, Response::HTTP_OK);
  }

  public function show($id) {
    $post = $this->post->getPostById($id);
    if (!$post) {
      return response()->json(["message" => "投稿が見つかりませんでした。"], Response::HTTP_NOT_FOUND);
    }

    return response()->json($post, Response::HTTP_OK);
  }

  public function store(PostRequest $request) {
    $postData = $request->validated();
    $postData["user_id"] = Auth::id();
    $tags = $postData["tags"] ?? [];

    if ($request->hasFile("image")) {
      $postData["image"] = $this->post->uploadImageToS3($request->file("image"));
    }

    $post = $this->post->createPost($postData, $tags);
    return response()->json($post, Response::HTTP_CREATED);
  }

  public function update(PostRequest $request, $id) {
    $postData = $request->validated();
    $post = $this->post->getPostById($id);
    $tags = $postData["tags"] ?? [];

    if ($request->hasFile('image')) {
      $post->deleteImageFromS3($post->image);
      $postData['image'] = $this->post->uploadImageToS3($request->file('image'));
    }

    $post = $this->post->updatePostById($id, $postData, $tags);
    return response()->json($post, Response::HTTP_OK);
  }

  public function destroy($id) {
    $this->post->deletePostById($id);
    return response()->json(Response::HTTP_NO_CONTENT);
  }

  public function searchByTag(SearchPostsByTagRequest $request) {
    $tagName = $request->input("tag_name");
    $posts = $this->post->findByTag($tagName);
    if ($posts->isEmpty()) {
      return response()->json(["data" => []], Response::HTTP_OK);
    }

    return response()->json($posts, Response::HTTP_OK);
  }
}
