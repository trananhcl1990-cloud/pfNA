@extends('layouts.admin', ['title' => $achievement ? 'Sửa thành tích' : 'Thêm thành tích'])

@section('content')
    <div class="admin-header">
        <h2>{{ $achievement ? 'Sửa thành tích' : 'Thêm thành tích' }}</h2>
        <a href="{{ route('admin.achievements.index') }}">← Danh sách</a>
    </div>

    <form class="post-form" method="POST" action="{{ $achievement ? route('admin.achievements.update', $achievement['id']) : route('admin.achievements.store') }}">
        @csrf
        @if($achievement) @method('PUT') @endif

        <div class="admin-form-grid admin-form-grid-3">
            <div>
                <label>Thứ tự hiển thị</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $achievement['sort_order'] ?? 10) }}" min="0" required>
            </div>
            <div>
                <label>Nhãn</label>
                <input name="badge" value="{{ old('badge', $achievement['badge'] ?? '') }}" placeholder="2025, Excellence...">
            </div>
            <div>
                <label>Thời gian / năm</label>
                <input name="date" value="{{ old('date', $achievement['date'] ?? '') }}" placeholder="2025">
            </div>
        </div>

        <label>Tên thành tích</label>
        <div class="admin-form-grid admin-form-grid-3">
            <input name="title_vi" value="{{ old('title_vi', $achievement['title_vi'] ?? '') }}" placeholder="Tiếng Việt" required>
            <input name="title_en" value="{{ old('title_en', $achievement['title_en'] ?? '') }}" placeholder="English">
            <input name="title_zh" value="{{ old('title_zh', $achievement['title_zh'] ?? '') }}" placeholder="中文">
        </div>

        <label>Tổ chức / đơn vị</label>
        <div class="admin-form-grid admin-form-grid-3">
            <input name="organization_vi" value="{{ old('organization_vi', $achievement['organization_vi'] ?? '') }}" placeholder="Tiếng Việt">
            <input name="organization_en" value="{{ old('organization_en', $achievement['organization_en'] ?? '') }}" placeholder="English">
            <input name="organization_zh" value="{{ old('organization_zh', $achievement['organization_zh'] ?? '') }}" placeholder="中文">
        </div>

        <label>Mô tả</label>
        <div class="admin-form-grid admin-form-grid-3">
            <textarea name="description_vi" rows="6" placeholder="Mô tả tiếng Việt" required>{{ old('description_vi', $achievement['description_vi'] ?? '') }}</textarea>
            <textarea name="description_en" rows="6" placeholder="English description">{{ old('description_en', $achievement['description_en'] ?? '') }}</textarea>
            <textarea name="description_zh" rows="6" placeholder="中文描述">{{ old('description_zh', $achievement['description_zh'] ?? '') }}</textarea>
        </div>

        @if($errors->any())
            <div class="admin-alert admin-alert-error">{{ $errors->first() }}</div>
        @endif

        <div class="admin-form-actions">
            <button class="admin-primary" type="submit">Lưu thành tích</button>
        </div>
    </form>
@endsection
