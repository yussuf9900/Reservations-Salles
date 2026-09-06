<div class="page-header">
    <div class="title-wrap">
        <h1>Gestion des Réservations</h1>
        <p class="subtitle">Consultez l'historique et le calendrier des réservations de l'université.</p>
    </div>
    <div class="action-wrap">
        <a href="/reservations/create" class="btn btn-primary">+ Nouvelle réservation</a>
    </div>
</div>

<div class="card filter-card">
    <form method="GET" action="/reservations" class="filter-form">
        <div class="form-inline">
            <label for="filter-salle">Filtrer par salle :</label>
            <select id="filter-salle" name="salle_id" class="form-control" onchange="this.form.submit()">
                <option value="">-- Toutes les salles --</option>
                <?php foreach ($salles as $salle): ?>
                    <option value="<?= (int)$salle->id ?>" <?= (isset($salleIdSelectionnee) && $salleIdSelectionnee === (int)$salle->id) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($salle->nom) ?> (<?= htmlspecialchars($salle->batiment) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
            <noscript><button type="submit" class="btn btn-sm btn-secondary">Filtrer</button></noscript>
            <?php if (!empty($salleIdSelectionnee)): ?>
                <a href="/reservations" class="btn btn-sm btn-outline">Réinitialiser</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="card table-card">
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Salle</th>
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
                    <td colspan="8" class="text-center empty-state">Aucune réservation trouvée.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($reservations as $res): ?>
                    <tr>
                        <td>#<?= (int)$res->id ?></td>
                        <td>
                            <strong>
                                <a href="/salles/<?= (int)$res->salle_id ?>"><?= htmlspecialchars($res->salle->nom ?? 'Salle #' . $res->salle_id) ?></a>
                            </strong>
                        </td>
                        <td>
                            <div><?= htmlspecialchars($res->responsable) ?></div>
                            <small class="text-muted"><?= htmlspecialchars($res->email) ?></small>
                        </td>
                        <td><?= htmlspecialchars($res->motif) ?></td>
                        <td><?= htmlspecialchars($res->date_debut->format('d/m/Y H:i')) ?></td>
                        <td><?= htmlspecialchars($res->date_fin->format('d/m/Y H:i')) ?></td>
                        <td>
                            <?php if ($res->statut === 'confirmée'): ?>
                                <span class="badge badge-success">Confirmée</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Annulée</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-right table-actions">
                            <a href="/reservations/<?= (int)$res->id ?>" class="btn btn-xs btn-outline">Détails</a>
                            <?php if ($res->statut === 'confirmée'): ?>
                                <form method="POST" action="/reservations/<?= (int)$res->id ?>/cancel" style="display:inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette réservation ?');">
                                    <button type="submit" class="btn btn-xs btn-danger">Annuler</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
