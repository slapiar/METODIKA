<div class="actions">
    <form method="post" action="<?= site_url('diagnostics/database/create-test-evidence') ?>">
        <?= csrf_field() ?>
        <button type="submit">Vytvoriť testovací dôkaz</button>
    </form>
</div>
