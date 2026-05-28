<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin' }} - pfNA CMS</title>
    <link rel="stylesheet" href="/cms-admin-assets/cms.css?v=9">
</head>
<body class="admin-body">
    <aside class="admin-sidebar">
        <h1>pfNA CMS</h1>
        <nav>
            <a href="{{ route('admin.dashboard') }}">Tổng quan</a>
            <a href="{{ route('admin.sections.edit') }}">Nội dung các mục</a>
            <a href="{{ route('admin.page.edit') }}">Sửa trực tiếp website</a>
            <a href="{{ route('admin.cv.edit') }}">File CV</a>
            <a href="{{ route('admin.experiences.index') }}">Kinh nghiệm làm việc</a>
            <a href="{{ route('admin.achievements.index') }}">Thành tích</a>
            <a href="{{ route('admin.posts.index') }}">Bài viết</a>
            <a href="{{ route('home') }}" target="_blank">Xem website</a>
        </nav>
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit">Đăng xuất</button>
        </form>
    </aside>
    <main class="admin-main">
        @if(session('status'))
            <div class="admin-alert">{{ session('status') }}</div>
        @endif
        @yield('content')
    </main>
</body>
</html>
