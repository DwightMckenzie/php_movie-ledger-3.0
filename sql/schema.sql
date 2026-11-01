-- Movie Ledger schema (MariaDB 10.5+ / MySQL 8+)
-- Create the database first, then run this file against it.

CREATE TABLE IF NOT EXISTS movie_list (
    id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    title        VARCHAR(255)  NOT NULL,
    type         VARCHAR(20)   NOT NULL,
    category     VARCHAR(30)   NOT NULL,
    rating       VARCHAR(10)   NOT NULL,
    release_date DATE          NULL,
    note         TEXT          NULL,
    cover        VARCHAR(255)  NULL,
    entry_date   DATE          NOT NULL,
    PRIMARY KEY (id),
    KEY idx_movie_title (title)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;
