<?php if ($pager->getPageCount() > 1): ?>
    <ul class="pagination pagination-sm m-0 float-right">
        <?php if ($pager->hasPreviousPage()): ?>
            <li class="page-item">
                <a href="<?= $pager->getFirst() ?>" class="page-link" aria-label="First">
                    <span aria-hidden="true">First</span>
                </a>
            </li>
            <li class="page-item">
                <a href="<?= $pager->getPreviousPage() ?>" class="page-link" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
        <?php endif; ?>

        <?php foreach ($pager->links() as $link): ?>
            <li class="page-item <?= $link['active'] ? 'active' : '' ?>">
                <a href="<?= $link['uri'] ?>" class="page-link">
                    <?= $link['title'] ?>
                </a>
            </li>
        <?php endforeach; ?>

        <?php if ($pager->hasNextPage()): ?>
            <li class="page-item">
                <a href="<?= $pager->getNextPage() ?>" class="page-link" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
            <li class="page-item">
                <a href="<?= $pager->getLast() ?>" class="page-link" aria-label="Last">
                    <span aria-hidden="true">Last</span>
                </a>
            </li>
        <?php endif; ?>
    </ul>
<?php endif; ?>