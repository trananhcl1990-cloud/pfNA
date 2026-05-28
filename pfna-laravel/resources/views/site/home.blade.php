@extends('layouts.site', ['title' => 'Nguyễn Gia Huy'])

@section('content')
    {!! $pageHtml !!}

    @if(count($posts))
        <section id="blog" class="section-container cms-blog-section">
            <div class="section-header">
                <span class="section-number">09</span>
                <h2 class="section-title">Bài viết</h2>
                <div class="section-line"></div>
            </div>
            <div class="cms-post-grid">
                @foreach($posts as $post)
                    <article class="cms-post-card">
                        @if(!empty($post['cover_image']))
                            <a href="{{ route('blog.show', $post['slug']) }}"><img src="{{ $post['cover_image'] }}" alt="{{ $post['title'] }}"></a>
                        @endif
                        <div>
                            <h3><a href="{{ route('blog.show', $post['slug']) }}">{{ $post['title'] }}</a></h3>
                            @if(!empty($post['excerpt']))
                                <p>{{ $post['excerpt'] }}</p>
                            @endif
                            <a class="cms-read-more" href="{{ route('blog.show', $post['slug']) }}">Đọc bài viết</a>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif
@endsection
