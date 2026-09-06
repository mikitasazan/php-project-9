# Анализатор страниц (PHP)

[![hexlet-check](https://github.com/mikitasazan/php-project-9/actions/workflows/hexlet-check.yml/badge.svg)](https://github.com/mikitasazan/php-project-9/actions/workflows/hexlet-check.yml)

Сайт, который принимает адрес страницы и проверяет её на пригодность для
поисковой оптимизации: отвечает ли сервер, что стоит в заголовке `h1`, в
`title` и в описании страницы. Каждая проверка сохраняется, поэтому видно, как
страница менялась со временем.

## Требования

- PHP 8.2 или новее
- Composer
- PostgreSQL

## Установка

```bash
git clone https://github.com/mikitasazan/php-project-9.git
cd php-project-9
make setup
```

Адрес базы задаётся переменной `DATABASE_URL`, например
`postgres://user:password@localhost:5432/page_analyzer`. Таблицы создаются
командой:

```bash
make migrate
```

## Запуск

```bash
make start          # http://localhost:8000
make start PORT=80  # на другом порту
```

## Разработка

```bash
make lint   # проверка стиля и статический анализ
```

## Развёртывание

В репозитории лежит `Dockerfile`, готовый для Render.com: он ставит
зависимости, собирает автозагрузку и поднимает приложение на порту из
переменной `PORT`. Сам сервис пока не развёрнут.
