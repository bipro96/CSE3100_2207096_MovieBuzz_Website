<a href="{{ route('movies.show', $movie->slug) }}" class="text-decoration-none">
    <div class="mb-movie-card">
        <div class="mb-poster-wrap">
            <img src="{{ $movie->posterUrl() }}" alt="{{ $movie->title }}" loading="lazy">
            @if($movie->vote_average)
                <span class="mb-rating-badge"><i class="fa-solid fa-star text-warning"></i> {{ number_format($movie->vote_average, 1) }}</span>
            @endif
        </div>
        <div class="pt-2">
            <h6 class="mb-1 text-truncate">{{ $movie->title }}</h6>
            <p class="small text-secondary mb-0">
                {{ $movie->genres->pluck('name')->take(2)->implode(', ') ?: 'Movie' }}
                @if($movie->release_date) &middot; {{ $movie->release_date->format('Y') }} @endif
            </p>
        </div>
    </div>
</a>
