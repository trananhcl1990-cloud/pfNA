@extends('layouts.admin', ['title' => $post ? 'Sửa bài viết' : 'Tạo bài viết'])

@section('content')
    <div class="admin-header">
        <h2>{{ $post ? 'Sửa bài viết' : 'Tạo bài viết' }}</h2>
        <a href="{{ route('admin.posts.index') }}">← Danh sách</a>
    </div>
    <form class="post-form" method="POST" action="{{ $post ? route('admin.posts.update', $post['id']) : route('admin.posts.store') }}">
        @csrf
        @if($post) @method('PUT') @endif
        <label>Tiêu đề</label>
        <input name="title" value="{{ old('title', $post['title'] ?? '') }}" required>
        <label>Slug URL</label>
        <input name="slug" value="{{ old('slug', $post['slug'] ?? '') }}" placeholder="tu-dong-neu-de-trong">
        <label>Mô tả ngắn</label>
        <textarea name="excerpt" rows="3">{{ old('excerpt', $post['excerpt'] ?? '') }}</textarea>
        <label>Ảnh đại diện URL</label>
        <div class="media-line">
            <input id="coverImage" name="cover_image" value="{{ old('cover_image', $post['cover_image'] ?? '') }}" placeholder="/uploads/media/...">
            <button type="button" id="uploadCover">Upload</button>
        </div>
        <label>Trạng thái</label>
        <select name="status">
            <option value="draft" @selected(old('status', $post['status'] ?? 'draft') === 'draft')>Bản nháp</option>
            <option value="published" @selected(old('status', $post['status'] ?? '') === 'published')>Xuất bản</option>
        </select>
        <label>Nội dung bài viết</label>
        <div class="word-toolbar">
            <button type="button" data-command="bold">B</button>
            <button type="button" data-command="italic"><i>I</i></button>
            <button type="button" data-command="underline"><u>U</u></button>
            <button type="button" data-block="h2">H2</button>
            <button type="button" data-block="h3">H3</button>
            <button type="button" data-command="insertUnorderedList">• List</button>
            <button type="button" data-command="insertOrderedList">1. List</button>
            <button type="button" data-command="justifyLeft">Trái</button>
            <button type="button" data-command="justifyCenter">Giữa</button>
            <button type="button" data-command="justifyRight">Phải</button>
            <button type="button" data-command="createLink">Link</button>
            <button type="button" id="insertImage">Ảnh</button>
        </div>
        <div id="postEditor" class="word-editor" contenteditable="true">{!! old('content', $post['content'] ?? '') !!}</div>
        <textarea id="postContent" name="content" hidden></textarea>
        <div class="admin-form-actions">
            <button class="admin-primary" type="submit">Lưu bài viết</button>
        </div>
    </form>
    <input type="file" id="postUploadInput" hidden accept="image/*">
    <script>
        window.cmsRoutes = {
            upload: @json(route('admin.upload')),
            csrf: @json(csrf_token()),
        };
    </script>
    <script src="/cms-admin-assets/post-editor.js?v=2"></script>
@endsection
