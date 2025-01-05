<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
 public function index(Request $request)
 {
     $posts = Post::all();
     return view('Backend/blog');
 }
}
