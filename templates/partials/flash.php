<?php

/** @var array $flash */
$styles = ['success' => 'alert-success', 'error' => 'alert-error'];
?>
<?php foreach ($styles as $key => $class): ?>
    <?php foreach ($flash[$key] ?? [] as $message): ?>
        <div class="alert <?= $class ?>" role="alert"><?= e($message) ?></div>
    <?php endforeach; ?>
<?php endforeach;
