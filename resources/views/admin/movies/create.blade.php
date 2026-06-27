@extends('layouts.admin')

@section('title', 'Add Movie')
@section('page-title', 'Add Movie')

@section('content')

<div class="admin-panel-box p-4">

    <div class="mb-4 p-3 border rounded" style="border-color:#333 !important;">
        <label class="form-label fw-bold">Fetch from TMDb</label>
        <div class="d-flex gap-2 position-relative">
            <input type="text" id="tmdbQuery" class="form-control" placeholder="Type a movie title, e.g. Inception">
            <button type="button" id="tmdbSearchBtn" class="btn btn-outline-warning text-nowrap">
                <i class="fa-solid fa-magnifying-glass"></i> Search TMDb
            </button>
            <div id="tmdbResults" class="tmdb-results-dropdown d-none"></div>
        </div>
        <div id="tmdbStatus" class="small text-secondary mt-2"></div>
    </div>

    <form method="POST" action="{{ route('admin.movies.store') }}" id="movieForm">
        @csrf
        <input type="hidden" name="tmdb_id" id="field_tmdb_id">
        <input type="hidden" name="poster_path" id="field_poster_path">
        <input type="hidden" name="backdrop_path" id="field_backdrop_path">
        <input type="hidden" name="production_countries" id="field_production_countries">
        <div id="genreNamesContainer"></div>

        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label">Title <span class="text-danger">*</span></label>
                <input type="text" name="title" id="field_title" class="form-control" required value="{{ old('title') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Original Title</label>
                <input type="text" name="original_title" id="field_original_title" class="form-control" value="{{ old('original_title') }}">
            </div>

            <div class="col-12">
                <label class="form-label">Overview</label>
                <textarea name="overview" id="field_overview" rows="3" class="form-control">{{ old('overview') }}</textarea>
            </div>

            <div class="col-md-3">
                <label class="form-label">Release Date</label>
                <input type="date" name="release_date" id="field_release_date" class="form-control" value="{{ old('release_date') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Runtime (min)</label>
                <input type="number" name="runtime" id="field_runtime" class="form-control" value="{{ old('runtime') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Language</label>
                <input type="text" name="language" id="field_language" class="form-control" value="{{ old('language') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Original Language</label>
                <input type="text" name="original_language" id="field_original_language" class="form-control" value="{{ old('original_language') }}">
            </div>

            <div class="col-md-3">
                <label class="form-label">Popularity</label>
                <input type="number" step="0.001" name="popularity" id="field_popularity" class="form-control" value="{{ old('popularity') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Vote Average</label>
                <input type="number" step="0.01" name="vote_average" id="field_vote_average" class="form-control" value="{{ old('vote_average') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Vote Count</label>
                <input type="number" name="vote_count" id="field_vote_count" class="form-control" value="{{ old('vote_count') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">TMDb Status</label>
                <input type="text" name="status" id="field_status" class="form-control" value="{{ old('status') }}">
            </div>

            <div class="col-md-4">
                <label class="form-label d-block">Adult Content</label>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="adult" id="field_adult" value="1">
                </div>
            </div>

            <div class="col-md-4">
                <label class="form-label">Listing Status <span class="text-danger">*</span></label>
                <select name="listing_status" class="form-select" required>
                    <option value="upcoming">Upcoming</option>
                    <option value="now_showing">Now Showing</option>
                    <option value="archived">Archived</option>
                </select>
            </div>

            <div class="col-md-4 d-flex align-items-end gap-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="isFeatured">
                    <label class="form-check-label" for="isFeatured">Featured</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" checked>
                    <label class="form-check-label" for="isActive">Active</label>
                </div>
            </div>

            <div class="col-12">
                <label class="form-label">Genres</label>
                <div id="genreCheckboxes" class="d-flex flex-wrap gap-3">
                    @foreach($genres as $genre)
                        <div class="form-check">
                            <input class="form-check-input genre-checkbox" type="checkbox" name="genre_ids[]" value="{{ $genre->id }}" id="genre_{{ $genre->id }}">
                            <label class="form-check-label" for="genre_{{ $genre->id }}">{{ $genre->name }}</label>
                        </div>
                    @endforeach
                </div>
                <div id="newGenresNote" class="small text-warning mt-2"></div>
            </div>

            <div class="col-md-6">
                <label class="form-label">Poster Preview</label>
                <div><img id="posterPreview" src="{{ asset('images/poster-placeholder.png') }}" style="width:150px;height:220px;object-fit:cover;border-radius:6px" class="border"></div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Backdrop Preview</label>
                <div><img id="backdropPreview" src="{{ asset('images/backdrop-placeholder.png') }}" style="width:100%;max-width:320px;height:120px;object-fit:cover;border-radius:6px" class="border"></div>
            </div>
        </div>

        <div class="mt-4">
            <button class="btn btn-warning px-4">Save Movie</button>
            <a href="{{ route('admin.movies.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
