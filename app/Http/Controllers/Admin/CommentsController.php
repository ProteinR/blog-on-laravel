<?php

namespace App\Http\Controllers\Admin;

use App\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CommentsController extends Controller
{
    public function index() {
        //->orderBy('id', 'desc')
        $comments = Comment::orderBy('id', 'desc')->get();
        return view('admin.comments.index', compact('comments'));
    }

    public function toggle($id) {
        $comment = Comment::find($id);
        $comment->toggleStatus();

        return redirect()->back()->with('status', 'Успешно');
    }

    public function destroy($id) {
        Comment::find($id)->delete();
        return redirect()->back()->with('status', 'Комментарий удалён');
    }
}
