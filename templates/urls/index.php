<?php

/** @var App\Domain\Site[] $sites */
/** @var array<int, App\Domain\SiteCheck> $latestChecks */
/** @var Slim\Interfaces\RouteParserInterface $routeParser */

?>
<h1>Сайты</h1>
<table data-test="urls">
    <thead>
    <tr>
        <th>ID</th>
        <th>Имя</th>
        <th>Последняя проверка</th>
        <th>Код ответа</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($sites as $site): ?>
        <?php $check = $latestChecks[$site->getId()] ?? null; ?>
        <tr>
            <td><?= e($site->getId()) ?></td>
            <td>
                <a href="<?= e(routeUrl($routeParser, 'urls.show', ['id' => (string) $site->getId()])) ?>">
                    <?= e($site->getName()) ?>
                </a>
            </td>
            <td><?= e($check?->getCreatedAt()) ?></td>
            <td><?= e($check?->getStatusCode()) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
