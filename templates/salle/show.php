<div class="page-header">
    <div class="title-wrap">
        <h1>Détail de la salle : <?= htmlspecialchars($salle->nom) ?></h1>
        <p class="subtitle">Bâtiment <?= htmlspecialchars($salle->batiment) ?> &bull; Capacité <?= (int)$salle->capacite ?> personnes</p>
    </div>
    <div class="action-wrap">
        <a href="/salles" class="btn btn-outline">&larr; Retour à la liste</a>
        <a href="/salles/<?= (int)$salle->id ?>/edit" class="btn btn-secondary">Modifier</a>
        <a href="/reservations/create?salle_id=<?= (int)$salle->id ?>" class="btn btn-primary">+ Réserver cette salle</a>
    </div>
</div>

<div class="grid grid-2">
    <div class="card">
        <h3>Informations Générales</h3>
        <ul class="info-list">
            <li><strong>Identifiant :</strong> #<?= (int)$salle->id ?></li>
            <li><strong>Nom :</strong> <?= htmlspecialchars($salle->nom) ?></li>
            <li><strong>Bâtiment :</strong> <?= htmlspecialchars($salle->batiment) ?></li>
            <li><strong>Capacité :</strong> <?= (int)$salle->capacite ?> places</li>
            <li><strong>Type d'usage :</strong> <span class="badge badge-info"><?= htmlspecialchars($salle->type) ?></span></li>
            <li><strong>Disponibilité :</strong> 
                <?php if ($salle->active): ?>
                    <span class="badge badge-success">Salle active</span>
                <?php else: ?>
                    <span class="badge badge-danger">Salle inactive</span>
                <?php endif; ?>
            </li>
            <li><strong>Créée le :</strong> <?= $salle->created_at ? htmlspecialchars($salle->created_at->format('d/m/Y H:i')) : '-' ?></li>
        </ul>
    </div>

    <div class="card">
        <h3>Réservations Récentes</h3>
        <?php $reservations = $salle->reservations()->orderBy('date_debut', 'desc')->get(); ?>
        <?php if ($reservations->isEmpty()): ?>
            <p class="empty-state">Aucune réservation pour cette salle actuellement.</p>
        <?php else: ?>
            <ul class="reservation-mini-list">
                <?php foreach ($reservations as $res): ?>
                    <li class="reservation-item">
                        <div>
                            <strong><?= htmlspecialchars($res->responsable) ?></strong> (<?= htmlspecialchars($res->motif) ?>)
                            <div class="text-muted text-sm">
                                <?= htmlspecialchars($res->date_debut->format('d/m/Y H:i')) ?> &rarr; <?= htmlspecialchars($res->date_fin->format('H:i')) ?>
                            </div>
                        </div>
                        <div>
                            <?php if ($res->statut === 'confirmée'): ?>
                                <span class="badge badge-success">Confirmée</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Annulée</span>
                            <?php endif; ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>
