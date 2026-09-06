DROP TABLE IF EXISTS url_checks CASCADE;
DROP TABLE IF EXISTS urls CASCADE;

CREATE TABLE urls (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    created_at TIMESTAMP(0) NOT NULL
);

CREATE TABLE url_checks (
    id BIGSERIAL PRIMARY KEY,
    url_id BIGINT NOT NULL REFERENCES urls (id),
    status_code INT,
    h1 VARCHAR(1000),
    title TEXT,
    description TEXT,
    created_at TIMESTAMP(0) NOT NULL
);

CREATE INDEX url_checks_url_id_idx ON url_checks (url_id);
