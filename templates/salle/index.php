<?php
$totalSalles = count($salles ?? []);
$sallesActives = 0;
$capaciteTotale = 0;
$typesDistincts = [];

foreach ($salles as $s) {
    if ($s->active) {
        $sallesActives++;
    }
    $capaciteTotale += (int)$s->capacite;
    $typesDistincts[$s->type] = true;
}
$nbTypes = count($typesDistincts);
?>

<div class="page-header animate-in">
    <div class="title-wrap">
        <h1>Gestion des Salles</h1>
        <p class="subtitle">Consultez, administrez et visualisez l'état des salles universitaires.</p>
    </div>
    <div class="action-wrap">
        <a href="/salles/create" class="btn btn-primary">
            <?= \App\View\Icons::plus('icon-sm') ?>
            <span>Ajouter une salle</span>
        </a>
    </div>
</div>

<div class="stats-grid animate-in">
    <div class="stat-card">
        <div class="stat-icon blue">
            <?= \App\View\Icons::building('icon-lg') ?>
        </div>
        <div class="stat-info">
            <span class="stat-value"><?= $totalSalles ?></span>
            <span class="stat-label">Total Salles</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">
            <?= \App\View\Icons::checkCircle('icon-lg') ?>
        </div>
        <div class="stat-info">
            <span class="stat-value"><?= $sallesActives ?></span>
            <span class="stat-label">Salles Actives</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon amber">
            <?= \App\View\Icons::users('icon-lg') ?>
        </div>
        <div class="stat-info">
            <span class="stat-value"><?= number_format($capaciteTotale, 0, ',', ' ') ?></span>
            <span class="stat-label">Capacité Totale</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon slate">
            <?= \App\View\Icons::tag('icon-lg') ?>
        </div>
        <div class="stat-info">
            <span class="stat-value"><?= $nbTypes ?></span>
            <span class="stat-label">Types d'Espaces</span>
        </div>
    </div>
</div>

<div class="card table-card animate-in">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Identifiant</th>
                    <th>Nom de la salle</th>
                    <th>Bâtiment</th>
                    <th>Capacité</th>
                    <th>Type</th>
                    <th>Statut</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($salles)): ?>
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <?= \App\View\Icons::building('empty-icon') ?>
                                <p class="empty-text">Aucune salle universitaire enregistrée dans le système.</p>
                                <a href="/salles/create" class="btn btn-sm btn-primary" style="margin-top: 1rem;">
                                    <?= \App\View\Icons::plus('icon-sm') ?>
                                    <span>Ajouter la première salle</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($salles as $salle): ?>
                        <tr>
                            <td>
                                <span class="badge badge-secondary">#<?= (int)$salle->id ?></span>
                            </td>
                            <td>
                                <strong>
                                    <a href="/salles/<?= (int)$salle->id ?>" class="row-highlight">
                                        <?= htmlspecialchars($salle->nom) ?>
                                    </a>
                                </strong>
                            </td>
                            <td>
                                <span class="text-muted"><?= htmlspecialchars($salle->batiment) ?></span>
                            </td>
                            <td>
                                <span class="badge badge-secondary">
                                    <?= \App\View\Icons::users('icon-sm') ?>
                                    <?= (int)$salle->capacite ?> places
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-info">
                                    <?= htmlspecialchars($salle->type) ?>
                                </span>
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
                                    <a href="/salles/<?= (int)$salle->id ?>" class="btn btn-xs btn-outline" title="Consulter">
                                        <?= \App\View\Icons::eye('icon-sm') ?>
                                        <span>Détails</span>
                                    </a>
                                    <a href="/salles/<?= (int)$salle->id ?>/edit" class="btn btn-xs btn-secondary" title="Modifier">
                                        <?= \App\View\Icons::edit('icon-sm') ?>
                                        <span>Modifier</span>
                                    </a>
                                    <a href="/reservations/create?salle_id=<?= (int)$salle->id ?>" class="btn btn-xs btn-primary" title="Réserver">
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
</div>
