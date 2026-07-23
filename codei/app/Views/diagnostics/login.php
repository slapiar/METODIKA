<?php
$title = 'METODIKA | DB diagnostika';
$activeNav = 'diagnostics';
$showFooter = false;
?>

<?= $this->extend('layouts/app') ?>

<?= $this->section('meta') ?>
<meta name="robots" content="noindex,nofollow,noarchive">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="panel">
    <h1>Diagnostika databazy METODIKA</h1>
    <p>Pristup je povoleny iba po serverovom overeni diagnostickeho tokenu.</p>
    <form method="post" action="<?= site_url('diagnostics/database/login') ?>" autocomplete="off">
        <?= csrf_field() ?>
        <label for="diagnostics_token">Diagnosticky token</label>
        <input id="diagnostics_token" name="diagnostics_token" type="password" required>
        <div class="actions">
            <button type="submit">Overit token</button>
        </div>
    </form>
    <p class="notice">Diagnostika je iba citacia. Migracie neboli spustene.</p>
</section>
<?= $this->endSection() ?>
