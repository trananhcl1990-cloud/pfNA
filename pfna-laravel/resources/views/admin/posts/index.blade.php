@extends('layouts.admin', ['title' => 'Bài viết'])

@section('content')
    <div class="admin-header">
        <h2>Bài viết</h2>
        <a class="admin-primary" href="{{ route('admin.posts.create') }}">Tạo bài viết</a>
    </div>
    <div class="admin-table">
        @forelse($posts as $post)
            <div class="admin-row">
                <div>
                    <strong>{{ $post['title'] }}</strong>
                    <span>{{ $post['status'] === 'published' ? 'Đã xuất bản' : 'Bản nháp' }}</span>
                </div>
                <div class="admin-actions">
                    @if(($post['status'] ?? '') === 'published')
                        <a href="{{ route('blog.show', $post['slug']) }}" target="_blank">Xem</a>
                    @endif
                    <a href="{{ route('admin.posts.edit', $post['id']) }}">Sửa</a>
                    <form method="POST" action="{{ route('admin.posts.destroy', $post['id']) }}" onsubmit="return confirm('Xoá bài viết này?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Xoá</button>
                    </form>
                </div>
            </div>
        @empty
            <p>Chưa có bài viết.</p>
        @endforelse
    </div>
@endsection
