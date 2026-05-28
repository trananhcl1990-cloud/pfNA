<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập admin</title>
    <link rel="stylesheet" href="/cms-admin-assets/cms.css?v=4">
</head>
<body class="login-body">
    <form class="login-card" method="POST" action="{{ route('admin.login.submit') }}">
        @csrf
        <h1>Đăng nhập admin</h1>
        <label>Email</label>
        <input type="email" name="email" value="{{ old('email', env('ADMIN_EMAIL')) }}" required>
        <label>Mật khẩu</label>
        <input type="password" name="password" required>
        @error('email')<p class="form-error">{{ $message }}</p>@enderror
        <button type="submit">Đăng nhập</button>
        <p class="login-hint">Mặc định: admin@pfna.local / admin123456. Đổi trong file .env trước khi public website.</p>
    </form>
</body>
</html>
