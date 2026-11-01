/**
 * Reference implementation: movie filtering + SERVER-SIDE pagination via XHR.
 *
 * The server now does the LIMIT/OFFSET work and returns:
 *   { total: <int>, results: [...] }   // results = just the current page
 *
 * This client only ever holds one page of rows in memory at a time,
 * and asks the server for a fresh page whenever page/filter/sort changes.
 */

"use strict";

// ---- State ----
let currentPage = 1;
let pageSize = 'all';

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

const movieExportElm = document.getElementById('export-csv');

/**
 * Escape a value before injecting it into HTML.
 */
function escapeHtml(value) {
  const div = document.createElement('div');
  div.textContent = String(value ?? '');
  return div.innerHTML;
}

/**
 * Render the movie rows for the current page.
 * @param {Array} pageItems - the current page's rows, as returned by the server
 */
function renderMovieRows(pageItems) {
  const tbody = movieListElm.querySelector('tbody');

	//
  if (!pageItems || pageItems.length === 0) {
    tbody.innerHTML = '<tr><td colspan="5" class="table-warning">Sorry! No matches found</td></tr>';
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
 * Update the "results found" counter using the server-reported total.
 */
function updateResultsCount(total) {
  resultsFoundElm.innerHTML = total;
}

/**
 * Build pagination controls based on the server-reported total.
 * @param {number} total - total matching rows across ALL pages
 */
function renderPagination(total) {
  paginationElm.innerHTML = '';

  const pageCount = Math.max(1, Math.ceil(total / pageSize));

  // guard against a stale currentPage (e.g. filters shrank the result set)
  if (currentPage > pageCount) {
    currentPage = pageCount;
  }

  // const prevBtn = document.createElement('button');
  const prevBtn = document.createElement('li');
  // prevBtn.textContent = 'Prev';
  
	prevBtn.setAttribute('class', 'page-item');
	prevBtn.innerHTML = '<a class="page-link" href="#">Previous</a>';
  
  prevBtn.disabled = currentPage === 1;
  prevBtn.addEventListener('click', () => {
    currentPage--;
    fetchMovies(false);
  });
  paginationElm.appendChild(prevBtn);


  //
  if (total > 0) {
    for (let i = 1; i <= pageCount; i++) {
      // const btn = document.createElement('button');
	  
	const btn = document.createElement('li');
	btn.setAttribute('class', 'page-item');
	    
      // btn.textContent = i;
      
	 // print current index in button
		btn.innerHTML = `<a class="page-link" href="#">${i}</a>`;
	  
	  
	  if (i === currentPage) btn.classList.add('active');
      
	  
	  btn.addEventListener('click', () => {
        currentPage = i;
        fetchMovies(false);
      });
      
	  paginationElm.appendChild(btn);
    
	}
  
  }

	// create next button
  // const nextBtn = document.createElement('button');
  // nextBtn.textContent = 'Next';
  const nextBtn = document.createElement('li');	
	nextBtn.setAttribute('class', 'page-item');
	nextBtn.innerHTML = '<a class="page-link" href="#">Next</a>';
  
  nextBtn.disabled = total === 0 || currentPage === pageCount;
  nextBtn.addEventListener('click', () => {
    currentPage++;
    fetchMovies(false);
  });
  
  // add the next button then close
  paginationElm.appendChild(nextBtn);
}

/**
 * Read the current filter/search/sort values from the form controls.
 */
function getFilters() {
  return {
    'movie-order': movieOrderElm.value,
    'movie-title': searchTitleElm.value.trim() || 'null',
    'movie-type': movieTypeElm.value,
    'movie-category': movieCategoryElm.value,
    'movie-rating': movieRatingElm.value,
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
 * Fetch ONE page of results from the server. The server applies filters,
 * sort, and LIMIT/OFFSET, and returns { total, results }.
 *
 * @param {boolean} resetPage - jump back to page 1 (any filter/search/order
 *                              change should ALWAYS do this — the previous
 *                              page number may no longer exist for the new
 *                              result set)
 */
function fetchMovies(resetPage = true) {
  if (resetPage) currentPage = 1;

  pageSize = parseInt(pageSizeElm.value, 10) || 25;

  const payload = {
    ...getFilters(),
    page: currentPage,
    pageSize: pageSize,
  };

  const xhr = new XMLHttpRequest();
  xhr.open('POST', './ajax/movieFilter-1.2.php', true);
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

    if (data.error) {
      console.error('Server error:', data.error);
      renderMovieRows([]);
      renderPagination(0);
      updateResultsCount(0);
      return;
    }

    const total = data.total ?? 0;
    const results = data.results ?? [];

    renderMovieRows(results);
    renderPagination(total);
    updateResultsCount(total);
  };

  xhr.onerror = function () {
    console.error('Network error while fetching movies');
  };

  xhr.send(JSON.stringify(payload));
}





/*
export 
*/

function movieExportCsv() {
	
	
	
}





/* ---- Event wiring ----
	Any change to filters/search/sort/page-size resets to page 1,
	since the previous page number may not exist in the new result set.
*/
searchTitleElm?.addEventListener('input', () => fetchMovies(true));
movieOrderElm?.addEventListener('change', () => fetchMovies(false));
// movieTypeElm?.addEventListener('change', () => fetchMovies(false));
// movieCategoryElm?.addEventListener('change', () => fetchMovies(false));
// movieRatingElm?.addEventListener('change', () => fetchMovies(false));
pageSizeElm?.addEventListener('change', () => fetchMovies(false));





document.getElementById('apply')?.addEventListener('click', () => {
	// console.log('here is apply');
	
	// let test = getFilters();
	// console.log(test);
	
	fetchMovies();
	
});

document.getElementById('reset')?.addEventListener('click', () => {
  resetFilterControls();
  fetchMovies(true);
});

// ---- Initial load ----
fetchMovies(true);
