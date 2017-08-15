
CREATE DATABASE framework;

CREATE TABLE bookmark
(
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    url VARCHAR(255) NOT NULL,
    nom VARCHAR(100) NOT NULL,
    CONSTRAINT bookmark_id_uindex UNIQUE (id)
);

INSERT INTO bookmark (url, nom) VALUES ('www.php.net', 'PHP');
INSERT INTO bookmark (url, nom) VALUES ('www.postgresql.org', 'PostgreSQL');
INSERT INTO bookmark (url, nom) VALUES ('www.debian.org', 'Debian');
