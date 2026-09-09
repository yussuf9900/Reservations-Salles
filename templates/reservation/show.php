<div class="page-header animate-in">
    <div class="title-wrap">
        <h1>Réservation #<?= (int)$reservation->id ?></h1>
        <p class="subtitle"><?= htmlspecialchars($reservation->salle->nom ?? 'Salle #' . $reservation->salle_id) ?> &bull; <?= htmlspecialchars($reservation->motif) ?></p>
    </div>
    <div class="action-wrap">
        <a href="/reservations" class="btn btn-outline">
            <?= \App\View\Icons::arrowLeft('icon-sm') ?>
            <span>Retour à la liste</span>
        </a>
        <?php 
            $canCancelUser = $canCancel ?? ($isAdmin ?? false);
        ?>
        <?php if ($reservation->statut === 'confirmée' && $canCancelUser): ?>
            <form method="POST" action="/reservations/<?= (int)$reservation->id ?>/cancel">
                <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                <button type="submit" class="btn btn-danger">
                    <?= \App\View\Icons::trash('icon-sm') ?>
                    <span>Annuler cette réservation</span>
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>

<div class="card animate-in">
    <div class="card-header">
        <h2 class="card-title">
            <?= \App\View\Icons::calendar('icon') ?>
            <span>Détails du Dossier de Réservation</span>
        </h2>
    </div>
    <ul class="info-list">
        <li class="info-item">
            <span class="info-key">
                <?= \App\View\Icons::tag('icon-sm') ?>
                <span>Numéro de dossier</span>
            </span>
            <span class="info-val">#<?= (int)$reservation->id ?></span>
        </li>
        <li class="info-item">
            <span class="info-key">
                <?= \App\View\Icons::building('icon-sm') ?>
                <span>Salle réservée</span>
            </span>
            <span class="info-val">
                <a href="/salles/<?= (int)$reservation->salle_id ?>">
                    <?= htmlspecialchars($reservation->salle->nom ?? 'Salle #' . $reservation->salle_id) ?>
                </a>
                <span class="text-muted text-sm">(Bâtiment <?= htmlspecialchars($reservation->salle->batiment ?? '-') ?>)</span>
            </span>
        </li>
        <li class="info-item">
            <span class="info-key">
                <?= \App\View\Icons::user('icon-sm') ?>
                <span>Responsable de la demande</span>
            </span>
            <span class="info-val"><?= htmlspecialchars($reservation->responsable) ?></span>
        </li>
        <li class="info-item">
            <span class="info-key">
                <?= \App\View\Icons::mail('icon-sm') ?>
                <span>Courriel institutionnel</span>
            </span>
            <span class="info-val">
                <a href="mailto:<?= htmlspecialchars($reservation->email) ?>"><?= htmlspecialchars($reservation->email) ?></a>
            </span>
        </li>
        <li class="info-item">
            <span class="info-key">
                <?= \App\View\Icons::tag('icon-sm') ?>
                <span>Motif déclaré</span>
            </span>
            <span class="info-val"><?= htmlspecialchars($reservation->motif) ?></span>
        </li>
        <li class="info-item">
            <span class="info-key">
                <?= \App\View\Icons::clock('icon-sm') ?>
                <span>Date et heure de début</span>
            </span>
            <span class="info-val"><?= htmlspecialchars($reservation->date_debut->format('d/m/Y à H:i')) ?></span>
        </li>
        <li class="info-item">
            <span class="info-key">
                <?= \App\View\Icons::clock('icon-sm') ?>
                <span>Date et heure de fin</span>
            </span>
            <span class="info-val"><?= htmlspecialchars($reservation->date_fin->format('d/m/Y à H:i')) ?></span>
        </li>
        <li class="info-item">
            <span class="info-key">
                <?= \App\View\Icons::clock('icon-sm') ?>
                <span>Durée du créneau</span>
            </span>
            <span class="info-val">
                <?php
                    $diffMinutes = ($reservation->date_fin->getTimestamp() - $reservation->date_debut->getTimestamp()) / 60;
                    $heures = floor($diffMinutes / 60);
                    $minutes = $diffMinutes % 60;
                    echo ($heures > 0 ? "{$heures}h " : '') . ($minutes > 0 ? "{$minutes}min" : ($heures === 0.0 ? '0min' : ''));
                ?>
            </span>
        </li>
        <li class="info-item">
            <span class="info-key">
                <?= \App\View\Icons::checkCircle('icon-sm') ?>
                <span>Statut actuel</span>
            </span>
            <span class="info-val">
                <?php if ($reservation->statut === 'confirmée'): ?>
                    <span class="badge badge-success">
                        <span class="status-dot"></span>
                        Réservation confirmée
                    </span>
                <?php else: ?>
                    <span class="badge badge-danger">
                        <span class="status-dot"></span>
                        Réservation annulée
                    </span>
                <?php endif; ?>
            </span>
        </li>
        <li class="info-item">
            <span class="info-key">
                <?= \App\View\Icons::clock('icon-sm') ?>
                <span>Enregistrée dans le système le</span>
            </span>
            <span class="info-val"><?= $reservation->created_at ? htmlspecialchars($reservation->created_at->format('d/m/Y à H:i')) : '-' ?></span>
        </li>
    </ul>
</div>
