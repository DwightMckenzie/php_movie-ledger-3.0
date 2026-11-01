/**
 * Reference implementation: movie filtering + pagination via XHR.
 *
 * Same architecture as the original: the PHP endpoint returns the FULL
 * filtered/sorted array, and this script slices it client-side for
 * pagination. Fixes applied vs. the original:
 *   - "results found" now reflects the true total, not the rendered page
 *   - currentPage resets to 1 on every filter/search/order change
 *   - "no results" row is valid <tr><td>...</td></tr> markup
 *   - values are escaped before being injected into HTML
 *   - pagination handles a zero-result set without leaving stale buttons
 */

"use strict";

// ---- State ----
let currentPage = 1;
let pageSize = 10;

// ---- DOM refs ----
const paginationElm = document.getElementById('pagination');
const pageSizeElm = document.getElementById('page-size');
const movieListElm = document.getElementById('movie-list');
const resultsFoundElm = document.querySelector('#results-found span');

const searchTitleElm = document.getElementById('search-title');
const movieOrderElm = document.getElementById('movie-order');
const movieTypeElm = document.getElementById('movie-type');
const movieCategoryElm = document.getElementById('movie-category');
const movieRatingElm = document.getElementById('movie-rating');

/**
 * Escape a value before injecting it into HTML.
 */
function escapeHtml(value) {
  const div = document.createElement('div');
  div.textContent = String(value ?? '');
  return div.innerHTML;
}

/**
 * Render the movie rows for the CURRENT PAGE ONLY.
 * @param {Array} pageItems - already-sliced subset of the full result set
 */
function renderMovieRows(pageItems) {
  const tbody = movieListElm.querySelector('tbody');

  if (!pageItems || pageItems.length === 0) {
    tbody.innerHTML = '<tr><td colspan="5">Sorry! No matches found</td></tr>';
    return;
  }

  tbody.innerHTML = pageItems.map((m) => `
    <tr>
      <td>${escapeHtml(m.title)}</td>
      <td>${escapeHtml(m.type)}</td>
      <td>${escapeHtml(m.category)}</td>
      <td>${escapeHtml(m.rating)}</td>
      <td><a href="single-movie.php?id=${encodeURIComponent(m.id)}">View</a></td>
    </tr>
  `).join('');
}

/**
 * Update the "results found" counter using the TRUE total
 * (full filtered array length), not the rendered row count.
 */
function updateResultsCount(total) {
  resultsFoundElm.innerHTML = total;
}

/**
 * Build pagination controls based on the full result set size.
 * @param {number} total - length of the FULL filtered array (not the page slice)
 */
function renderPagination(total) {
  paginationElm.innerHTML = '';

  const pageCount = Math.max(1, Math.ceil(total / pageSize));

  // guard against a stale currentPage (e.g. filters shrank the result set)
  if (currentPage > pageCount) currentPage = pageCount;

  const prevBtn = document.createElement('button');
  prevBtn.textContent = 'Prev';
  prevBtn.disabled = currentPage === 1;
  prevBtn.addEventListener('click', () => {
    currentPage--;
    fetchMovies();
  });
  paginationElm.appendChild(prevBtn);

  // Only render page-number buttons when there's real data.
  if (total > 0) {
    for (let i = 1; i <= pageCount; i++) {
      const btn = document.createElement('button');
      btn.textContent = i;
      if (i === currentPage) btn.classList.add('active');
      btn.addEventListener('click', () => {
        currentPage = i;
        fetchMovies();
      });
      paginationElm.appendChild(btn);
    }
  }

  const nextBtn = document.createElement('button');
  nextBtn.textContent = 'Next';
  nextBtn.disabled = total === 0 || currentPage === pageCount;
  nextBtn.addEventListener('click', () => {
    currentPage++;
    fetchMovies();
  });
  paginationElm.appendChild(nextBtn);
}

/**
 * Read the current filter/search/sort values from the form controls.
 */
function getFilters(overrides = {}) {
  return {
    'movie-order': movieOrderElm.value,
    'movie-title': searchTitleElm.value.trim() || 'null',
    'movie-type': movieTypeElm.value,
    'movie-category': movieCategoryElm.value,
    'movie-rating': movieRatingElm.value,
    ...overrides,
  };
}

/**
 * Reset all filter controls back to their defaults (used by "reset").
 */
function resetFilterControls() {
  searchTitleElm.value = '';
  movieOrderElm.value = 'uo';
  movieTypeElm.value = 'all';
  movieCategoryElm.value = 'all';
  movieRatingElm.value = 'all';
}

/**
 * Fetch the full filtered/sorted list from the server, then slice it
 * client-side for the current page.
 *
 * @param {boolean} resetPage - jump back to page 1 (any filter/search/order
 *                              change should ALWAYS do this, or you can end
 *                              up requesting a page that no longer exists)
 */
function fetchMovies(resetPage = true) {
  if (resetPage) currentPage = 1;

  pageSize = parseInt(pageSizeElm.value, 10) || 10;

  const payload = getFilters();

  const xhr = new XMLHttpRequest();
  xhr.open('POST', './ajax/movieFilter.php', true);
  xhr.setRequestHeader('Content-Type', 'application/json');

  xhr.onload = function () {
    if (xhr.status !== 200) {
      console.error('Error:', xhr.statusText);
      return;
    }

    let data;
    try {
      data = JSON.parse(xhr.responseText);
    } catch (err) {
      console.error('Bad JSON from server:', err);
      return;
    }

    const total = Array.isArray(data) ? data.length : 0;

    const start = (currentPage - 1) * pageSize;
    const end = start + pageSize;
    const pageItems = Array.isArray(data) ? data.slice(start, end) : [];

    renderMovieRows(pageItems);
    renderPagination(total);
    updateResultsCount(total);
  };

  xhr.onerror = function () {
    console.error('Network error while fetching movies');
  };

  xhr.send(JSON.stringify(payload));
}

// ---- Event wiring ----
// Any change to filters/search/sort/page-size resets to page 1.
searchTitleElm?.addEventListener('input', () => fetchMovies(true));
movieOrderElm?.addEventListener('change', () => fetchMovies(true));
movieTypeElm?.addEventListener('change', () => fetchMovies(true));
movieCategoryElm?.addEventListener('change', () => fetchMovies(true));
movieRatingElm?.addEventListener('change', () => fetchMovies(true));
pageSizeElm?.addEventListener('change', () => fetchMovies(true));

document.getElementById('reset-btn')?.addEventListener('click', () => {
  resetFilterControls();
  fetchMovies(true);
});

// ---- Initial load ----
fetchMovies(true);
