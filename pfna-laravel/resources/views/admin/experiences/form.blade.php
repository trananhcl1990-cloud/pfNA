@extends('layouts.admin', ['title' => $experience ? 'Sửa kinh nghiệm' : 'Thêm kinh nghiệm'])

@php
    $skillsVi = implode("\n", $experience['skills_vi'] ?? []);
    $skillsEn = implode("\n", $experience['skills_en'] ?? []);
    $skillsZh = implode("\n", $experience['skills_zh'] ?? []);
@endphp

@section('content')
    <div class="admin-header">
        <h2>{{ $experience ? 'Sửa kinh nghiệm' : 'Thêm kinh nghiệm' }}</h2>
        <a href="{{ route('admin.experiences.index') }}">← Danh sách</a>
    </div>
    <form class="post-form" method="POST" action="{{ $experience ? route('admin.experiences.update', $experience['id']) : route('admin.experiences.store') }}">
        @csrf
        @if($experience) @method('PUT') @endif

        <div class="admin-form-grid">
            <div>
                <label>Thứ tự hiển thị</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $experience['sort_order'] ?? 10) }}" min="0" required>
            </div>
            <div>
                <label>Năm mốc</label>
                <input name="year" value="{{ old('year', $experience['year'] ?? '') }}" placeholder="2024">
            </div>
        </div>

        <label>Nhãn nổi bật</label>
        <div class="admin-form-grid admin-form-grid-3">
            <input name="badge_vi" value="{{ old('badge_vi', $experience['badge_vi'] ?? '') }}" placeholder="Hiện tại">
            <input name="badge_en" value="{{ old('badge_en', $experience['badge_en'] ?? '') }}" placeholder="Current">
            <input name="badge_zh" value="{{ old('badge_zh', $experience['badge_zh'] ?? '') }}" placeholder="当前">
        </div>

        <label>Thời gian làm việc</label>
        <div class="admin-form-grid admin-form-grid-3">
            <input name="date_vi" value="{{ old('date_vi', $experience['date_vi'] ?? '') }}" placeholder="03/2024 - Nay" required>
            <input name="date_en" value="{{ old('date_en', $experience['date_en'] ?? '') }}" placeholder="03/2024 - Present">
            <input name="date_zh" value="{{ old('date_zh', $experience['date_zh'] ?? '') }}" placeholder="03/2024 - 至今">
        </div>

        <label>Chức danh</label>
        <div class="admin-form-grid admin-form-grid-3">
            <input name="title_vi" value="{{ old('title_vi', $experience['title_vi'] ?? '') }}" placeholder="Lập trình viên Web" required>
            <input name="title_en" value="{{ old('title_en', $experience['title_en'] ?? '') }}" placeholder="Web Developer">
            <input name="title_zh" value="{{ old('title_zh', $experience['title_zh'] ?? '') }}" placeholder="网页开发者">
        </div>

        <label>Công ty / nơi làm việc</label>
        <div class="admin-form-grid admin-form-grid-3">
            <input name="company_vi" value="{{ old('company_vi', $experience['company_vi'] ?? '') }}" required>
            <input name="company_en" value="{{ old('company_en', $experience['company_en'] ?? '') }}">
            <input name="company_zh" value="{{ old('company_zh', $experience['company_zh'] ?? '') }}">
        </div>

        <label>Mô tả công việc</label>
        <div class="admin-form-grid admin-form-grid-3">
            <textarea name="description_vi" rows="5" required>{{ old('description_vi', $experience['description_vi'] ?? '') }}</textarea>
            <textarea name="description_en" rows="5">{{ old('description_en', $experience['description_en'] ?? '') }}</textarea>
            <textarea name="description_zh" rows="5">{{ old('description_zh', $experience['description_zh'] ?? '') }}</textarea>
        </div>

        <label>Kỹ năng / tag, mỗi dòng một mục</label>
        <div class="admin-form-grid admin-form-grid-3">
            <textarea name="skills_vi" rows="5" placeholder="Thiết kế Web&#10;Quản lý dự án">{{ old('skills_vi', $skillsVi) }}</textarea>
            <textarea name="skills_en" rows="5" placeholder="Web Design&#10;Project Management">{{ old('skills_en', $skillsEn) }}</textarea>
            <textarea name="skills_zh" rows="5" placeholder="网页设计&#10;项目管理">{{ old('skills_zh', $skillsZh) }}</textarea>
        </div>

        @if($errors->any())
            <div class="admin-alert admin-alert-error">{{ $errors->first() }}</div>
        @endif

        <div class="admin-form-actions">
            <button class="admin-primary" type="submit">Lưu kinh nghiệm</button>
        </div>
    </form>
@endsection
