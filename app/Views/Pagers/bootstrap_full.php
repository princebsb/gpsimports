<?php

/**
 * @var \CodeIgniter\Pager\PagerRenderer $pager
 */

$pager->setSurroundCount(2);
$queryString = $_GET;
unset($queryString['page']);
$baseQueryString = http_build_query($queryString);
$baseQueryString = $baseQueryString ? '&' . $baseQueryString : '';
?>

<nav aria-label="Navegacao de paginas">
    <ul class="pagination pagination-modern justify-content-center mb-0">
        <?php if ($pager->hasPrevious()): ?>
            <li class="page-item">
                <a class="page-link" href="<?= $pager->getFirst() . $baseQueryString ?>" aria-label="Primeira" title="Primeira pagina">
                    <i class="bi bi-chevron-double-left"></i>
                </a>
            </li>
            <li class="page-item">
                <a class="page-link" href="<?= $pager->getPrevious() . $baseQueryString ?>" aria-label="Anterior" title="Pagina anterior">
                    <i class="bi bi-chevron-left"></i>
                </a>
            </li>
        <?php else: ?>
            <li class="page-item disabled">
                <span class="page-link"><i class="bi bi-chevron-double-left"></i></span>
            </li>
            <li class="page-item disabled">
                <span class="page-link"><i class="bi bi-chevron-left"></i></span>
            </li>
        <?php endif; ?>

        <?php foreach ($pager->links() as $link): ?>
            <li class="page-item <?= $link['active'] ? 'active' : '' ?>">
                <a class="page-link" href="<?= $link['uri'] . $baseQueryString ?>">
                    <?= $link['title'] ?>
                </a>
            </li>
        <?php endforeach; ?>

        <?php if ($pager->hasNext()): ?>
            <li class="page-item">
                <a class="page-link" href="<?= $pager->getNext() . $baseQueryString ?>" aria-label="Proxima" title="Proxima pagina">
                    <i class="bi bi-chevron-right"></i>
                </a>
            </li>
            <li class="page-item">
                <a class="page-link" href="<?= $pager->getLast() . $baseQueryString ?>" aria-label="Ultima" title="Ultima pagina">
                    <i class="bi bi-chevron-double-right"></i>
                </a>
            </li>
        <?php else: ?>
            <li class="page-item disabled">
                <span class="page-link"><i class="bi bi-chevron-right"></i></span>
            </li>
            <li class="page-item disabled">
                <span class="page-link"><i class="bi bi-chevron-double-right"></i></span>
            </li>
        <?php endif; ?>
    </ul>
</nav>

<style>
.pagination-modern {
    gap: 4px;
}

.pagination-modern .page-item .page-link {
    border: none;
    border-radius: 8px;
    padding: 10px 16px;
    font-weight: 500;
    color: #495057;
    background-color: #f8f9fa;
    transition: all 0.2s ease;
    min-width: 44px;
    text-align: center;
}

.pagination-modern .page-item .page-link:hover {
    background-color: #e9ecef;
    color: #212529;
    transform: translateY(-1px);
}

.pagination-modern .page-item.active .page-link {
    background-color: #0d6efd;
    color: #fff;
    box-shadow: 0 2px 8px rgba(13, 110, 253, 0.3);
}

.pagination-modern .page-item.disabled .page-link {
    background-color: #f8f9fa;
    color: #adb5bd;
    cursor: not-allowed;
}

.pagination-modern .page-item .page-link i {
    font-size: 0.875rem;
}

@media (max-width: 576px) {
    .pagination-modern .page-item .page-link {
        padding: 8px 12px;
        min-width: 38px;
        font-size: 0.875rem;
    }
}
</style>
