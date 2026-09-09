<?php
$totalRes = $paginator->total() ?? count($reservations ?? []);
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
        <p class="subtitle">Consultez l'historique des réservations, filtrez et gérez les créneaux horaires.</p>
    </div>
    <div class="action-wrap">
        <a href="/reservations/create" class="btn btn-primary">
            <?= \App\View\Icons::plus('icon-sm') ?>
            <span>Nouvelle réservation</span>
        </a>
    </div>
</div>

<div class="page-stats-strip animate-in">
    <div class="stat-chip">
        <?= \App\View\Icons::calendar('icon-sm') ?>
        <span>Total :</span>
        <span class="stat-chip-num"><?= $totalRes ?></span>
    </div>
    <div class="stat-chip">
        <?= \App\View\Icons::checkCircle('icon-sm text-success') ?>
        <span>Confirmées :</span>
        <span class="stat-chip-num"><?= $confirmees ?></span>
    </div>
    <div class="stat-chip">
        <?= \App\View\Icons::xCircle('icon-sm text-danger') ?>
        <span>Annulées :</span>
        <span class="stat-chip-num"><?= $annulees ?></span>
    </div>
</div>

<div class="card filter-card animate-in">
    <form method="GET" action="/reservations" class="filter-toolbar">
        <div class="filter-group">
            <label for="filter-salle" class="filter-label">Salle</label>
            <select id="filter-salle" name="salle_id" class="filter-control">
                <option value="">Toutes les salles</option>
                <?php foreach ($salles as $salle): ?>
                    <option value="<?= (int)$salle->id ?>" <?= ($criteres['salle_id'] ?? '') == $salle->id ? 'selected' : '' ?>>
                        <?= htmlspecialchars($salle->nom) ?> (<?= htmlspecialchars($salle->batiment) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="filter-group">
            <label for="statut" class="filter-label">Statut</label>
            <select id="statut" name="statut" class="filter-control">
                <option value="">Tous les statuts</option>
                <option value="confirmée" <?= ($criteres['statut'] ?? '') === 'confirmée' ? 'selected' : '' ?>>Confirmée</option>
                <option value="annulée" <?= ($criteres['statut'] ?? '') === 'annulée' ? 'selected' : '' ?>>Annulée</option>
            </select>
        </div>

        <div class="filter-group filter-search">
            <label for="q" class="filter-label">Responsable / Motif</label>
            <div class="filter-input-wrap">
                <span class="input-icon"><?= \App\View\Icons::search('icon-sm') ?></span>
                <input type="text" id="q" name="q" class="filter-control" value="<?= htmlspecialchars($criteres['q'] ?? '') ?>" placeholder="Nom, email ou motif...">
            </div>
        </div>

        <div class="filter-group">
            <label for="date" class="filter-label">Date du créneau</label>
            <input type="date" id="date" name="date" class="filter-control" value="<?= htmlspecialchars($criteres['date'] ?? '') ?>">
        </div>

        <div class="filter-actions">
            <button type="submit" class="btn btn-primary" title="Appliquer les filtres">
                <?= \App\View\Icons::filter('icon-sm') ?>
                <span>Filtrer</span>
            </button>
            <?php if (!empty($queryParams)): ?>
                <a href="/reservations" class="btn btn-ghost" title="Réinitialiser les filtres">
                    <?= \App\View\Icons::refresh('icon-sm') ?>
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="card table-card animate-in">
    <div class="table-responsive">
        <table class="data-table data-table-reservations">
            <thead>
                <tr>
                    <th class="col-id">#</th>
                    <th>Salle réservée</th>
                    <th>Responsable</th>
                    <th>Motif</th>
                    <th class="col-schedule">Créneau horaire</th>
                    <th class="col-status">Statut</th>
                    <th class="col-actions text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($reservations)): ?>
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <?= \App\View\Icons::calendar('empty-icon') ?>
                                <p class="empty-text">Aucune réservation ne correspond aux critères sélectionnés.</p>
                                <a href="/reservations" class="btn btn-sm btn-outline">
                                    <span>Réinitialiser les filtres</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($reservations as $res): ?>
                        <?php
                        $dtDebut = $res->date_debut instanceof \DateTimeInterface ? $res->date_debut : new \DateTimeImmutable((string)$res->date_debut);
                        $dtFin = $res->date_fin instanceof \DateTimeInterface ? $res->date_fin : new \DateTimeImmutable((string)$res->date_fin);
                        $isSameDay = $dtDebut->format('Y-m-d') === $dtFin->format('Y-m-d');
                        ?>
                        <tr>
                            <td>
                                <span class="badge badge-secondary">#<?= (int)$res->id ?></span>
                            </td>
                            <td>
                                <div class="cell-entity">
                                    <a href="/salles/<?= (int)$res->salle_id ?>" class="cell-entity-title">
                                        <?= htmlspecialchars($res->salle->nom ?? 'Salle #' . $res->salle_id) ?>
                                    </a>
                                    <span class="cell-entity-sub">
                                        <?= \App\View\Icons::building('icon-xs') ?>
                                        <?= htmlspecialchars($res->salle->batiment ?? '') ?>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div class="cell-entity">
                                    <span class="cell-entity-title"><?= htmlspecialchars($res->responsable) ?></span>
                                    <span class="cell-entity-sub">
                                        <?= \App\View\Icons::mail('icon-xs') ?>
                                        <?= htmlspecialchars($res->email) ?>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <span class="cell-motif" title="<?= htmlspecialchars($res->motif) ?>"><?= htmlspecialchars($res->motif) ?></span>
                            </td>
                            <td>
                                <div class="cell-schedule">
                                    <span class="schedule-date">
                                        <?= \App\View\Icons::calendar('icon-xs') ?>
                                        <?= $dtDebut->format('d/m/Y') ?>
                                    </span>
                                    <span class="schedule-time">
                                        <?= \App\View\Icons::clock('icon-xs') ?>
                                        <?= $dtDebut->format('H:i') ?> → <?= $dtFin->format('H:i') ?>
                                        <?php if (!$isSameDay): ?>
                                            <span class="text-muted">(<?= $dtFin->format('d/m') ?>)</span>
                                        <?php endif; ?>
                                    </span>
                                </div>
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
                                    <a href="/reservations/<?= (int)$res->id ?>" class="btn btn-xs btn-ghost" title="Détails de la réservation">
                                        <?= \App\View\Icons::eye('icon-sm') ?>
                                        <span>Détails</span>
                                    </a>
                                    <?php 
                                        $canCancelItem = ($isAdmin ?? false) || (!empty($currentUser) && ($currentUser->email === $res->email || $currentUser->nom === $res->responsable));
                                    ?>
                                    <?php if ($res->statut === 'confirmée' && $canCancelItem): ?>
                                        <form method="POST" action="/reservations/<?= (int)$res->id ?>/cancel" class="table-actions">
                                            <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                                            <button type="submit" class="btn btn-xs btn-danger" title="Annuler cette réservation">
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

    <?php require dirname(__DIR__) . '/shared/pagination.php'; ?>
</div>
