<?php

/** @var string $content */
/** @var array $flash */
/** @var Slim\Interfaces\RouteParserInterface $routeParser */
/** @var string|null $currentRouteName */

?><!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Анализатор страниц</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 0; color: #1f2937; }
        header { background: #1f2937; color: #fff; padding: .75rem 1.5rem; display: flex; gap: 1.5rem; align-items: center; }
        header a { color: inherit; text-decoration: none; }
        header a.brand { font-weight: 600; font-size: 1.1rem; }
        header nav a.active { text-decoration: underline; }
        main { max-width: 960px; margin: 1.5rem auto; padding: 0 1rem; }
        .alert { margin: .75rem 0; padding: .75rem 1rem; border-radius: .25rem; border: 1px solid; }
        .alert-success { background: #ecfdf5; border-color: #a7f3d0; color: #065f46; }
        .alert-error { background: #fef2f2; border-color: #fecaca; color: #991b1b; }
        table { width: 100%; border-collapse: collapse; margin: 1rem 0; }
        th, td { border: 1px solid #d1d5db; padding: .5rem .75rem; text-align: left; }
        input[type="text"] { padding: .5rem; font-size: 1rem; width: 100%; box-sizing: border-box; }
        input[type="submit"] { padding: .5rem 1.25rem; font-size: 1rem; cursor: pointer; }
    </style>
</head>
<body>
<header>
    <a class="brand" href="<?= e(routeUrl($routeParser, 'root')) ?>">Анализатор страниц</a>
    <nav>
        <a class="<?= $currentRouteName === 'urls.index' ? 'active' : '' ?>"
           href="<?= e(routeUrl($routeParser, 'urls.index')) ?>">Сайты</a>
    </nav>
</header>
<main>
    <?php include __DIR__ . '/partials/flash.php'; ?>
    <?= $content ?>
</main>
</body>
</html>
