<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.75rem; font-weight: 700; color: var(--slate-900);">Tableau de Bord & Statistiques</h1>
        <p style="color: var(--slate-500); margin-top: 0.25rem;">Vue d'ensemble de l'utilisation et de la fréquentation des salles</p>
    </div>
    <div style="display: flex; gap: 0.75rem;">
        <a href="/reservations/create" class="btn btn-primary">
            <?= \App\View\Icons::plus('icon-sm') ?>
            <span>Nouvelle Réservation</span>
        </a>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
    <div class="card" style="padding: 1.25rem; border-left: 4px solid var(--primary);">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <div style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--slate-500); font-weight: 600;">Salles Répertoriées</div>
                <div style="font-size: 1.85rem; font-weight: 700; color: var(--slate-900); margin-top: 0.25rem;"><?= $stats['total_salles'] ?></div>
                <div style="font-size: 0.8rem; color: var(--slate-500); margin-top: 0.25rem;">
                    <?= $stats['salles_actives'] ?> actives &bull; <?= $stats['salles_inactives'] ?> inactives
                </div>
            </div>
            <div style="color: var(--primary); background: var(--primary-light); padding: 0.75rem; border-radius: var(--radius-md);">
                <?= \App\View\Icons::building('icon-lg') ?>
            </div>
        </div>
    </div>

    <div class="card" style="padding: 1.25rem; border-left: 4px solid var(--success);">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <div style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--slate-500); font-weight: 600;">Réservations Actives</div>
                <div style="font-size: 1.85rem; font-weight: 700; color: var(--slate-900); margin-top: 0.25rem;"><?= $stats['reservations_confirmees'] ?></div>
                <div style="font-size: 0.8rem; color: var(--slate-500); margin-top: 0.25rem;">
                    Sur <?= $stats['total_reservations'] ?> demandes totales
                </div>
            </div>
            <div style="color: var(--success); background: var(--success-bg); padding: 0.75rem; border-radius: var(--radius-md);">
                <?= \App\View\Icons::checkCircle('icon-lg') ?>
            </div>
        </div>
    </div>

    <div class="card" style="padding: 1.25rem; border-left: 4px solid #8b5cf6;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <div style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--slate-500); font-weight: 600;">Volume Horaire Réservé</div>
                <div style="font-size: 1.85rem; font-weight: 700; color: var(--slate-900); margin-top: 0.25rem;"><?= $stats['heures_totales'] ?> h</div>
                <div style="font-size: 0.8rem; color: var(--slate-500); margin-top: 0.25rem;">
                    Cumul des créneaux validés
                </div>
            </div>
            <div style="color: #8b5cf6; background: #f5f3ff; padding: 0.75rem; border-radius: var(--radius-md);">
                <?= \App\View\Icons::clock('icon-lg') ?>
            </div>
        </div>
    </div>

    <div class="card" style="padding: 1.25rem; border-left: 4px solid var(--warning);">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <div style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--slate-500); font-weight: 600;">Taux de Fréquentation</div>
                <div style="font-size: 1.85rem; font-weight: 700; color: var(--slate-900); margin-top: 0.25rem;"><?= $stats['taux_occupation'] ?> %</div>
                <div style="font-size: 0.8rem; color: var(--slate-500); margin-top: 0.25rem;">
                    <?= $stats['reservations_annulees'] ?> réservations annulées
                </div>
            </div>
            <div style="color: var(--warning); background: var(--warning-bg); padding: 0.75rem; border-radius: var(--radius-md);">
                <?= \App\View\Icons::barChart('icon-lg') ?>
            </div>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
    <div class="card" style="padding: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
            <h2 style="font-size: 1.15rem; font-weight: 700; color: var(--slate-900);">Top 5 des Salles les Plus Utilisées</h2>
            <a href="/salles" class="text-sm">Voir toutes les salles &rarr;</a>
        </div>

        <div class="table-responsive">
            <table class="table" style="width: 100%;">
                <thead>
                    <tr>
                        <th style="width: 45px;">Rang</th>
                        <th>Salle</th>
                        <th>Type</th>
                        <th>Capacité</th>
                        <th style="text-align: center;">Réservations</th>
                        <th style="text-align: right;">Heures</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($stats['top_salles'])): ?>
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--slate-500); padding: 1.5rem;">
                                Aucune donnée de réservation disponible.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($stats['top_salles'] as $idx => $salle): ?>
                            <tr>
                                <td>
                                    <span style="display: inline-flex; width: 26px; height: 26px; border-radius: 50%; background: <?= $idx === 0 ? '#fef3c7' : ($idx === 1 ? '#e2e8f0' : ($idx === 2 ? '#ffedd5' : 'var(--slate-100)')) ?>; color: <?= $idx === 0 ? '#b45309' : ($idx === 1 ? '#475569' : ($idx === 2 ? '#c2410c' : 'var(--slate-700)')) ?>; font-weight: 700; font-size: 0.8rem; align-items: center; justify-content: center;">
                                        <?= $idx + 1 ?>
                                    </span>
                                </td>
                                <td>
                                    <strong><a href="/salles/<?= $salle['id'] ?>"><?= htmlspecialchars($salle['nom']) ?></a></strong>
                                    <div class="text-sm text-muted"><?= htmlspecialchars($salle['batiment']) ?></div>
                                </td>
                                <td><span class="badge"><?= htmlspecialchars($salle['type']) ?></span></td>
                                <td><?= $salle['capacite'] ?> places</td>
                                <td style="text-align: center;">
                                    <span class="badge" style="background: var(--primary-light); color: var(--primary); font-weight: 600;">
                                        <?= $salle['reservations'] ?> résa.
                                    </span>
                                </td>
                                <td style="text-align: right; font-weight: 600;">
                                    <?= $salle['heures'] ?> h
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card" style="padding: 1.5rem;">
        <h2 style="font-size: 1.15rem; font-weight: 700; color: var(--slate-900); margin-bottom: 1.25rem;">Répartition par Type</h2>

        <div style="display: flex; flex-direction: column; gap: 1rem;">
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
                <div>
                    <div style="display: flex; justify-content: space-between; font-size: 0.85rem; margin-bottom: 0.35rem;">
                        <span style="font-weight: 600;"><?= $labelsTypes[$type] ?? ucfirst($type) ?></span>
                        <span class="text-muted"><?= $count ?> (<?= $pct ?>%)</span>
                    </div>
                    <div style="height: 8px; background: var(--slate-100); border-radius: var(--radius-full); overflow: hidden;">
                        <div style="width: <?= $pct ?>%; height: 100%; background: var(--primary); border-radius: var(--radius-full);"></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="card" style="padding: 1.5rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
        <h2 style="font-size: 1.15rem; font-weight: 700; color: var(--slate-900);">Prochaines Réservations à Venir</h2>
        <a href="/reservations" class="text-sm">Consulter le calendrier complet &rarr;</a>
    </div>

    <div class="table-responsive">
        <table class="table" style="width: 100%;">
            <thead>
                <tr>
                    <th>Salle</th>
                    <th>Responsable</th>
                    <th>Date & Début</th>
                    <th>Fin</th>
                    <th>Durée</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($stats['prochaines_reservations'])): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--slate-500); padding: 1.5rem;">
                            Aucune réservation planifiée pour le moment.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($stats['prochaines_reservations'] as $p): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($p['salle_nom']) ?></strong></td>
                            <td><?= htmlspecialchars($p['responsable']) ?></td>
                            <td><?= htmlspecialchars($p['debut']) ?></td>
                            <td><?= htmlspecialchars($p['fin']) ?></td>
                            <td><span class="badge"><?= htmlspecialchars($p['duree']) ?></span></td>
                            <td style="text-align: right;">
                                <a href="/reservations/<?= $p['id'] ?>" class="btn btn-sm btn-outline">Détails</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
