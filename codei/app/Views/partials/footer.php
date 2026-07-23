<?php
$currentYear = isset($currentYear) ? (int) $currentYear : (int) date('Y');

if (! isset($releaseVersion)) {
    $releaseVersion = null;
    $releaseFiles = [
        dirname(__DIR__, 3) . '/deploy/RELEASE_VERSION.txt',
        dirname(__DIR__, 3) . '/RELEASE_VERSION',
        dirname(__DIR__, 4) . '/RELEASE_VERSION',
    ];

    foreach ($releaseFiles as $releaseFile) {
        if (is_file($releaseFile) && is_readable($releaseFile)) {
            $value = trim((string) file_get_contents($releaseFile));
            if ($value !== '') {
                $releaseVersion = $value;
                break;
            }
        }
    }
}
?>
<footer id="appFooter" class="site-footer">
    <div class="container">
        <div>
            © PIAR Team <?= htmlspecialchars((string) $currentYear, ENT_QUOTES, 'UTF-8'); ?>
            <?php if ($releaseVersion !== null): ?>
                · v<?= htmlspecialchars($releaseVersion, ENT_QUOTES, 'UTF-8'); ?>
            <?php else: ?>
                · VERZIA NEDOSTUPNÁ
            <?php endif; ?>
            | Page rendered in {elapsed_time}s | Memory {memory_usage} MB
        </div>
    </div>
</footer>
