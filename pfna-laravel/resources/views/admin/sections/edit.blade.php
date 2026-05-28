@extends('layouts.admin', ['title' => 'Nội dung các mục'])

@section('content')
    <div class="admin-header">
        <h2>Nội dung các mục</h2>
        <a href="{{ route('home') }}" target="_blank">Xem website</a>
    </div>

    <form class="post-form" method="POST" action="{{ route('admin.sections.update') }}">
        @csrf
        @method('PUT')

        <div class="admin-section-tabs">
            @foreach($sections as $id => $section)
                <a href="#section-{{ $id }}">{{ $section['label'] }}</a>
            @endforeach
        </div>

        @foreach($sections as $id => $section)
            <section class="admin-edit-section" id="section-{{ $id }}">
                <h3>{{ $section['label'] }}</h3>

                @forelse($section['items'] as $index => $item)
                    <div class="admin-translatable-item">
                        <div class="admin-item-title">
                            <strong>{{ strtoupper($item['tag']) }}</strong>
                            <span>{{ $item['label'] }}</span>
                        </div>
                        <div class="admin-form-grid admin-form-grid-3">
                            <div>
                                <label>Tiếng Việt</label>
                                <textarea name="items[{{ $id }}][{{ $index }}][vi]" rows="4">{{ old("items.$id.$index.vi", $item['vi']) }}</textarea>
                            </div>
                            <div>
                                <label>English</label>
                                <textarea name="items[{{ $id }}][{{ $index }}][en]" rows="4">{{ old("items.$id.$index.en", $item['en']) }}</textarea>
                            </div>
                            <div>
                                <label>中文</label>
                                <textarea name="items[{{ $id }}][{{ $index }}][zh]" rows="4">{{ old("items.$id.$index.zh", $item['zh']) }}</textarea>
                            </div>
                        </div>
                    </div>
                @empty
                    <p>Chưa tìm thấy nội dung có thể sửa trong mục này.</p>
                @endforelse
            </section>
        @endforeach

        @if($errors->any())
            <div class="admin-alert admin-alert-error">{{ $errors->first() }}</div>
        @endif

        <div class="admin-form-actions">
            <button class="admin-primary" type="submit">Lưu nội dung</button>
        </div>
    </form>
@endsection
