<div class="page-header animate-in">
    <div class="title-wrap">
        <h1>Tableau de Bord &amp; Statistiques</h1>
        <p class="subtitle">Vue d'ensemble de l'utilisation et de la fréquentation des salles</p>
    </div>
    <div class="action-wrap">
        <?php if (!empty($currentUser) && $currentUser->isAdmin()): ?>
            <a href="http://localhost:<?= htmlspecialchars((string)($_ENV['PMA_PORT'] ?? getenv('PMA_PORT') ?: '8081')) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline" title="Gérer la base de données via phpMyAdmin">
                <?= \App\View\Icons::database('icon-sm') ?>
                <span>phpMyAdmin (BDD)</span>
            </a>
        <?php endif; ?>
        <a href="/reservations/create" class="btn btn-primary">
            <?= \App\View\Icons::plus('icon-sm') ?>
            <span>Nouvelle Réservation</span>
        </a>
    </div>
</div>

<div class="kpi-grid animate-in">
    <div class="kpi-card kpi-card--blue">
        <div class="kpi-body">
            <div>
                <div class="kpi-label">Salles Répertoriées</div>
                <div class="kpi-value"><?= $stats['total_salles'] ?></div>
                <div class="kpi-sub"><?= $stats['salles_actives'] ?> actives &bull; <?= $stats['salles_inactives'] ?> inactives</div>
            </div>
            <div class="kpi-icon kpi-icon--blue">
                <?= \App\View\Icons::building('icon-lg') ?>
            </div>
        </div>
    </div>

    <div class="kpi-card kpi-card--green">
        <div class="kpi-body">
            <div>
                <div class="kpi-label">Réservations Actives</div>
                <div class="kpi-value"><?= $stats['reservations_confirmees'] ?></div>
                <div class="kpi-sub">Sur <?= $stats['total_reservations'] ?> demandes totales</div>
            </div>
            <div class="kpi-icon kpi-icon--green">
                <?= \App\View\Icons::checkCircle('icon-lg') ?>
            </div>
        </div>
    </div>

    <div class="kpi-card kpi-card--violet">
        <div class="kpi-body">
            <div>
                <div class="kpi-label">Volume Horaire Réservé</div>
                <div class="kpi-value"><?= $stats['heures_totales'] ?> h</div>
                <div class="kpi-sub">Cumul des créneaux validés</div>
            </div>
            <div class="kpi-icon kpi-icon--violet">
                <?= \App\View\Icons::clock('icon-lg') ?>
            </div>
        </div>
    </div>

    <div class="kpi-card kpi-card--amber">
        <div class="kpi-body">
            <div>
                <div class="kpi-label">Taux de Fréquentation</div>
                <div class="kpi-value"><?= $stats['taux_occupation'] ?> %</div>
                <div class="kpi-sub"><?= $stats['reservations_annulees'] ?> réservations annulées</div>
            </div>
            <div class="kpi-icon kpi-icon--amber">
                <?= \App\View\Icons::barChart('icon-lg') ?>
            </div>
        </div>
    </div>
</div>

<div class="dashboard-grid">
    <div class="card animate-in">
        <div class="card-header">
            <h2 class="card-title">Top 5 des Salles les Plus Utilisées</h2>
            <a href="/salles" class="section-link">Voir toutes les salles &rarr;</a>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Rang</th>
                        <th>Salle</th>
                        <th>Type</th>
                        <th>Capacité</th>
                        <th class="text-center">Réservations</th>
                        <th class="text-right">Heures</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($stats['top_salles'])): ?>
                        <tr>
                            <td colspan="6" class="text-center">
                                <div class="empty-state">
                                    <p class="empty-text">Aucune donnée de réservation disponible.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($stats['top_salles'] as $idx => $salle): ?>
                            <tr>
                                <td>
                                    <span class="rank-badge <?= $idx === 0 ? 'rank-1' : ($idx === 1 ? 'rank-2' : ($idx === 2 ? 'rank-3' : 'rank-default')) ?>">
                                        <?= $idx + 1 ?>
                                    </span>
                                </td>
                                <td>
                                    <strong><a href="/salles/<?= $salle['id'] ?>"><?= htmlspecialchars($salle['nom']) ?></a></strong>
                                    <div class="text-sm text-muted"><?= htmlspecialchars($salle['batiment']) ?></div>
                                </td>
                                <td><span class="badge badge-info"><?= htmlspecialchars($salle['type']) ?></span></td>
                                <td><?= $salle['capacite'] ?> places</td>
                                <td class="text-center">
                                    <span class="badge badge-accent"><?= $salle['reservations'] ?> résa.</span>
                                </td>
                                <td class="text-right">
                                    <strong><?= $salle['heures'] ?> h</strong>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card animate-in">
        <h2 class="card-title">Répartition par Type</h2>

        <?php
        $labelsTypes = [
            'cours'        => 'Cours magistral',
            'informatique' => 'Informatique',
            'laboratoire'  => 'Laboratoire',
            'amphitheatre' => 'Amphithéâtre',
            'reunion'      => 'Réunion',
        ];
        $totalTypeRes = max(1, array_sum($stats['repartition_types']));
        ?>
        <?php foreach ($stats['repartition_types'] as $type => $count): ?>
            <?php $pct = round(($count / $totalTypeRes) * 100); ?>
            <div class="progress-item">
                <div class="progress-header">
                    <span class="progress-label"><?= $labelsTypes[$type] ?? ucfirst($type) ?></span>
                    <span class="progress-value"><?= $count ?> (<?= $pct ?>%)</span>
                </div>
                <div class="progress-track">
                    <div class="progress-bar" style="width: <?= $pct ?>%"></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="card animate-in">
    <div class="card-header">
        <h2 class="card-title">Prochaines Réservations à Venir</h2>
        <a href="/reservations" class="section-link">Consulter le calendrier complet &rarr;</a>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Salle</th>
                    <th>Responsable</th>
                    <th>Date &amp; Début</th>
                    <th>Fin</th>
                    <th>Durée</th>
                    <th class="text-right">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($stats['prochaines_reservations'])): ?>
                    <tr>
                        <td colspan="6" class="text-center">
                            <div class="empty-state">
                                <p class="empty-text">Aucune réservation planifiée pour le moment.</p>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($stats['prochaines_reservations'] as $p): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($p['salle_nom']) ?></strong></td>
                            <td><?= htmlspecialchars($p['responsable']) ?></td>
                            <td><?= htmlspecialchars($p['debut']) ?></td>
                            <td><?= htmlspecialchars($p['fin']) ?></td>
                            <td><span class="badge badge-secondary"><?= htmlspecialchars($p['duree']) ?></span></td>
                            <td class="text-right">
                                <a href="/reservations/<?= $p['id'] ?>" class="btn btn-sm btn-outline">Détails</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
