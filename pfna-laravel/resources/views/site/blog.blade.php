@extends('layouts.site', ['title' => 'Bài viết'])

@section('content')
    <main class="cms-blog-page">
        <header class="cms-blog-hero">
            <a href="{{ route('home') }}">← Về trang chủ</a>
            <h1>Bài viết</h1>
        </header>
        <section class="cms-post-grid">
            @forelse($posts as $post)
                <article class="cms-post-card">
                    @if(!empty($post['cover_image']))
                        <a href="{{ route('blog.show', $post['slug']) }}"><img src="{{ $post['cover_image'] }}" alt="{{ $post['title'] }}"></a>
                    @endif
                    <div>
                        <h2><a href="{{ route('blog.show', $post['slug']) }}">{{ $post['title'] }}</a></h2>
                        <p>{{ $post['excerpt'] ?? '' }}</p>
                        <a class="cms-read-more" href="{{ route('blog.show', $post['slug']) }}">Đọc bài viết</a>
                    </div>
                </article>
            @empty
                <p>Chưa có bài viết nào.</p>
            @endforelse
        </section>
    </main>
@endsection
