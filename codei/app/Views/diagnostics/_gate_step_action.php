<div class="actions">
    <form method="post" action="<?= site_url('diagnostics/database/create-test-step') ?>">
        <?= csrf_field() ?>
        <button type="submit">Vytvoriť testovací krok</button>
    </form>
</div>