const searchUrl = "{{ route('admin.movies.tmdb-search') }}";
const fetchUrl = "{{ route('admin.movies.tmdb-fetch') }}";

const queryInput = document.getElementById('tmdbQuery');
const resultsBox = document.getElementById('tmdbResults');
const statusBox = document.getElementById('tmdbStatus');
let searchTimer = null;

function runSearch() {
    const q = queryInput.value.trim();
    if (q.length < 2) { resultsBox.classList.add('d-none'); return; }

    fetch(`${searchUrl}?query=${encodeURIComponent(q)}`)
        .then(r => r.json())
        .then(data => {
            resultsBox.innerHTML = '';
            if (!data.results || data.results.length === 0) {
                resultsBox.classList.add('d-none');
                return;
            }
            data.results.forEach(movie => {
                const item = document.createElement('div');
                item.className = 'tmdb-result-item';
                item.innerHTML = `
                    <img src="${movie.poster_thumb || '{{ asset('images/poster-placeholder.png') }}'}" alt="">
                    <div>
                        <div class="fw-semibold">${movie.title}</div>
                        <div class="small text-secondary">${movie.release_date ? movie.release_date.substring(0,4) : 'N/A'}</div>
                    </div>
                `;
                item.addEventListener('click', () => fetchMovie(movie.tmdb_id));
                resultsBox.appendChild(item);
            });
            resultsBox.classList.remove('d-none');
        })
        .catch(() => { statusBox.innerText = 'Search failed. Check your TMDB_API_KEY in .env.'; });
}

queryInput.addEventListener('input', () => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(runSearch, 400);
});
document.getElementById('tmdbSearchBtn').addEventListener('click', runSearch);

function fetchMovie(tmdbId) {
    resultsBox.classList.add('d-none');
    statusBox.innerText = 'Fetching movie details...';

    fetch(`${fetchUrl}?tmdb_id=${tmdbId}`)
        .then(async r => {
            const data = await r.json();
            if (!r.ok) throw new Error(data.message || 'Fetch failed');
            return data;
        })
        .then(data => {
            document.getElementById('field_tmdb_id').value = data.tmdb_id;
            document.getElementById('field_title').value = data.title || '';
            document.getElementById('field_original_title').value = data.original_title || '';
            document.getElementById('field_original_language').value = data.original_language || '';
            document.getElementById('field_overview').value = data.overview || '';
            document.getElementById('field_release_date').value = data.release_date || '';
            document.getElementById('field_runtime').value = data.runtime || '';
            document.getElementById('field_language').value = data.language || '';
            document.getElementById('field_popularity').value = data.popularity || '';
            document.getElementById('field_vote_average').value = data.vote_average || '';
            document.getElementById('field_vote_count').value = data.vote_count || '';
            document.getElementById('field_status').value = data.status || '';
            document.getElementById('field_adult').checked = !!data.adult;
            document.getElementById('field_production_countries').value = data.production_countries || '';

            // Raw TMDb relative paths — the backend will download & store these locally on save.
            document.getElementById('field_poster_path').value = data.poster_path || '';
            document.getElementById('field_backdrop_path').value = data.backdrop_path || '';

            document.getElementById('posterPreview').src = data.poster_preview || '{{ asset('images/poster-placeholder.png') }}';
            document.getElementById('backdropPreview').src = data.backdrop_preview || '{{ asset('images/backdrop-placeholder.png') }}';

            // Match genres against existing checkboxes; anything unmatched becomes a hidden genre_names[] field.
            document.querySelectorAll('.genre-checkbox').forEach(cb => cb.checked = false);
            const container = document.getElementById('genreNamesContainer');
            container.innerHTML = '';
            const unmatched = [];

            (data.genres || []).forEach(g => {
                const labels = Array.from(document.querySelectorAll('#genreCheckboxes label'));
                const match = labels.find(l => l.innerText.trim().toLowerCase() === g.name.toLowerCase());
                if (match) {
                    document.getElementById(match.getAttribute('for')).checked = true;
                } else {
                    unmatched.push(g.name);
                    const hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = 'genre_names[]';
                    hidden.value = g.name;
                    container.appendChild(hidden);
                }
            });

            document.getElementById('newGenresNote').innerText = unmatched.length
                ? `New genres will be created: ${unmatched.join(', ')}`
                : '';

            statusBox.innerHTML = `<span class="text-success"><i class="fa-solid fa-circle-check"></i> Movie details loaded. Review and click Save Movie.</span>`;
        })
        .catch(err => {
            statusBox.innerHTML = `<span class="text-danger">${err.message}</span>`;
        });
}

document.addEventListener('click', (e) => {
    if (!resultsBox.contains(e.target) && e.target !== queryInput) {
        resultsBox.classList.add('d-none');
    }
});
</script>
@endpush
