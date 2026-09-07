PORT ?= 8000

.PHONY: setup install start migrate lint lint-fix

setup: install

install:
	composer install

migrate:
	psql -a -d "$(DATABASE_URL)" -f database.sql

start:
	PHP_CLI_SERVER_WORKERS=5 php -S 0.0.0.0:$(PORT) -t public

lint:
	./vendor/bin/mago format --dry-run
	./vendor/bin/mago lint
	./vendor/bin/phpcs

lint-fix:
	./vendor/bin/mago format
	./vendor/bin/mago lint --fix
	./vendor/bin/phpcbf || true
