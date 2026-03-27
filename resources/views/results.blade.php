@extends('layouts.app')

@section('title', 'Results — YouTube Course Scraper')

@section('content')
<div class="container py-4">

    {{-- ── Page Header ────────────────────────────────────── --}}
    <div class="page-header">
        <h1><i class="bi bi-collection-play me-2"></i>Discovered Playlists</h1>
        <p class="subtitle">Browse all collected educational YouTube playlists</p>
    </div>

    {{-- ── Stats Bar ──────────────────────────────────────── --}}
    <div class="stats-bar">
        <div class="d-flex flex-wrap gap-4">
            <div class="stat-item">
                <i class="bi bi-play-circle"></i>
                <span>Total Playlists: <span class="stat-value">{{ $totalCount }}</span></span>
            </div>
            <div class="stat-item">
                <i class="bi bi-tags"></i>
                <span>Categories: <span class="stat-value">{{ $categoryCounts->count() }}</span></span>
            </div>
        </div>
        <a href="{{ route('home') }}" class="btn btn-gradient btn-sm">
            <i class="bi bi-plus-lg"></i> Fetch More
        </a>
    </div>

    {{-- ── Category Filter Tabs ───────────────────────────── --}}
    @if($categoryCounts->count() > 0)
        <div class="filter-section">
            <div class="filter-tabs">
                {{-- All tab --}}
                <a href="{{ route('results') }}"
                   class="filter-tab {{ !$category ? 'active' : '' }}">
                    All
                    <span class="count-badge">{{ $totalCount }}</span>
                </a>

                {{-- Category tabs --}}
                @foreach($categoryCounts as $cat)
                    <a href="{{ route('results', ['category' => $cat->category]) }}"
                       class="filter-tab {{ $category === $cat->category ? 'active' : '' }}">
                        {{ ucfirst($cat->category) }}
                        <span class="count-badge">{{ $cat->count }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ── Playlist Cards Grid ────────────────────────────── --}}
    @if($playlists->count() > 0)
        <div class="row g-4">
            @foreach($playlists as $playlist)
                <div class="col-xl-3 col-lg-4 col-md-6 fade-in-up">
                    <div class="playlist-card">
                        {{-- Thumbnail --}}
                        <a href="https://www.youtube.com/playlist?list={{ $playlist->playlist_id }}"
                           target="_blank" rel="noopener noreferrer"
                           class="card-thumbnail d-block">
                            <img src="{{ $playlist->thumbnail }}"
                                 alt="{{ $playlist->title }}"
                                 loading="lazy">

                            {{-- Play overlay on hover --}}
                            <div class="play-overlay">
                                <i class="bi bi-play-circle-fill"></i>
                            </div>

                            {{-- Video count badge --}}
                            @if($playlist->video_count > 0)
                                <div class="video-count-badge">
                                    <i class="bi bi-collection-play-fill"></i>
                                    {{ $playlist->video_count }} videos
                                </div>
                            @endif
                        </a>

                        {{-- Card Body --}}
                        <div class="card-body">
                            {{-- Title --}}
                            <h5 class="card-title">
                                <a href="https://www.youtube.com/playlist?list={{ $playlist->playlist_id }}"
                                   target="_blank" rel="noopener noreferrer"
                                   title="{{ $playlist->title }}">
                                    {{ $playlist->title }}
                                </a>
                            </h5>

                            {{-- Category Badge --}}
                            <div class="category-badge">
                                <i class="bi bi-tag-fill"></i>
                                {{ $playlist->category }}
                            </div>

                            {{-- Channel Info --}}
                            <div class="channel-info">
                                <div class="channel-avatar">
                                    {{ strtoupper(substr($playlist->channel_name, 0, 1)) }}
                                </div>
                                <span class="channel-name" title="{{ $playlist->channel_name }}">
                                    {{ $playlist->channel_name }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ── Pagination ─────────────────────────────────── --}}
        @if($playlists->hasPages())
            <div class="pagination-custom d-flex justify-content-center mt-5">
                {{ $playlists->links() }}
            </div>
        @endif

    @else
        {{-- ── Empty State ────────────────────────────────── --}}
        <div class="empty-state">
            <div class="empty-icon">
                <i class="bi bi-inbox"></i>
            </div>
            <h3>No playlists found</h3>
            <p>Start by entering some categories on the home page to fetch YouTube playlists.</p>
            <a href="{{ route('home') }}" class="btn btn-gradient mt-3">
                <i class="bi bi-search me-1"></i>
                Start Fetching
            </a>
        </div>
    @endif

</div>
@endsection
