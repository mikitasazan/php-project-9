<?php

/** @var App\Domain\Site $site */
/** @var App\Domain\SiteCheck[] $checks */
/** @var Slim\Interfaces\RouteParserInterface $routeParser */

?>
<h1><?= e($site->getName()) ?></h1>
<table data-test="url">
    <tbody>
    <tr>
        <td>ID</td>
        <td><?= e($site->getId()) ?></td>
    </tr>
    <tr>
        <td>Имя</td>
        <td><?= e($site->getName()) ?></td>
    </tr>
    <tr>
        <td>Дата создания</td>
        <td><?= e($site->getCreatedAt()) ?></td>
    </tr>
    </tbody>
</table>

<h2>Проверки</h2>
<form method="post" action="<?= e(routeUrl($routeParser, 'urls.checks.store', ['id' => (string) $site->getId()])) ?>">
    <input type="submit" value="Запустить проверку">
</form>

<table data-test="checks">
    <thead>
    <tr>
        <th>ID</th>
        <th>Код ответа</th>
        <th>h1</th>
        <th>title</th>
        <th>description</th>
        <th>Дата создания</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($checks as $check): ?>
        <tr>
            <td><?= e($check->getId()) ?></td>
            <td><?= e($check->getStatusCode()) ?></td>
            <td><?= e(truncate($check->getH1())) ?></td>
            <td><?= e(truncate($check->getTitle())) ?></td>
            <td><?= e(truncate($check->getDescription())) ?></td>
            <td><?= e($check->getCreatedAt()) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
