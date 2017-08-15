
CREATE DATABASE framework
WITH OWNER = stephane
ENCODING = 'UTF8'
LC_COLLATE = 'fr_FR.UTF-8'
LC_CTYPE = 'fr_FR.UTF-8'
CONNECTION LIMIT = -1;

CREATE TABLE bookmark
(
    id SMALLSERIAL,
    url VARCHAR(255) NOT NULL,
    nom VARCHAR(100) NOT NULL,
    PRIMARY KEY (id)
);

INSERT INTO bookmark (url, nom) VALUES ('www.php.net', 'PHP');
INSERT INTO bookmark (url, nom) VALUES ('www.postgresql.org', 'PostgreSQL');
INSERT INTO bookmark (url, nom) VALUES ('www.debian.org', 'Debian');
