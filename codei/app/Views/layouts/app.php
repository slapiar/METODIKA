<!doctype html>
<html lang="<?= esc($lang ?? 'sk') ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= esc($metaDescription ?? 'METODIKA') ?>">
    <?= $this->renderSection('meta') ?>
    <title><?= esc($title ?? 'METODIKA') ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
    <?= $this->renderSection('styles') ?>
</head>
<body class="<?= esc($pageClass ?? '') ?>">
    <?php if (($showHeader ?? true) === true): ?>
        <?= view('partials/header', [
            'activeNav' => $activeNav ?? '',
            'navigation' => $navigation ?? null,
            'subtitle' => $subtitle ?? '',
        ]) ?>
    <?php endif; ?>

    <main class="page-main">
        <div class="container">
            <?= $this->renderSection('content') ?>
        </div>
    </main>

    <?php if (($showFooter ?? true) === true): ?>
        <?= view('partials/footer') ?>
    <?php endif; ?>

    <?= $this->renderSection('scripts') ?>
</body>
</html>
