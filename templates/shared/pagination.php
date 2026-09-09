<?php if (isset($paginator) && $paginator->lastPage() > 1): ?>
    <div class="pagination-wrapper">
        <div class="text-sm text-muted">
            Affichage de <strong><?= count($paginator->items()) ?></strong> sur <strong><?= $paginator->total() ?></strong> résultats (Page <strong><?= $paginator->currentPage() ?></strong> / <strong><?= $paginator->lastPage() ?></strong>)
        </div>
        <ul class="pagination">
            <?php
            $params = $queryParams ?? [];
            ?>

            <?php if ($paginator->hasPreviousPage()): ?>
                <?php $params['page'] = $paginator->previousPage(); ?>
                <li>
                    <a href="<?= htmlspecialchars($baseUrl) . '?' . http_build_query($params) ?>" class="btn btn-sm btn-outline">
                        Précédent
                    </a>
                </li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $paginator->lastPage(); $i++): ?>
                <?php $params['page'] = $i; ?>
                <li>
                    <a href="<?= htmlspecialchars($baseUrl) . '?' . http_build_query($params) ?>" class="btn btn-sm <?= $i === $paginator->currentPage() ? 'btn-primary' : 'btn-outline' ?>">
                        <?= $i ?>
                    </a>
                </li>
            <?php endfor; ?>

            <?php if ($paginator->hasNextPage()): ?>
                <?php $params['page'] = $paginator->nextPage(); ?>
                <li>
                    <a href="<?= htmlspecialchars($baseUrl) . '?' . http_build_query($params) ?>" class="btn btn-sm btn-outline">
                        Suivant
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
<?php endif; ?>
