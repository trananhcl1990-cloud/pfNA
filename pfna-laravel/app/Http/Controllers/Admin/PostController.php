<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\JsonCmsStore;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(JsonCmsStore $cms)
    {
        return view('admin.posts.index', ['posts' => $cms->posts()]);
    }

    public function create()
    {
        return view('admin.posts.form', ['post' => null]);
    }

    public function store(Request $request, JsonCmsStore $cms)
    {
        $post = $cms->savePost($this->validated($request));

        return redirect()->route('admin.posts.edit', $post['id'])->with('status', 'Đã tạo bài viết.');
    }

    public function edit(string $id, JsonCmsStore $cms)
    {
        $post = $cms->findPost($id);
        abort_if(! $post, 404);

        return view('admin.posts.form', compact('post'));
    }

    public function update(Request $request, string $id, JsonCmsStore $cms)
    {
        $cms->savePost($this->validated($request), $id);

        return back()->with('status', 'Đã lưu bài viết.');
    }

    public function destroy(string $id, JsonCmsStore $cms)
    {
        $cms->deletePost($id);

        return redirect()->route('admin.posts.index')->with('status', 'Đã xoá bài viết.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'slug' => ['nullable', 'string', 'max:180'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['nullable', 'string'],
            'cover_image' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'in:draft,published'],
        ]);
    }
}
