<section id="achievements" class="section-container">
    <div class="section-header">
        <span class="section-number">03</span>
        <h2 class="section-title animate-on-scroll" data-vi="Thành tích &amp; Ghi nhận" data-en="Achievements &amp; Recognition" data-zh="成果与荣誉">Thành tích &amp; Ghi nhận</h2>
        <div class="section-line"></div>
    </div>

    <div class="achievements-container">
        <div class="achievements-timeline animate-on-scroll">
            <div class="timeline-line"></div>
            @foreach($achievements as $achievement)
                <div class="achievement-item animate-on-scroll">
                    <div class="timeline-dot">
                        <div class="dot-inner"></div>
                    </div>
                    <div class="achievement-content">
                        @if(!empty($achievement['badge']))
                            <div class="achievement-badge">{{ $achievement['badge'] }}</div>
                        @endif
                        @if(!empty($achievement['date']))
                            <div class="achievement-date">{{ $achievement['date'] }}</div>
                        @endif
                        <h3
                            data-vi="{{ $achievement['title_vi'] }}"
                            data-en="{{ $achievement['title_en'] ?: $achievement['title_vi'] }}"
                            data-zh="{{ $achievement['title_zh'] ?: $achievement['title_vi'] }}">{{ $achievement['title_vi'] }}</h3>
                        @if(!empty($achievement['organization_vi']))
                            <h4
                                data-vi="{{ $achievement['organization_vi'] }}"
                                data-en="{{ $achievement['organization_en'] ?: $achievement['organization_vi'] }}"
                                data-zh="{{ $achievement['organization_zh'] ?: $achievement['organization_vi'] }}">{{ $achievement['organization_vi'] }}</h4>
                        @endif
                        <p
                            data-vi="{{ $achievement['description_vi'] }}"
                            data-en="{{ $achievement['description_en'] ?: $achievement['description_vi'] }}"
                            data-zh="{{ $achievement['description_zh'] ?: $achievement['description_vi'] }}">{{ $achievement['description_vi'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
