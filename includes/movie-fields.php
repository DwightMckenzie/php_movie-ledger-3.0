<?php
declare(strict_types=1);

/**
 * Shared form fields for the add and edit movie forms.
 * $prefix keeps element IDs unique when both forms exist on one page.
 *
 * @param array<string, mixed> $movie existing values (empty for the add form)
 */
function render_movie_fields(string $prefix, array $movie = []): string
{
    $v = static fn (string $key): string => (string) ($movie[$key] ?? '');

    return '
    <div class="mb-3">
        <label for="' . $prefix . 'title" class="form-label fw-semibold">Title</label>
        <input type="text" class="form-control form-control-sm" id="' . $prefix . 'title" name="title"
               value="' . e($v('title')) . '" maxlength="255" required>
    </div>
    <div class="mb-3">
        <label for="' . $prefix . 'type" class="form-label fw-semibold">Type</label>
        <select class="form-select form-select-sm" id="' . $prefix . 'type" name="type" required>'
            . select_options(Movies::TYPES, $v('type'), 'Choose…') . '
        </select>
    </div>
    <div class="mb-3">
        <label for="' . $prefix . 'category" class="form-label fw-semibold">Category</label>
        <select class="form-select form-select-sm" id="' . $prefix . 'category" name="category" required>'
            . select_options(Movies::CATEGORIES, $v('category'), 'Choose…') . '
        </select>
    </div>
    <div class="mb-3">
        <label for="' . $prefix . 'rating" class="form-label fw-semibold">Rating</label>
        <select class="form-select form-select-sm" id="' . $prefix . 'rating" name="rating" required>'
            . select_options(Movies::RATINGS, $v('rating'), 'Choose…') . '
        </select>
    </div>
    <div class="mb-3">
        <label for="' . $prefix . 'release_date" class="form-label fw-semibold">Release date</label>
        <input type="date" class="form-control form-control-sm" id="' . $prefix . 'release_date" name="release_date"
               value="' . e($v('release_date')) . '">
        <div class="form-text">Optional.</div>
    </div>
    <div class="mb-3">
        <label for="' . $prefix . 'note" class="form-label fw-semibold">Notes</label>
        <textarea class="form-control form-control-sm" id="' . $prefix . 'note" name="note" rows="3" maxlength="5000">'
            . e($v('note')) . '</textarea>
    </div>
    <div class="mb-3">
        <label for="' . $prefix . 'cover" class="form-label fw-semibold">Cover image</label>
        <input type="file" class="form-control form-control-sm" id="' . $prefix . 'cover" name="movie-cover"
               accept="image/jpeg,image/png,image/webp">
        <div class="form-text">Optional. JPEG, PNG or WebP, up to 5 MB.</div>
    </div>';
}
