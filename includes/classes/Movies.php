<?php
declare(strict_types=1);

/**
 * Data access and validation for the movie_list table.
 * All SQL uses bound parameters. Allowed values are defined once here.
 */
final class Movies
{
    /** value => label */
    public const TYPES = [
        'Movie'       => 'Movie',
        'Series'      => 'Series',
        'Mini Series' => 'Mini Series',
    ];

    public const CATEGORIES = [
        'Action'      => 'Action',
        'Adventure'   => 'Adventure',
        'Classic'     => 'Classic',
        'Comedy'      => 'Comedy',
        'Crime'       => 'Crime',
        'Documentary' => 'Documentary',
        'Drama'       => 'Drama',
        'Family'      => 'Family',
        'Fantasy'     => 'Fantasy',
        'Horror'      => 'Horror',
        'Romance'     => 'Romance',
        'Scifi'       => 'Sci-fi',
        'Thriller'    => 'Thriller',
        'Western'     => 'Western',
    ];

    public const RATINGS = [
        'R'      => 'R',
        'NR'     => 'NR',
        'PG'     => 'PG',
        'PG-13'  => 'PG-13',
        'M'      => 'Mature',
    ];

    public const PAGE_SIZES = [25, 50, 75, 100];

    /** Sort direction keyword => SQL keyword (whitelist, never interpolate raw input). */
    public const ORDERS = [
        'desc' => 'DESC',
        'asc'  => 'ASC',
    ];

    private const TITLE_MAX = 255;
    private const NOTE_MAX  = 5000;

    public function __construct(private PDO $db)
    {
    }

    /**
     * Validate and normalise form input.
     *
     * @return array{0: array<string, ?string>, 1: list<string>} [clean data, error messages]
     */
    public static function validate(array $input): array
    {
        $errors = [];

        $title = trim(str_input($input, 'title'));
        if ($title === '') {
            $errors[] = 'Title is required.';
        } elseif (mb_strlen($title) > self::TITLE_MAX) {
            $errors[] = 'Title must be ' . self::TITLE_MAX . ' characters or fewer.';
        }

        $type = str_input($input, 'type');
        if (!array_key_exists($type, self::TYPES)) {
            $errors[] = 'Choose a valid type.';
        }

        $category = str_input($input, 'category');
        if (!array_key_exists($category, self::CATEGORIES)) {
            $errors[] = 'Choose a valid category.';
        }

        $rating = str_input($input, 'rating');
        if (!array_key_exists($rating, self::RATINGS)) {
            $errors[] = 'Choose a valid rating.';
        }

        $release = trim(str_input($input, 'release_date'));
        if ($release !== '') {
            $date = DateTimeImmutable::createFromFormat('!Y-m-d', $release);
            if ($date === false || $date->format('Y-m-d') !== $release) {
                $errors[] = 'Release date must be a valid date.';
            }
        }

        $note = trim(str_input($input, 'note'));
        if (mb_strlen($note) > self::NOTE_MAX) {
            $errors[] = 'Notes must be ' . self::NOTE_MAX . ' characters or fewer.';
        }

        $clean = [
            'title'        => $title,
            'type'         => $type,
            'category'     => $category,
            'rating'       => $rating,
            'release_date' => $release === '' ? null : $release,
            'note'         => $note === '' ? null : $note,
        ];

        return [$clean, $errors];
    }

    /** @return array<string, mixed>|null */
    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM movie_list WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    /**
     * One page of results for the list view.
     *
     * @param array{title: string, type: string, category: string, rating: string} $filters
     * @return array{total: int, page: int, pageCount: int, results: list<array<string, mixed>>}
     */
    public function search(array $filters, int $page, int $pageSize, string $order): array
    {
        [$where, $params] = $this->buildWhere($filters);

        $count = $this->db->prepare("SELECT COUNT(*) FROM movie_list $where");
        $count->execute($params);
        $total = (int) $count->fetchColumn();

        $pageCount = max(1, (int) ceil($total / $pageSize));
        $page = min(max(1, $page), $pageCount);
        $offset = ($page - 1) * $pageSize;

        $dir = self::ORDERS[$order] ?? 'DESC';

        $stmt = $this->db->prepare(
            "SELECT id, title, type, category, rating FROM movie_list $where
             ORDER BY title $dir, id $dir
             LIMIT :limit OFFSET :offset"
        );
        foreach ($params as $name => $value) {
            $stmt->bindValue($name, $value);
        }
        $stmt->bindValue(':limit', $pageSize, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'total'     => $total,
            'page'      => $page,
            'pageCount' => $pageCount,
            'results'   => $stmt->fetchAll(),
        ];
    }

    /** Stream every movie in title order (for CSV export). */
    public function exportRows(): Generator
    {
        $stmt = $this->db->query(
            'SELECT id, title, type, category, rating, release_date, note, entry_date
             FROM movie_list ORDER BY title, id'
        );
        foreach ($stmt as $row) {
            yield $row;
        }
    }

    /** @param array<string, ?string> $data */
    public function create(array $data, ?string $coverPath): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO movie_list (title, type, category, rating, release_date, note, cover, entry_date)
             VALUES (:title, :type, :category, :rating, :release_date, :note, :cover, CURDATE())'
        );
        $stmt->execute([
            ':title'        => $data['title'],
            ':type'         => $data['type'],
            ':category'     => $data['category'],
            ':rating'       => $data['rating'],
            ':release_date' => $data['release_date'],
            ':note'         => $data['note'],
            ':cover'        => $coverPath,
        ]);
        return (int) $this->db->lastInsertId();
    }

    /**
     * Update every editable field. Pass a cover path to replace the cover;
     * pass null to keep the existing one.
     *
     * @param array<string, ?string> $data
     */
    public function update(int $id, array $data, ?string $coverPath): void
    {
        $sql = 'UPDATE movie_list
                SET title = :title, type = :type, category = :category, rating = :rating,
                    release_date = :release_date, note = :note';
        $params = [
            ':id'           => $id,
            ':title'        => $data['title'],
            ':type'         => $data['type'],
            ':category'     => $data['category'],
            ':rating'       => $data['rating'],
            ':release_date' => $data['release_date'],
            ':note'         => $data['note'],
        ];
        if ($coverPath !== null) {
            $sql .= ', cover = :cover';
            $params[':cover'] = $coverPath;
        }
        $this->db->prepare($sql . ' WHERE id = :id')->execute($params);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM movie_list WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Build the WHERE clause. Title uses LIKE with wildcards escaped;
     * type, category and rating are exact matches.
     *
     * @return array{0: string, 1: array<string, string>}
     */
    private function buildWhere(array $filters): array
    {
        $clauses = [];
        $params = [];

        if ($filters['title'] !== '') {
            $clauses[] = "title LIKE :title ESCAPE '!'";
            $params[':title'] = '%' . self::escapeLike($filters['title']) . '%';
        }
        foreach (['type', 'category', 'rating'] as $field) {
            if ($filters[$field] !== '') {
                $clauses[] = "$field = :$field";
                $params[":$field"] = $filters[$field];
            }
        }

        return [$clauses ? 'WHERE ' . implode(' AND ', $clauses) : '', $params];
    }

    /** Escape LIKE wildcards using '!' as the escape character. */
    private static function escapeLike(string $value): string
    {
        return str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $value);
    }
}
