<?php
$title = 'METODIKA | Home';
$activeNav = 'home';
$subtitle = 'CodeIgniter ' . CodeIgniter\CodeIgniter::CI_VERSION . ' runtime';
?>

<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<section class="panel">
    <h1>Welcome to METODIKA</h1>
    <p>This installation now uses a shared layout with reusable navigation and footer components.</p>
    <p>View file: <strong>app/Views/welcome_message.php</strong></p>
    <p>Controller: <strong>app/Controllers/Home.php</strong></p>
</section>

<section class="grid" aria-label="Quick links">
    <article class="card">
        <h2>Diagnostics</h2>
        <p>Run safe read-only diagnostics for external environment and database capabilities.</p>
        <a href="<?= site_url('diagnostics/database') ?>">Open diagnostics</a>
    </article>
    <article class="card">
        <h2>User Guide</h2>
        <p>Framework guides and references for routing, views, services, and deployment.</p>
        <a href="https://codeigniter.com/user_guide/" target="_blank" rel="noopener">Open docs</a>
    </article>
    <article class="card">
        <h2>Community</h2>
        <p>Forums and channels for troubleshooting and best practices.</p>
        <a href="https://forum.codeigniter.com/" target="_blank" rel="noopener">Open community</a>
    </article>
</section>
<?= $this->endSection() ?>
