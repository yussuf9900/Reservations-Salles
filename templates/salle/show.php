<div class="page-header animate-in">
    <div class="title-wrap">
        <h1><?= htmlspecialchars($salle->nom) ?></h1>
        <p class="subtitle"><?= htmlspecialchars($salle->batiment) ?> &bull; Capacité de <?= (int)$salle->capacite ?> personnes</p>
    </div>
    <div class="action-wrap">
        <a href="/salles" class="btn btn-outline">
            <?= \App\View\Icons::arrowLeft('icon-sm') ?>
            <span>Retour</span>
        </a>
        <?php if (!empty($isAdmin) || (!empty($currentUser) && $currentUser->isAdmin())): ?>
            <a href="/salles/<?= (int)$salle->id ?>/edit" class="btn btn-secondary">
                <?= \App\View\Icons::edit('icon-sm') ?>
                <span>Modifier</span>
            </a>
        <?php endif; ?>
        <a href="/reservations/create?salle_id=<?= (int)$salle->id ?>" class="btn btn-primary">
            <?= \App\View\Icons::calendar('icon-sm') ?>
            <span>Réserver cette salle</span>
        </a>
    </div>
</div>

<div class="grid grid-2 animate-in">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">
                <?= \App\View\Icons::building('icon') ?>
                <span>Caractéristiques de l'Espace</span>
            </h2>
        </div>
        <ul class="info-list">
            <li class="info-item">
                <span class="info-key">
                    <?= \App\View\Icons::tag('icon-sm') ?>
                    <span>Identifiant</span>
                </span>
                <span class="info-val">#<?= (int)$salle->id ?></span>
            </li>
            <li class="info-item">
                <span class="info-key">
                    <?= \App\View\Icons::building('icon-sm') ?>
                    <span>Désignation</span>
                </span>
                <span class="info-val"><?= htmlspecialchars($salle->nom) ?></span>
            </li>
            <li class="info-item">
                <span class="info-key">
                    <?= \App\View\Icons::building('icon-sm') ?>
                    <span>Bâtiment universitaire</span>
                </span>
                <span class="info-val"><?= htmlspecialchars($salle->batiment) ?></span>
            </li>
            <li class="info-item">
                <span class="info-key">
                    <?= \App\View\Icons::users('icon-sm') ?>
                    <span>Capacité d'accueil</span>
                </span>
                <span class="info-val"><?= (int)$salle->capacite ?> places</span>
            </li>
            <li class="info-item">
                <span class="info-key">
                    <?= \App\View\Icons::tag('icon-sm') ?>
                    <span>Type d'usage</span>
                </span>
                <span class="info-val">
                    <span class="badge badge-info"><?= htmlspecialchars($salle->type) ?></span>
                </span>
            </li>
            <li class="info-item">
                <span class="info-key">
                    <?= \App\View\Icons::checkCircle('icon-sm') ?>
                    <span>Disponibilité</span>
                </span>
                <span class="info-val">
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
                </span>
            </li>
            <li class="info-item">
                <span class="info-key">
                    <?= \App\View\Icons::clock('icon-sm') ?>
                    <span>Enregistrée le</span>
                </span>
                <span class="info-val"><?= $salle->created_at ? htmlspecialchars($salle->created_at->format('d/m/Y à H:i')) : '-' ?></span>
            </li>
        </ul>
    </div>

    <div class="card">
        <div class="card-header">
            <h2 class="card-title">
                <?= \App\View\Icons::calendar('icon') ?>
                <span>Réservations de cette Salle</span>
            </h2>
        </div>
        <?php $reservations = $salle->reservations()->orderBy('date_debut', 'desc')->get(); ?>
        <?php if ($reservations->isEmpty()): ?>
            <div class="empty-state">
                <?= \App\View\Icons::calendar('empty-icon') ?>
                <p class="empty-text">Aucun créneau réservé pour cette salle actuellement.</p>
                <a href="/reservations/create?salle_id=<?= (int)$salle->id ?>" class="btn btn-sm btn-primary">
                    <?= \App\View\Icons::plus('icon-sm') ?>
                    <span>Réserver le premier créneau</span>
                </a>
            </div>
        <?php else: ?>
            <ul class="reservation-mini-list">
                <?php foreach ($reservations as $res): ?>
                    <li class="reservation-mini-item">
                        <div class="cell-meta">
                            <strong>
                                <a href="/reservations/<?= (int)$res->id ?>">
                                    <?= htmlspecialchars($res->responsable) ?>
                                </a>
                            </strong>
                            <span class="cell-sub"><?= htmlspecialchars($res->motif) ?></span>
                            <span class="text-sm text-muted">
                                <?= \App\View\Icons::clock('icon-sm') ?>
                                <?= htmlspecialchars($res->date_debut->format('d/m/Y H:i')) ?> &rarr; <?= htmlspecialchars($res->date_fin->format('H:i')) ?>
                            </span>
                        </div>
                        <div>
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
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>
