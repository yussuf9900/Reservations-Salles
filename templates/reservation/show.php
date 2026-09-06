<div class="page-header">
    <div class="title-wrap">
        <h1>Réservation #<?= (int)$reservation->id ?></h1>
        <p class="subtitle">Salle <?= htmlspecialchars($reservation->salle->nom ?? '') ?> &bull; <?= htmlspecialchars($reservation->motif) ?></p>
    </div>
    <div class="action-wrap">
        <a href="/reservations" class="btn btn-outline">&larr; Retour à la liste</a>
        <?php if ($reservation->statut === 'confirmée'): ?>
            <form method="POST" action="/reservations/<?= (int)$reservation->id ?>/cancel" style="display:inline;" onsubmit="return confirm('Confirmez-vous l\'annulation ?');">
                <button type="submit" class="btn btn-danger">Annuler la réservation</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<div class="card">
    <h3>Détails de la demande</h3>
    <ul class="info-list">
        <li><strong>Numéro :</strong> #<?= (int)$reservation->id ?></li>
        <li><strong>Salle réservée :</strong> 
            <a href="/salles/<?= (int)$reservation->salle_id ?>">
                <?= htmlspecialchars($reservation->salle->nom ?? 'Salle #' . $reservation->salle_id) ?>
            </a> (Bâtiment <?= htmlspecialchars($reservation->salle->batiment ?? '-') ?>)
        </li>
        <li><strong>Responsable :</strong> <?= htmlspecialchars($reservation->responsable) ?></li>
        <li><strong>Email de contact :</strong> <a href="mailto:<?= htmlspecialchars($reservation->email) ?>"><?= htmlspecialchars($reservation->email) ?></a></li>
        <li><strong>Motif :</strong> <?= htmlspecialchars($reservation->motif) ?></li>
        <li><strong>Date et heure de début :</strong> <?= htmlspecialchars($reservation->date_debut->format('d/m/Y à H:i')) ?></li>
        <li><strong>Date et heure de fin :</strong> <?= htmlspecialchars($reservation->date_fin->format('d/m/Y à H:i')) ?></li>
        <li><strong>Durée :</strong> 
            <?php 
                $diffMinutes = ($reservation->date_fin->getTimestamp() - $reservation->date_debut->getTimestamp()) / 60;
                $heures = floor($diffMinutes / 60);
                $minutes = $diffMinutes % 60;
                echo ($heures > 0 ? "{$heures}h " : '') . ($minutes > 0 ? "{$minutes}min" : ($heures === 0.0 ? '0min' : ''));
            ?>
        </li>
        <li><strong>Statut actuel :</strong> 
            <?php if ($reservation->statut === 'confirmée'): ?>
                <span class="badge badge-success">Réservation confirmée</span>
            <?php else: ?>
                <span class="badge badge-danger">Réservation annulée</span>
            <?php endif; ?>
        </li>
        <li><strong>Enregistrée le :</strong> <?= $reservation->created_at ? htmlspecialchars($reservation->created_at->format('d/m/Y H:i')) : '-' ?></li>
    </ul>
</div>
