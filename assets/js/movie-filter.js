/**
 * Movie list: filtering, sorting, and server-side pagination.
 *
 * - Requests are debounced while typing and any older in-flight request is
 *   aborted, so a slow response can't overwrite a newer one.
 * - All server data is written with textContent, never innerHTML.
 */
(() => {
  'use strict';

  const form = document.getElementById('filter-form');
  if (!form) return;

  const endpoint = 'ajax/movie-search.php';
  const DEBOUNCE_MS = 300;
  const WINDOW = 2; // page links shown either side of the current page

  const el = {
    title: document.getElementById('search-title'),
    type: document.getElementById('movie-type'),
    category: document.getElementById('movie-category'),
    rating: document.getElementById('movie-rating'),
    order: document.getElementById('movie-order'),
    pageSize: document.getElementById('page-size'),
    reset: document.getElementById('reset'),
    tbody: document.querySelector('#movie-table tbody'),
    count: document.getElementById('results-count'),
    pagination: document.getElementById('pagination'),
    error: document.getElementById('list-error'),
  };

  let page = 1;
  let debounceTimer = null;
  let controller = null;

  /* ---------- requests ---------- */

  function currentFilters() {
    return {
      title: el.title.value.trim(),
      type: el.type.value,
      category: el.category.value,
      rating: el.rating.value,
      order: el.order.value,
      pageSize: Number(el.pageSize.value),
      page,
    };
  }

  async function load() {
    if (controller) controller.abort();
    controller = new AbortController();
    showError('');

    try {
      const response = await fetch(endpoint, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify(currentFilters()),
        signal: controller.signal,
      });

      if (response.status === 401) {
        window.location.href = 'login.php';
        return;
      }
      const data = await response.json();
      if (!response.ok || data.error) {
        throw new Error(data.error || 'Request failed.');
      }

      page = data.page; // the server clamps out-of-range pages
      render(data);
    } catch (err) {
      if (err.name === 'AbortError') return; // superseded by a newer request
      showError('The movie list could not be loaded. Please try again.');
    }
  }

  function requestNow(resetPage) {
    if (resetPage) page = 1;
    clearTimeout(debounceTimer);
    load();
  }

  function requestDebounced() {
    page = 1;
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(load, DEBOUNCE_MS);
  }

  /* ---------- rendering ---------- */

  function render(data) {
    el.count.textContent = String(data.total);
    renderRows(data.results);
    renderPagination(data.page, data.pageCount);
  }

  function renderRows(rows) {
    el.tbody.replaceChildren();

    if (rows.length === 0) {
      const tr = document.createElement('tr');
      const td = document.createElement('td');
      td.colSpan = 5;
      td.className = 'table-warning';
      td.textContent = 'No movies match these filters.';
      tr.append(td);
      el.tbody.append(tr);
      return;
    }

    for (const movie of rows) {
      const tr = document.createElement('tr');
      tr.append(
        cell(movie.title),
        cell(movie.type),
        cell(movie.category),
        cell(movie.rating),
      );

      const linkCell = document.createElement('td');
      const link = document.createElement('a');
      link.href = `single-movie.php?id=${encodeURIComponent(movie.id)}`;
      link.textContent = 'View';
      linkCell.append(link);
      tr.append(linkCell);

      el.tbody.append(tr);
    }
  }

  function cell(text) {
    const td = document.createElement('td');
    td.textContent = text ?? '';
    return td;
  }

  function renderPagination(current, pageCount) {
    el.pagination.replaceChildren();
    if (pageCount <= 1) return;

    el.pagination.append(
      pageItem('Previous', current - 1, current <= 1),
    );

    const pages = visiblePages(current, pageCount);
    let previous = 0;
    for (const p of pages) {
      if (p - previous > 1) {
        el.pagination.append(gapItem());
      }
      el.pagination.append(pageItem(String(p), p, false, p === current));
      previous = p;
    }

    el.pagination.append(
      pageItem('Next', current + 1, current >= pageCount),
    );
  }

  function visiblePages(current, pageCount) {
    const set = new Set([1, pageCount]);
    for (let p = current - WINDOW; p <= current + WINDOW; p++) {
      if (p >= 1 && p <= pageCount) set.add(p);
    }
    return [...set].sort((a, b) => a - b);
  }

  function pageItem(label, target, disabled, active = false) {
    const li = document.createElement('li');
    li.className = 'page-item' + (disabled ? ' disabled' : '') + (active ? ' active' : '');

    const button = document.createElement('button');
    button.type = 'button';
    button.className = 'page-link';
    button.textContent = label;
    button.disabled = disabled;
    if (active) button.setAttribute('aria-current', 'page');
    if (!disabled && !active) {
      button.addEventListener('click', () => {
        page = target;
        load();
      });
    }

    li.append(button);
    return li;
  }

  function gapItem() {
    const li = document.createElement('li');
    li.className = 'page-item disabled';
    const span = document.createElement('span');
    span.className = 'page-link';
    span.textContent = '…';
    li.append(span);
    return li;
  }

  function showError(message) {
    el.error.textContent = message;
    el.error.classList.toggle('d-none', message === '');
  }

  /* ---------- events ---------- */

  form.addEventListener('submit', (event) => {
    event.preventDefault();
    requestNow(true);
  });

  el.title.addEventListener('input', requestDebounced);
  el.type.addEventListener('change', () => requestNow(true));
  el.category.addEventListener('change', () => requestNow(true));
  el.rating.addEventListener('change', () => requestNow(true));
  el.order.addEventListener('change', () => requestNow(true));
  el.pageSize.addEventListener('change', () => requestNow(true));

  el.reset.addEventListener('click', () => {
    el.title.value = '';
    el.type.value = '';
    el.category.value = '';
    el.rating.value = '';
    el.order.value = 'desc';
    el.pageSize.value = '25';
    requestNow(true);
  });

  load();
})();
