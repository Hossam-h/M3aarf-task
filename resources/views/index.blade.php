@extends('layouts.app')

@section('title', 'YouTube Course Scraper — Home')

@section('content')
<div class="container">

    {{-- ── Hero Section ───────────────────────────────────── --}}
    <section class="hero-section">
        <div class="hero-badge">
            <i class="bi bi-circle-fill"></i>
            AI-Powered YouTube Playlist Discovery
        </div>

        <h1>جمع الدورات التعليمية من يوتيوب</h1>

        <p class="hero-subtitle">
            Enter your learning categories below — our AI will generate smart search
            queries and find the best educational playlists on YouTube for you.
        </p>
    </section>

    {{-- ── Input Form Card ────────────────────────────────── --}}
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
            <div class="glass-card">
                <form action="{{ route('fetch') }}" method="POST" id="fetchForm">
                    @csrf

                    {{-- Categories Textarea --}}
                    <div class="mb-4">
                        <label for="categories" class="form-label-custom">
                            <i class="bi bi-tags-fill"></i>
                            Categories (one per line)
                        </label>
                        <textarea
                            class="form-control form-control-dark"
                            id="categories"
                            name="categories"
                            rows="8"
                            placeholder="Enter categories, one per line. For example:&#10;&#10;Python Programming&#10;Web Development&#10;Machine Learning&#10;Data Science&#10;Flutter Mobile Development&#10;Cyber Security"
                            required
                        >{{ old('categories') }}</textarea>
                        <div class="form-text text-muted mt-2" style="color: var(--text-muted) !important;">
                            <i class="bi bi-info-circle me-1"></i>
                            Each line will be processed separately. The AI will generate search queries for each category.
                        </div>
                    </div>

                    {{-- Feature Info --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="d-flex align-items-center gap-2" style="color: var(--text-secondary); font-size: 0.85rem;">
                                <i class="bi bi-robot text-info"></i>
                                <span>AI-Generated Queries</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center gap-2" style="color: var(--text-secondary); font-size: 0.85rem;">
                                <i class="bi bi-youtube text-danger"></i>
                                <span>YouTube Playlist Search</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center gap-2" style="color: var(--text-secondary); font-size: 0.85rem;">
                                <i class="bi bi-shield-check text-success"></i>
                                <span>Auto Deduplication</span>
                            </div>
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <div class="d-flex flex-wrap gap-3 align-items-center">
                        <button type="submit" class="btn btn-gradient" id="fetchBtn">
                            <i class="bi bi-search"></i>
                            Start Fetching
                        </button>
                        <a href="{{ route('results') }}" class="btn btn-outline-glass">
                            <i class="bi bi-collection-play me-1"></i>
                            View Results
                        </a>
                    </div>
                </form>
            </div>

            {{-- ── How It Works ───────────────────────────── --}}
            <div class="glass-card mt-4">
                <h5 class="mb-3" style="color: var(--text-primary); font-weight: 700;">
                    <i class="bi bi-lightning-charge-fill me-2" style="color: var(--warning);"></i>
                    How It Works
                </h5>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="text-center p-3">
                            <div class="mb-2" style="font-size: 2rem; color: var(--accent-primary);">
                                <i class="bi bi-1-circle-fill"></i>
                            </div>
                            <h6 style="color: var(--text-primary);">Enter Categories</h6>
                            <p style="color: var(--text-muted); font-size: 0.82rem; margin: 0;">
                                Type your learning topics, one per line
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center p-3">
                            <div class="mb-2" style="font-size: 2rem; color: var(--accent-secondary);">
                                <i class="bi bi-2-circle-fill"></i>
                            </div>
                            <h6 style="color: var(--text-primary);">AI Generates Queries</h6>
                            <p style="color: var(--text-muted); font-size: 0.82rem; margin: 0;">
                                OpenAI creates smart search terms for each category
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center p-3">
                            <div class="mb-2" style="font-size: 2rem; color: var(--info);">
                                <i class="bi bi-3-circle-fill"></i>
                            </div>
                            <h6 style="color: var(--text-primary);">Discover Playlists</h6>
                            <p style="color: var(--text-muted); font-size: 0.82rem; margin: 0;">
                                YouTube playlists are found, deduplicated & stored
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
