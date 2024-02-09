<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PostRequest;
use App\Http\Requests\SearchPostsByTagRequest;
// use Illuminate\Support\Facades\Storage;

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

    // ローカルストレージに画像を保存するとCORSエラーが発生するため、一旦コメントアウト
    // if ($request->hasFile("image")) {
    //   $path = $request->file("image")->store('public');
    //   $postData['image'] = Storage::url($path);
    // }

    $tags = $postData["tags"];
    $post = $this->post->createPost($postData, $tags);
    return response()->json($post, Response::HTTP_CREATED);
  }

  public function update(PostRequest $request, $id) {
    $postData = $request->validated();
    $post = $this->post->updatePostById($id, $postData);
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
      return response()->json(["message" => "投稿が見つかりませんでした。"], Response::HTTP_OK);
    }

    return response()->json($posts, Response::HTTP_OK);
  }
}
