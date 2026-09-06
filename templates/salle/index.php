<div class="page-header">
    <div class="title-wrap">
        <h1>Gestion des Salles</h1>
        <p class="subtitle">Consultez, modifiez et gérez la disponibilité des salles universitaires.</p>
    </div>
    <div class="action-wrap">
        <a href="/salles/create" class="btn btn-primary">+ Ajouter une salle</a>
    </div>
</div>

<div class="card table-card">
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
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
                    <td colspan="7" class="text-center empty-state">Aucune salle enregistrée.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($salles as $salle): ?>
                    <tr>
                        <td>#<?= (int)$salle->id ?></td>
                        <td><strong><a href="/salles/<?= (int)$salle->id ?>"><?= htmlspecialchars($salle->nom) ?></a></strong></td>
                        <td><?= htmlspecialchars($salle->batiment) ?></td>
                        <td><span class="badge badge-secondary"><?= (int)$salle->capacite ?> places</span></td>
                        <td><span class="badge badge-info"><?= htmlspecialchars($salle->type) ?></span></td>
                        <td>
                            <?php if ($salle->active): ?>
                                <span class="badge badge-success">Active</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-right table-actions">
                            <a href="/salles/<?= (int)$salle->id ?>" class="btn btn-xs btn-outline">Détails</a>
                            <a href="/salles/<?= (int)$salle->id ?>/edit" class="btn btn-xs btn-secondary">Modifier</a>
                            <a href="/reservations/create?salle_id=<?= (int)$salle->id ?>" class="btn btn-xs btn-primary">Réserver</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
