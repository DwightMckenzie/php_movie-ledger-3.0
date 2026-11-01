# Movie Ledger 3.0

A small PHP and MySQL/MariaDB app for keeping a personal list of movies and series:
search, filter, sort, paginate, add/edit/delete entries, attach a cover image, and export to CSV.

## Requirements

- PHP 8.1 or newer, with the `pdo_mysql`, `fileinfo` and `gd` extensions
- MariaDB 10.5+ or MySQL 8+
- A web server that can run PHP (Apache, nginx + PHP-FPM, or `php -S` for local use)

## Setup

1. **Create the database and a user**

   ```sql
   CREATE DATABASE movies_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   CREATE USER 'movies_app'@'localhost' IDENTIFIED BY 'choose-a-strong-password';
   GRANT SELECT, INSERT, UPDATE, DELETE ON movies_db.* TO 'movies_app'@'localhost';
   ```

   Grant only these four privileges. The app never needs to create or drop tables.

2. **Create the table**

   ```bash
   mysql -u root -p movies_db < sql/schema.sql
   ```

3. **Configure the app.** Copy the example file and fill in your values:

   ```bash
   cp includes/config.local.example.php includes/config.local.php
   ```

   Generate the login password hash:

   ```bash
   php -r 'echo password_hash("your-password", PASSWORD_DEFAULT), PHP_EOL;'
   ```

   Paste the output into `APP_PASSWORD_HASH`. Environment variables with the same names take precedence over the file. `includes/config.local.php` is git-ignored, so credentials stay out of the repository.

4. **Make the upload folder writable** by the web server user:

   ```bash
   chmod 775 assets/uploads/covers   # adjust owner/group for your server
   ```

5. **Run it.** For a quick local test:

   ```bash
   php -S localhost:8000
   ```

   Then open http://localhost:8000. For production, serve the folder through Apache or nginx with HTTPS, and keep `APP_DEBUG` at `0`.

## Upgrading from 2.x

The schema is compatible with the old `movie_list` table, with two additions: `note` and `cover`. If your table lacks them, add them (MariaDB syntax shown; on MySQL, check first whether each column exists):

```sql
ALTER TABLE movie_list
  ADD COLUMN IF NOT EXISTS note  TEXT NULL,
  ADD COLUMN IF NOT EXISTS cover VARCHAR(255) NULL;
```

Existing rows must use the values the app now offers. Rows with other values (for example `all` from the old create form) will appear with blank or unrecognised labels until corrected. Check with:

```sql
SELECT id, title, type, category, rating FROM movie_list
WHERE type NOT IN ('Movie','Series','Mini Series')
   OR category NOT IN ('Action','Adventure','Classic','Comedy','Crime','Documentary','Drama',
                       'Family','Fantasy','Horror','Romance','Scifi','Thriller','Western')
   OR rating NOT IN ('R','NR','PG','PG-13','M');
```

Allowed values are defined once in `includes/classes/Movies.php`.

## Project layout

| Path | Purpose |
| --- | --- |
| `index.php` | Movie list with filters, sorting and paging |
| `single-movie.php` | Movie details, edit and delete |
| `login.php`, `logout.php` | Single-user login |
| `controllers/` | Form handlers for create, update and delete (POST + CSRF only) |
| `ajax/movie-search.php` | JSON search endpoint used by the list |
| `ajax/movie-export.php` | CSV download |
| `includes/` | Config, helpers, auth, header/footer, shared form fields |
| `includes/classes/Movies.php` | Data access, validation and allowed values |
| `includes/classes/CoverUpload.php` | Validated cover image uploads |
| `assets/js/` | `movie-filter.js` (list), `app.js` (confirm prompts) |
| `sql/schema.sql` | Table definition |

## Security notes

- All SQL uses prepared statements. LIKE wildcards in the title search are escaped.
- Create, update, delete and logout require POST with a CSRF token.
- Output is HTML-escaped. The list builds its DOM with `textContent`.
- Uploads are checked by content (not file extension or browser type), limited to 5 MB, and renamed to random names.
- Exports neutralise spreadsheet formulas (cells starting with `=`, `+`, `-`, `@`).
- Login fails closed: with no password hash configured, nobody can log in.
- Errors are logged on the server. Visitors see generic messages.

Not yet included: login rate limiting, multiple user accounts, and image resizing for covers.

## Development checks

```bash
for f in $(git ls-files '*.php'); do php -l "$f"; done
node --check assets/js/movie-filter.js
```
