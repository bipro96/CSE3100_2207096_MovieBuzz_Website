document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('navSearchInput');
    const results = document.getElementById('navSearchResults');
    if (!input || !results) return;

    let timer = null;

    input.addEventListener('input', function () {
        clearTimeout(timer);
        const q = input.value.trim();
        if (q.length < 1) {
            results.classList.add('d-none');
            return;
        }
        timer = setTimeout(() => {
            fetch(`/movies/search?q=${encodeURIComponent(q)}`)
                .then(r => r.json())
                .then(data => {
                    results.innerHTML = '';
                    if (!data.results || data.results.length === 0) {
                        results.classList.add('d-none');
                        return;
                    }
                    data.results.forEach(movie => {
                        const a = document.createElement('a');
                        a.href = movie.url;
                        a.innerHTML = `<img src="${movie.poster}" alt=""><div><div>${movie.title}</div><small class="text-secondary">${movie.year || ''}</small></div>`;
                        results.appendChild(a);
                    });
                    results.classList.remove('d-none');
                })
                .catch(() => results.classList.add('d-none'));
        }, 300);
    });

    document.addEventListener('click', function (e) {
        if (!results.contains(e.target) && e.target !== input) {
            results.classList.add('d-none');
        }
    });
});
