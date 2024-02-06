<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
  public function index() {
    $posts = Post::with("user")->
      orderBy("created_at", "desc")->
      paginate(15);

    return response()->json($posts, Response::HTTP_OK);
  }

  public function show($id) {
    $post = Post::with("user")->find($id);

    if (!$post) {
      return response()->json(["message" => "投稿が見つかりませんでした。"], Response::HTTP_NOT_FOUND);
    }

    return response()->json($post, Response::HTTP_OK);
  }

  public function store(Request $request) {
    $request->validate([
      "title" => ["required", "string", "max:255"],
      "content" => ["required", "string", "max:1000"],
      "image" => ["nullable", "file", "image", "mimes:jpeg,png,jpg,webp", "max:2048"],
    ]);

    $postData = $request->only(["title", "content"]);
    $postData['user_id'] = Auth::id();

    // if ($request->hasFile("image")) {
    //   $path = $request->file("image")->store('public');
    //   $postData['image'] = Storage::url($path);
    // }

    $post = Post::create($postData);

    return response()->json($post, Response::HTTP_CREATED);
  }
}
