<?php

namespace App\Http\Controllers;

use App\Like;
use App\Post;
use App\Tag;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Auth;
use Reflector;

class PostController extends Controller
{
    //
    public function getIndex (Store $session) {
        $posts = Post::orderBy('created_at', 'desc')->paginate(2);
        return view('blog.index', ['posts' => $posts]);
    }

    public function getAdminIndex(Store $session) {
        $posts = Post::orderBy('title', 'asc')->get();
        return view('admin.index', ['posts' => $posts]);
    }

    public function getPost ($id) {
        $post = Post::where('id', '=', $id)->first();
        return view('blog.post', ['post' => $post]);
    }

    public function getLikePost ($id) {
        $post = Post::where('id', '=', $id)->first();
        $like = new Like();
        $post->likes()->save($like);
        return redirect()->back();
    }

    public function getAdminEdit ($id) {
        $post = Post::find($id);
        if (Gate::denies('update-post', $post)) {
            return redirect()->back();
        }
        $tags = Tag::all();
        return view('admin.edit', ['post' => $post, 'postId' => $id, 'tags' => $tags]);
    }
    public function getAdminDelete ($id) {
        $post = Post::find($id);
        if (Gate::denies('update-post', $post)) {
            return redirect()->back();
        }
        $post->likes()->delete();
        $post->tags()->detach();
        $post->delete();
        return redirect()->route('admin.index')
        ->with('info', 'Post deleted, Title is: ' . $post->title);
    }

    public function getAdminCreate() {
        $tags = Tag::all();
        return view('admin.create', ['tags' => $tags]);
    }

    public function postAdminCreate(Store $session, Request $request) {

        $this->validate($request, [
            'title' => 'required|min:6',
            'content' => 'required|min:10',
        ]);
        $user = Auth::user();
        // dd($user->id);

        $post = new Post([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'user_id' => $user->id
        ]);

        $post->save();
        $post->tags()->attach($request->input('tags') === null ? [] : $request->input('tags'));
        return redirect()->route('admin.index')->with('info', 'Post create, Title is: ' . $request->input('title'));
    }

    public function postAdminUpdate(Request $request) {
        $this->validate($request, [
            'title' => 'required|min:6',
            'content' => 'required|min:10',
        ]);

        $post = Post::find($request->input(('id')));
        if (Gate::denies('update-post', $post)) {
            return redirect()->back();
        }
        $post->title = $request->input('title');
        $post->content = $request->input('content');
        $post->save();
        // $post->tags()->detach();
        $post->tags()->sync($request->input('tags') === null ? [] : $request->input('tags'));
        return redirect()->route('admin.index')
            ->with('info', 'Post edited, Title is: ' . $request->input('title'));
    }
}
