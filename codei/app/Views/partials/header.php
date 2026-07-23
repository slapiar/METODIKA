<?php
$navigation = $navigation ?? [
    ['key' => 'home', 'label' => 'Domov', 'url' => site_url('/')],
    ['key' => 'diagnostics', 'label' => 'Diagnostika', 'url' => site_url('diagnostics/database')],
    ['key' => 'docs', 'label' => 'CodeIgniter Docs', 'url' => 'https://codeigniter.com/user_guide/', 'target' => '_blank'],
];
?>
<header class="site-header">
    <div class="container topbar">
        <a class="brand" href="<?= site_url('/') ?>">METODIKA</a>
        <nav class="nav" aria-label="Hlavna navigacia">
            <?php foreach ($navigation as $item): ?>
                <?php
                $itemKey = (string) ($item['key'] ?? '');
                $isActive = $itemKey !== '' && $itemKey === ($activeNav ?? '');
                ?>
                <a href="<?= esc((string) $item['url']) ?>"
                   <?= isset($item['target']) ? 'target="' . esc((string) $item['target']) . '" rel="noopener"' : '' ?>
                   class="<?= $isActive ? 'active' : '' ?>">
                    <?= esc((string) $item['label']) ?>
                </a>
            <?php endforeach; ?>
        </nav>
    </div>
    <?php if (($subtitle ?? '') !== ''): ?>
        <div class="container hero">
            <p><?= esc((string) $subtitle) ?></p>
        </div>
    <?php endif; ?>
</header>
