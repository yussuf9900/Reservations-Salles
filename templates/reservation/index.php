<?php
$totalRes = count($reservations ?? []);
$confirmees = 0;
$annulees = 0;

foreach ($reservations as $r) {
    if ($r->statut === 'confirmée') {
        $confirmees++;
    } elseif ($r->statut === 'annulée') {
        $annulees++;
    }
}
?>

<div class="page-header animate-in">
    <div class="title-wrap">
        <h1>Gestion des Réservations</h1>
        <p class="subtitle">Consultez le calendrier et l'état des réservations universitaires.</p>
    </div>
    <div class="action-wrap">
        <a href="/reservations/create" class="btn btn-primary">
            <?= \App\View\Icons::plus('icon-sm') ?>
            <span>Nouvelle réservation</span>
        </a>
    </div>
</div>

<div class="stats-grid animate-in">
    <div class="stat-card">
        <div class="stat-icon blue">
            <?= \App\View\Icons::calendar('icon-lg') ?>
        </div>
        <div class="stat-info">
            <span class="stat-value"><?= $totalRes ?></span>
            <span class="stat-label">Total Réservations</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">
            <?= \App\View\Icons::checkCircle('icon-lg') ?>
        </div>
        <div class="stat-info">
            <span class="stat-value"><?= $confirmees ?></span>
            <span class="stat-label">Confirmées</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon amber">
            <?= \App\View\Icons::xCircle('icon-lg') ?>
        </div>
        <div class="stat-info">
            <span class="stat-value"><?= $annulees ?></span>
            <span class="stat-label">Annulées</span>
        </div>
    </div>
</div>

<div class="filter-card animate-in">
    <form method="GET" action="/reservations" class="filter-form">
        <div class="filter-group">
            <label for="filter-salle">
                <?= \App\View\Icons::filter('icon-sm') ?>
                <span>Filtrer par salle :</span>
            </label>
            <select id="filter-salle" name="salle_id" class="form-control">
                <option value="">-- Toutes les salles --</option>
                <?php foreach ($salles as $salle): ?>
                    <option value="<?= (int)$salle->id ?>" <?= (isset($salleIdSelectionnee) && $salleIdSelectionnee === (int)$salle->id) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($salle->nom) ?> (<?= htmlspecialchars($salle->batiment) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="action-wrap">
            <button type="submit" class="btn btn-sm btn-primary">
                <?= \App\View\Icons::filter('icon-sm') ?>
                <span>Filtrer</span>
            </button>
            <?php if (!empty($salleIdSelectionnee)): ?>
                <a href="/reservations" class="btn btn-sm btn-outline">
                    <?= \App\View\Icons::refresh('icon-sm') ?>
                    <span>Réinitialiser</span>
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
                    <th>Salle réservée</th>
                    <th>Responsable</th>
                    <th>Motif</th>
                    <th>Début</th>
                    <th>Fin</th>
                    <th>Statut</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($reservations)): ?>
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <?= \App\View\Icons::calendar('empty-icon') ?>
                                <p class="empty-text">Aucune réservation enregistrée pour les critères sélectionnés.</p>
                                <a href="/reservations/create" class="btn btn-sm btn-primary" style="margin-top: 1rem;">
                                    <?= \App\View\Icons::plus('icon-sm') ?>
                                    <span>Effectuer une réservation</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($reservations as $res): ?>
                        <tr>
                            <td>
                                <span class="badge badge-secondary">#<?= (int)$res->id ?></span>
                            </td>
                            <td>
                                <strong>
                                    <a href="/salles/<?= (int)$res->salle_id ?>" class="row-highlight">
                                        <?= htmlspecialchars($res->salle->nom ?? 'Salle #' . $res->salle_id) ?>
                                    </a>
                                </strong>
                                <span class="cell-sub"><?= htmlspecialchars($res->salle->batiment ?? '') ?></span>
                            </td>
                            <td>
                                <div class="cell-meta">
                                    <span class="row-highlight"><?= htmlspecialchars($res->responsable) ?></span>
                                    <span class="cell-sub"><?= htmlspecialchars($res->email) ?></span>
                                </div>
                            </td>
                            <td>
                                <span><?= htmlspecialchars($res->motif) ?></span>
                            </td>
                            <td>
                                <span class="badge badge-secondary">
                                    <?= \App\View\Icons::clock('icon-sm') ?>
                                    <?= htmlspecialchars($res->date_debut->format('d/m/Y H:i')) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-secondary">
                                    <?= \App\View\Icons::clock('icon-sm') ?>
                                    <?= htmlspecialchars($res->date_fin->format('d/m/Y H:i')) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($res->statut === 'confirmée'): ?>
                                    <span class="badge badge-success">
                                        <span class="status-dot"></span>
                                        Confirmée
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-danger">
                                        <span class="status-dot"></span>
                                        Annulée
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-right">
                                <div class="table-actions">
                                    <a href="/reservations/<?= (int)$res->id ?>" class="btn btn-xs btn-outline" title="Détails">
                                        <?= \App\View\Icons::eye('icon-sm') ?>
                                        <span>Détails</span>
                                    </a>
                                    <?php if ($res->statut === 'confirmée'): ?>
                                        <form method="POST" action="/reservations/<?= (int)$res->id ?>/cancel" style="display:inline;">
                                            <button type="submit" class="btn btn-xs btn-danger" title="Annuler">
                                                <?= \App\View\Icons::trash('icon-sm') ?>
                                                <span>Annuler</span>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
