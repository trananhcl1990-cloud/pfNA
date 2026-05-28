<section id="experience" class="section-container dark-section">
    <div class="section-header">
        <span class="section-number">02</span>
        <h2 class="section-title animate-on-scroll" data-vi="Kinh nghiệm làm việc" data-en="Work Experience" data-zh="工作经历">Kinh nghiệm làm việc</h2>
        <div class="section-line"></div>
    </div>
    <div class="experience-timeline">
        <div class="timeline-line"></div>
        @foreach($experiences as $experience)
            @php
                $skillsVi = $experience['skills_vi'] ?? [];
                $skillsEn = $experience['skills_en'] ?? $skillsVi;
                $skillsZh = $experience['skills_zh'] ?? $skillsVi;
            @endphp
            <div class="timeline-item animate-on-scroll" data-date="{{ $experience['year'] ?? '' }}">
                <div class="timeline-dot"><div class="dot-inner"></div></div>
                <div class="timeline-content">
                    @if(!empty($experience['badge_vi']))
                        <div class="timeline-badge"
                            data-vi="{{ $experience['badge_vi'] }}"
                            data-en="{{ $experience['badge_en'] ?: $experience['badge_vi'] }}"
                            data-zh="{{ $experience['badge_zh'] ?: $experience['badge_vi'] }}">{{ $experience['badge_vi'] }}</div>
                    @endif
                    <div class="timeline-date"
                        data-vi="{{ $experience['date_vi'] }}"
                        data-en="{{ $experience['date_en'] ?: $experience['date_vi'] }}"
                        data-zh="{{ $experience['date_zh'] ?: $experience['date_vi'] }}">{{ $experience['date_vi'] }}</div>
                    <h3
                        data-vi="{{ $experience['title_vi'] }}"
                        data-en="{{ $experience['title_en'] ?: $experience['title_vi'] }}"
                        data-zh="{{ $experience['title_zh'] ?: $experience['title_vi'] }}">{{ $experience['title_vi'] }}</h3>
                    <h4
                        data-vi="{{ $experience['company_vi'] }}"
                        data-en="{{ $experience['company_en'] ?: $experience['company_vi'] }}"
                        data-zh="{{ $experience['company_zh'] ?: $experience['company_vi'] }}">{{ $experience['company_vi'] }}</h4>
                    <p
                        data-vi="{{ $experience['description_vi'] }}"
                        data-en="{{ $experience['description_en'] ?: $experience['description_vi'] }}"
                        data-zh="{{ $experience['description_zh'] ?: $experience['description_vi'] }}">{{ $experience['description_vi'] }}</p>
                    @if($skillsVi !== [])
                        <div class="timeline-skills">
                            @foreach($skillsVi as $index => $skill)
                                <span
                                    data-vi="{{ $skill }}"
                                    data-en="{{ $skillsEn[$index] ?? $skill }}"
                                    data-zh="{{ $skillsZh[$index] ?? $skill }}">{{ $skill }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</section>
