<?php

/** @var string $submittedUrl */
/** @var Slim\Interfaces\RouteParserInterface $routeParser */

?>
<h1>Анализатор страниц</h1>
<p>Бесплатно проверяйте сайты на SEO пригодность</p>
<form action="<?= e(routeUrl($routeParser, 'urls.store')) ?>" method="post">
    <label for="url">URL</label>
    <input
        id="url"
        type="text"
        name="url"
        value="<?= e($submittedUrl) ?>"
        placeholder="https://www.example.com"
    >
    <input type="submit" value="Проверить">
</form>
