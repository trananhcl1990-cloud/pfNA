@extends('layouts.admin', ['title' => 'Thành tích'])

@section('content')
    <div class="admin-header">
        <h2>Thành tích</h2>
        <a class="admin-primary" href="{{ route('admin.achievements.create') }}">Thêm thành tích</a>
    </div>

    <div class="admin-table">
        @forelse($achievements as $achievement)
            <div class="admin-row">
                <div>
                    <strong>{{ $achievement['date'] }} - {{ $achievement['title_vi'] }}</strong>
                    <span>{{ $achievement['organization_vi'] ?? '' }} · Thứ tự {{ $achievement['sort_order'] }}</span>
                </div>
                <div class="admin-actions">
                    <a href="{{ route('admin.achievements.edit', $achievement['id']) }}">Sửa</a>
                    <form method="POST" action="{{ route('admin.achievements.destroy', $achievement['id']) }}" onsubmit="return confirm('Xóa thành tích này?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Xóa</button>
                    </form>
                </div>
            </div>
        @empty
            <p>Chưa có thành tích nào.</p>
        @endforelse
    </div>
@endsection
