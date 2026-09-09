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
        <p class="subtitle">Consultez le calendrier, effectuez une recherche et gérez les créneaux.</p>
    </div>
    <div class="action-wrap">
        <a href="/reservations/create" class="btn btn-primary">
            <?= \App\View\Icons::plus('icon-sm') ?>
            <span>Nouvelle réservation</span>
        </a>
    </div>
</div>

<div class="card filter-card">
    <form method="GET" action="/reservations" class="filter-grid">
        <div class="form-group" style="margin-bottom: 0;">
            <label for="filter-salle" class="form-label" style="font-size: 0.8rem;">Filtrer par salle</label>
            <select id="filter-salle" name="salle_id" class="form-control">
                <option value="">Toutes les salles</option>
                <?php foreach ($salles as $salle): ?>
                    <option value="<?= (int)$salle->id ?>" <?= ($criteres['salle_id'] ?? '') == $salle->id ? 'selected' : '' ?>>
                        <?= htmlspecialchars($salle->nom) ?> (<?= htmlspecialchars($salle->batiment) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group" style="margin-bottom: 0;">
            <label for="statut" class="form-label" style="font-size: 0.8rem;">Statut</label>
            <select id="statut" name="statut" class="form-control">
                <option value="">Tous les statuts</option>
                <option value="confirmée" <?= ($criteres['statut'] ?? '') === 'confirmée' ? 'selected' : '' ?>>Confirmée</option>
                <option value="annulée" <?= ($criteres['statut'] ?? '') === 'annulée' ? 'selected' : '' ?>>Annulée</option>
            </select>
        </div>

        <div class="form-group" style="margin-bottom: 0;">
            <label for="q" class="form-label" style="font-size: 0.8rem;">Responsable / Motif</label>
            <input type="text" id="q" name="q" class="form-control" value="<?= htmlspecialchars($criteres['q'] ?? '') ?>" placeholder="ex: Nom, email ou motif...">
        </div>

        <div class="form-group" style="margin-bottom: 0;">
            <label for="date" class="form-label" style="font-size: 0.8rem;">Date du créneau</label>
            <input type="date" id="date" name="date" class="form-control" value="<?= htmlspecialchars($criteres['date'] ?? '') ?>">
        </div>

        <div class="filter-actions">
            <button type="submit" class="btn btn-primary" style="height: 42px;">
                <?= \App\View\Icons::filter('icon-sm') ?>
                <span>Filtrer</span>
            </button>
            <?php if (!empty($queryParams)): ?>
                <a href="/reservations" class="btn btn-outline" style="height: 42px;" title="Réinitialiser">
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
                                <p class="empty-text">Aucune réservation ne correspond aux critères sélectionnés.</p>
                                <a href="/reservations" class="btn btn-sm btn-outline" style="margin-top: 1rem;">
                                    <span>Réinitialiser les filtres</span>
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
                                <strong class="cell-room-title">
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
                                <span class="cell-motif" title="<?= htmlspecialchars($res->motif) ?>"><?= htmlspecialchars($res->motif) ?></span>
                            </td>
                            <td style="white-space: nowrap;">
                                <span class="badge badge-secondary">
                                    <?= \App\View\Icons::clock('icon-sm') ?>
                                    <?= htmlspecialchars($res->date_debut instanceof \DateTimeInterface ? $res->date_debut->format('d/m/Y H:i') : (string)$res->date_debut) ?>
                                </span>
                            </td>
                            <td style="white-space: nowrap;">
                                <span class="badge badge-secondary">
                                    <?= \App\View\Icons::clock('icon-sm') ?>
                                    <?= htmlspecialchars($res->date_fin instanceof \DateTimeInterface ? $res->date_fin->format('d/m/Y H:i') : (string)$res->date_fin) ?>
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
                                            <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
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

    <?php require dirname(__DIR__) . '/shared/pagination.php'; ?>
</div>
