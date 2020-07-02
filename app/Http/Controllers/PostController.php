<?php

namespace App\Http\Controllers;
use App\Post;
use Illuminate\Http\Request;
use Illuminate\Session\Store;
class PostController extends Controller
{
    //
    public function getIndex (Store $session) {
        $post = new Post();
        $posts = $post->getPosts($session);
        return view('blog.index', ['posts' => $posts]);
    }

    public function getAdminIndex(Store $session) {
        $post = new Post();
        $posts = $post->getPosts($session);
        return view('admin.index', ['posts' => $posts]);
    }

    public function getPost (Store $session, $id) {
        $post = new Post();
        $post = $post->getPost($session, $id);
        return view('blog.post', ['post' => $post]);
    }

    public function getAdminEdit (Store $session, $id) {
        $post = new Post();
        $post = $post->getPost($session, $id);
        return view('admin.edit', ['post' => $post, 'postId' => $id]);
    }

    public function getAdminCreate() {
        return view('admin.create');
    }

    public function postAdminCreate(Store $session, Request $request) {

        $this->validate($request, [
            'title' => 'required|min:6',
            'content' => 'required|min:10',
        ]);

        $post = new Post();
        $post->addPost($session,
            $request->input('title'),
            $request->input('content')
        );
        return redirect()->route('admin.index')->with('info', 'Post create, Title is: ' . $request->input('title'));
    }

    public function postAdminUpdate(Store $session, Request $request) {
        $post = new Post();
        $post->editPost($session,
            $request->input('id'),
            $request->input('title'),
            $request->input('content')
        );
        return redirect()->route('admin.index')
            ->with('info', 'Post edited, Title is: ' . $request->input('title'));
    }
}
