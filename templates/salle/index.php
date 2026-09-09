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
        <p class="subtitle">Consultez, administrez et filtrez les salles universitaires.</p>
    </div>
    <div class="action-wrap">
        <a href="/salles/create" class="btn btn-primary">
            <?= \App\View\Icons::plus('icon-sm') ?>
            <span>Ajouter une salle</span>
        </a>
    </div>
</div>

<div class="card filter-card">
    <form method="GET" action="/salles" class="filter-grid">
        <div class="form-group" style="margin-bottom: 0;">
            <label for="q" class="form-label" style="font-size: 0.8rem;">Recherche (nom, bâtiment)</label>
            <input type="text" id="q" name="q" class="form-control" value="<?= htmlspecialchars($criteres['q'] ?? '') ?>" placeholder="ex: Amphithéâtre, Bâtiment B...">
        </div>

        <div class="form-group" style="margin-bottom: 0;">
            <label for="type" class="form-label" style="font-size: 0.8rem;">Type de salle</label>
            <select id="type" name="type" class="form-control">
                <option value="">Tous les types</option>
                <option value="cours" <?= ($criteres['type'] ?? '') === 'cours' ? 'selected' : '' ?>>Cours</option>
                <option value="informatique" <?= ($criteres['type'] ?? '') === 'informatique' ? 'selected' : '' ?>>Informatique</option>
                <option value="laboratoire" <?= ($criteres['type'] ?? '') === 'laboratoire' ? 'selected' : '' ?>>Laboratoire</option>
                <option value="amphitheatre" <?= ($criteres['type'] ?? '') === 'amphitheatre' ? 'selected' : '' ?>>Amphithéâtre</option>
                <option value="reunion" <?= ($criteres['type'] ?? '') === 'reunion' ? 'selected' : '' ?>>Réunion</option>
            </select>
        </div>

        <div class="form-group" style="margin-bottom: 0;">
            <label for="capacite_min" class="form-label" style="font-size: 0.8rem;">Capacité minimale</label>
            <input type="number" id="capacite_min" name="capacite_min" class="form-control" min="1" value="<?= htmlspecialchars((string)($criteres['capacite_min'] ?? '')) ?>" placeholder="ex: 30">
        </div>

        <div class="form-group" style="margin-bottom: 0;">
            <label for="active" class="form-label" style="font-size: 0.8rem;">Disponibilité</label>
            <select id="active" name="active" class="form-control">
                <option value="">Toutes</option>
                <option value="1" <?= ($criteres['active'] ?? '') === '1' ? 'selected' : '' ?>>Actives uniquement</option>
                <option value="0" <?= ($criteres['active'] ?? '') === '0' ? 'selected' : '' ?>>Inactives</option>
            </select>
        </div>

        <div class="filter-actions">
            <button type="submit" class="btn btn-primary" style="height: 42px;">
                <?= \App\View\Icons::filter('icon-sm') ?>
                <span>Filtrer</span>
            </button>
            <?php if (!empty($queryParams)): ?>
                <a href="/salles" class="btn btn-outline" style="height: 42px;" title="Réinitialiser">
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
                                <p class="empty-text">Aucune salle ne correspond aux critères de recherche.</p>
                                <a href="/salles" class="btn btn-sm btn-outline" style="margin-top: 1rem;">
                                    <span>Réinitialiser les filtres</span>
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
                                <strong class="cell-room-title">
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

    <?php require dirname(__DIR__) . '/shared/pagination.php'; ?>
</div>
