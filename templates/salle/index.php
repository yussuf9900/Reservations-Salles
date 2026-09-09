<?php
$totalSalles = $paginator->total() ?? count($salles ?? []);
$sallesActives = 0;
$capaciteTotale = 0;
foreach ($salles as $s) {
    if ($s->active) {
        $sallesActives++;
    }
    $capaciteTotale += (int)$s->capacite;
}
?>

<div class="page-header animate-in">
    <div class="title-wrap">
        <h1>Gestion des Salles</h1>
        <p class="subtitle">Consultez, administrez et filtrez les espaces universitaires disponibles.</p>
    </div>
    <?php if (!empty($isAdmin) || (!empty($currentUser) && $currentUser->isAdmin())): ?>
        <div class="action-wrap">
            <a href="/salles/create" class="btn btn-primary">
                <?= \App\View\Icons::plus('icon-sm') ?>
                <span>Ajouter une salle</span>
            </a>
        </div>
    <?php endif; ?>
</div>

<div class="page-stats-strip animate-in">
    <div class="stat-chip">
        <?= \App\View\Icons::building('icon-sm') ?>
        <span>Total :</span>
        <span class="stat-chip-num"><?= $totalSalles ?></span>
    </div>
    <div class="stat-chip">
        <?= \App\View\Icons::checkCircle('icon-sm text-success') ?>
        <span>Actives :</span>
        <span class="stat-chip-num"><?= $sallesActives ?></span>
    </div>
    <div class="stat-chip">
        <?= \App\View\Icons::users('icon-sm') ?>
        <span>Capacité totale :</span>
        <span class="stat-chip-num"><?= $capaciteTotale ?> places</span>
    </div>
</div>

<div class="card filter-card animate-in">
    <form method="GET" action="/salles" class="filter-toolbar">
        <div class="filter-group filter-search">
            <label for="q" class="filter-label">Recherche</label>
            <div class="filter-input-wrap">
                <span class="input-icon"><?= \App\View\Icons::search('icon-sm') ?></span>
                <input type="text" id="q" name="q" class="filter-control" value="<?= htmlspecialchars($criteres['q'] ?? '') ?>" placeholder="Nom ou bâtiment...">
            </div>
        </div>

        <div class="filter-group">
            <label for="type" class="filter-label">Type</label>
            <select id="type" name="type" class="filter-control">
                <option value="">Tous les types</option>
                <option value="cours" <?= ($criteres['type'] ?? '') === 'cours' ? 'selected' : '' ?>>Cours</option>
                <option value="informatique" <?= ($criteres['type'] ?? '') === 'informatique' ? 'selected' : '' ?>>Informatique</option>
                <option value="laboratoire" <?= ($criteres['type'] ?? '') === 'laboratoire' ? 'selected' : '' ?>>Laboratoire</option>
                <option value="amphitheatre" <?= ($criteres['type'] ?? '') === 'amphitheatre' ? 'selected' : '' ?>>Amphithéâtre</option>
                <option value="reunion" <?= ($criteres['type'] ?? '') === 'reunion' ? 'selected' : '' ?>>Réunion</option>
            </select>
        </div>

        <div class="filter-group">
            <label for="capacite_min" class="filter-label">Capacité min.</label>
            <input type="number" id="capacite_min" name="capacite_min" class="filter-control" min="1" value="<?= htmlspecialchars((string)($criteres['capacite_min'] ?? '')) ?>" placeholder="ex: 30">
        </div>

        <div class="filter-group">
            <label for="active" class="filter-label">Disponibilité</label>
            <select id="active" name="active" class="filter-control">
                <option value="">Toutes</option>
                <option value="1" <?= ($criteres['active'] ?? '') === '1' ? 'selected' : '' ?>>Actives</option>
                <option value="0" <?= ($criteres['active'] ?? '') === '0' ? 'selected' : '' ?>>Inactives</option>
            </select>
        </div>

        <div class="filter-actions">
            <button type="submit" class="btn btn-primary" title="Appliquer les filtres">
                <?= \App\View\Icons::filter('icon-sm') ?>
                <span>Filtrer</span>
            </button>
            <?php if (!empty($queryParams)): ?>
                <a href="/salles" class="btn btn-ghost" title="Réinitialiser les filtres">
                    <?= \App\View\Icons::refresh('icon-sm') ?>
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="card table-card animate-in">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th class="col-id">#</th>
                    <th>Salle &amp; Bâtiment</th>
                    <th class="col-type">Type</th>
                    <th class="col-capacity">Capacité</th>
                    <th class="col-status">Statut</th>
                    <th class="col-actions text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($salles)): ?>
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <?= \App\View\Icons::building('empty-icon') ?>
                                <p class="empty-text">Aucune salle ne correspond aux critères de recherche.</p>
                                <a href="/salles" class="btn btn-sm btn-outline">
                                    <span>Réinitialiser les filtres</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($salles as $salle): ?>
                        <?php
                        $typeClass = match ($salle->type ?? '') {
                            'cours' => 'badge-type-cours',
                            'informatique' => 'badge-type-informatique',
                            'laboratoire' => 'badge-type-laboratoire',
                            'amphitheatre' => 'badge-type-amphitheatre',
                            'reunion' => 'badge-type-reunion',
                            default => 'badge-secondary',
                        };
                        ?>
                        <tr>
                            <td>
                                <span class="badge badge-secondary">#<?= (int)$salle->id ?></span>
                            </td>
                            <td>
                                <div class="cell-entity">
                                    <a href="/salles/<?= (int)$salle->id ?>" class="cell-entity-title">
                                        <?= htmlspecialchars($salle->nom) ?>
                                    </a>
                                    <span class="cell-entity-sub">
                                        <?= \App\View\Icons::building('icon-xs') ?>
                                        <?= htmlspecialchars($salle->batiment) ?>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <span class="badge <?= $typeClass ?>">
                                    <?= htmlspecialchars($salle->type) ?>
                                </span>
                            </td>
                            <td>
                                <div class="cell-capacity">
                                    <?= \App\View\Icons::users('icon-sm text-muted') ?>
                                    <span class="capacity-val"><?= (int)$salle->capacite ?></span>
                                    <span class="capacity-unit">places</span>
                                </div>
                            </td>
                            <td>
                                <?php if ($salle->active): ?>
                                    <span class="badge badge-success">
                                        <span class="status-dot"></span>
                                        Active
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-danger">
                                        <span class="status-dot"></span>
                                        Inactive
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-right">
                                <div class="table-actions">
                                    <a href="/salles/<?= (int)$salle->id ?>" class="btn btn-xs btn-ghost" title="Consulter la fiche détaillée">
                                        <?= \App\View\Icons::eye('icon-sm') ?>
                                        <span>Détails</span>
                                    </a>
                                    <?php if (!empty($isAdmin) || (!empty($currentUser) && $currentUser->isAdmin())): ?>
                                        <a href="/salles/<?= (int)$salle->id ?>/edit" class="btn btn-xs btn-secondary" title="Modifier les informations">
                                            <?= \App\View\Icons::edit('icon-sm') ?>
                                            <span>Modifier</span>
                                        </a>
                                    <?php endif; ?>
                                    <a href="/reservations/create?salle_id=<?= (int)$salle->id ?>" class="btn btn-xs btn-primary" title="Effectuer une réservation">
                                        <?= \App\View\Icons::calendar('icon-sm') ?>
                                        <span>Réserver</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php require dirname(__DIR__) . '/shared/pagination.php'; ?>
</div>
