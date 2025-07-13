<div class="alert alert-<?= $type ?>">
    <?= $message ?>
    <?php if(isset($stok)): ?>
        <p>Stok Terkini: <?= $stok ?> Liter</p>
    <?php endif ?>
</div>