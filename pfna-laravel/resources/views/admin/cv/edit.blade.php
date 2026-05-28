@extends('layouts.admin', ['title' => 'File CV'])

@section('content')
    <div class="admin-header">
        <h2>File CV</h2>
        <a href="{{ $settings['cv_url'] ?? '/site/CV.pdf' }}" target="_blank">Xem file hiện tại</a>
    </div>

    <form class="post-form" method="POST" action="{{ route('admin.cv.update') }}" enctype="multipart/form-data">
        @csrf
        <p>File hiện tại: <strong>{{ $settings['cv_original_name'] ?? 'CV.pdf' }}</strong></p>
        <p>Đường dẫn: <a href="{{ $settings['cv_url'] ?? '/site/CV.pdf' }}" target="_blank">{{ $settings['cv_url'] ?? '/site/CV.pdf' }}</a></p>

        <label>Chọn file CV mới</label>
        <input type="file" name="cv_file" accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" required>
        <p class="login-hint">Hỗ trợ PDF, DOC, DOCX. Tối đa 50MB.</p>

        @if($errors->any())
            <div class="admin-alert admin-alert-error">{{ $errors->first() }}</div>
        @endif

        <div class="admin-form-actions">
            <button class="admin-primary" type="submit">Lưu file CV</button>
        </div>
    </form>
@endsection
