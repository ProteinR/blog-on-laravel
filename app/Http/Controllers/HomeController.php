<?php

namespace App\Http\Controllers;

use App\Category;
use App\Post;
use App\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index() {
        $posts = Post::paginate(2);
        return view('pages.index', compact('posts'));
    }

    public function show($slug) {
        $post = Post::where('slug', $slug)->firstOrFail();
        return view('pages.show', compact('post'));
    }

    public function tag($slug) {
        $tag = Tag::where('slug', $slug)->firstOrFail();
        $posts = $tag->posts()->where('status', 1)->paginate(2);
        return view('pages.list', compact('posts'));
    }

    public function category($slug) {
        $category = Category::where('slug', $slug)->firstOrFail();
        $posts = $category->posts()->where('status', 1)->paginate(2);
//        dd($posts);
        return view('pages.list', compact('posts'));
    }
}
