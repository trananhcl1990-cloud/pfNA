@extends('layouts.site', ['title' => $post['title']])

@section('content')
    <main class="cms-post-page">
        <article class="cms-post-article">
            <a href="{{ route('blog.index') }}">← Tất cả bài viết</a>
            @if(!empty($post['cover_image']))
                <img class="cms-post-cover" src="{{ $post['cover_image'] }}" alt="{{ $post['title'] }}">
            @endif
            <h1>{{ $post['title'] }}</h1>
            @if(!empty($post['excerpt']))
                <p class="cms-post-excerpt">{{ $post['excerpt'] }}</p>
            @endif
            <div class="cms-rich-content">{!! $post['content'] ?? '' !!}</div>
        </article>
    </main>
@endsection
