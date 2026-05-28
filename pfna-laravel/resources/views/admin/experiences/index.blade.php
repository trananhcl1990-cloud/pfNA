@extends('layouts.admin', ['title' => 'Kinh nghiệm làm việc'])

@section('content')
    <div class="admin-header">
        <h2>Kinh nghiệm làm việc</h2>
        <a class="admin-primary" href="{{ route('admin.experiences.create') }}">Thêm kinh nghiệm</a>
    </div>
    <div class="admin-table">
        @forelse($experiences as $experience)
            <div class="admin-row">
                <div>
                    <strong>{{ $experience['date_vi'] }} - {{ $experience['title_vi'] }}</strong>
                    <span>{{ $experience['company_vi'] }} · Thứ tự {{ $experience['sort_order'] }}</span>
                </div>
                <div class="admin-actions">
                    <a href="{{ route('admin.experiences.edit', $experience['id']) }}">Sửa</a>
                    <form method="POST" action="{{ route('admin.experiences.destroy', $experience['id']) }}" onsubmit="return confirm('Xoá kinh nghiệm này?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Xoá</button>
                    </form>
                </div>
            </div>
        @empty
            <p>Chưa có kinh nghiệm nào.</p>
        @endforelse
    </div>
@endsection
