@extends('layouts.admin', ['title' => 'Tổng quan'])

@section('content')
    <div class="admin-header">
        <h2>Tổng quan</h2>
        <a class="admin-primary" href="{{ route('admin.page.edit') }}">Sửa website trực tiếp</a>
    </div>
    <div class="admin-stats">
        <div><strong>{{ count($posts) }}</strong><span>Bài viết</span></div>
        <div><strong>{{ count(array_filter($posts, fn($post) => ($post['status'] ?? '') === 'published')) }}</strong><span>Đã xuất bản</span></div>
        <div><strong>{{ count($experiences) }}</strong><span>Kinh nghiệm</span></div>
    </div>
    <section class="admin-panel">
        <h3>Thao tác nhanh</h3>
        <p>Chọn “Sửa website trực tiếp” để bấm vào chữ, ảnh hoặc video trên trang và chỉnh ngay tại giao diện.</p>
        <p>Chọn “Kinh nghiệm làm việc” để thêm/sửa/xoá timeline và chỉnh thời gian làm việc.</p>
        <p>Chọn “Bài viết” để viết bài với trình soạn thảo có định dạng tiêu đề, danh sách, căn lề, link và ảnh.</p>
    </section>
@endsection
