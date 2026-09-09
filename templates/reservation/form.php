<div class="page-header animate-in">
    <div class="title-wrap">
        <h1>Effectuer une réservation</h1>
        <p class="subtitle">Sélectionnez une salle universitaire et réservez un créneau (durée maximale : 4 heures).</p>
    </div>
    <div class="action-wrap">
        <a href="/reservations" class="btn btn-outline">
            <?= \App\View\Icons::arrowLeft('icon-sm') ?>
            <span>Retour à la liste</span>
        </a>
    </div>
</div>

<div class="card form-card animate-in">
    <form method="POST" action="/reservations">
        <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
        <div class="form-group <?= isset($errors['salle_id']) ? 'has-error' : '' ?>">
            <label for="salle_id">
                <?= \App\View\Icons::building('icon-sm') ?>
                <span>Salle concernée <span class="required">*</span></span>
            </label>
            <select id="salle_id" name="salle_id" class="form-control" required>
                <option value="">-- Choisir une salle disponible --</option>
                <?php foreach ($salles as $salle): ?>
                    <?php
                        $selectedId = (int)($old['salle_id'] ?? $salleIdSelectionnee ?? 0);
                    ?>
                    <option value="<?= (int)$salle->id ?>" <?= $selectedId === (int)$salle->id ? 'selected' : '' ?> <?= !$salle->active ? 'disabled' : '' ?>>
                        <?= htmlspecialchars($salle->nom) ?> (<?= htmlspecialchars($salle->batiment) ?> - <?= (int)$salle->capacite ?> places)
                        <?= !$salle->active ? ' [INDISPONIBLE]' : '' ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors['salle_id'])): ?>
                <div class="field-error">
                    <?= \App\View\Icons::alertCircle('icon-sm') ?>
                    <span><?= htmlspecialchars($errors['salle_id']) ?></span>
                </div>
            <?php endif; ?>
        </div>

        <div class="grid grid-2">
            <div class="form-group <?= isset($errors['responsable']) ? 'has-error' : '' ?>">
                <label for="responsable">
                    <?= \App\View\Icons::user('icon-sm') ?>
                    <span>Nom du responsable <span class="required">*</span></span>
                </label>
                <input type="text" id="responsable" name="responsable" class="form-control"
                       value="<?= htmlspecialchars($old['responsable'] ?? '') ?>" placeholder="Ex: Dr. Awa Ndiaye" required>
                <?php if (isset($errors['responsable'])): ?>
                    <div class="field-error">
                        <?= \App\View\Icons::alertCircle('icon-sm') ?>
                        <span><?= htmlspecialchars($errors['responsable']) ?></span>
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-group <?= isset($errors['email']) ? 'has-error' : '' ?>">
                <label for="email">
                    <?= \App\View\Icons::mail('icon-sm') ?>
                    <span>Email institutionnel <span class="required">*</span></span>
                </label>
                <input type="email" id="email" name="email" class="form-control"
                       value="<?= htmlspecialchars($old['email'] ?? '') ?>" placeholder="Ex: contact@universite.sn" required>
                <?php if (isset($errors['email'])): ?>
                    <div class="field-error">
                        <?= \App\View\Icons::alertCircle('icon-sm') ?>
                        <span><?= htmlspecialchars($errors['email']) ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-group <?= isset($errors['motif']) ? 'has-error' : '' ?>">
            <label for="motif">
                <?= \App\View\Icons::tag('icon-sm') ?>
                <span>Motif de la réservation <span class="required">*</span></span>
            </label>
            <textarea id="motif" name="motif" rows="3" class="form-control"
                      placeholder="Précisez l'objet : cours, examen, séminaire ou atelier..." required><?= htmlspecialchars($old['motif'] ?? '') ?></textarea>
            <?php if (isset($errors['motif'])): ?>
                <div class="field-error">
                    <?= \App\View\Icons::alertCircle('icon-sm') ?>
                    <span><?= htmlspecialchars($errors['motif']) ?></span>
                </div>
            <?php endif; ?>
        </div>

        <div class="grid grid-2">
            <div class="form-group <?= isset($errors['date_debut']) ? 'has-error' : '' ?>">
                <label for="date_debut">
                    <?= \App\View\Icons::clock('icon-sm') ?>
                    <span>Début du créneau <span class="required">*</span></span>
                </label>
                <input type="datetime-local" id="date_debut" name="date_debut" class="form-control"
                       value="<?= htmlspecialchars($old['date_debut'] ?? '') ?>" required>
                <?php if (isset($errors['date_debut'])): ?>
                    <div class="field-error">
                        <?= \App\View\Icons::alertCircle('icon-sm') ?>
                        <span><?= htmlspecialchars($errors['date_debut']) ?></span>
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-group <?= isset($errors['date_fin']) ? 'has-error' : '' ?>">
                <label for="date_fin">
                    <?= \App\View\Icons::clock('icon-sm') ?>
                    <span>Fin du créneau <span class="required">*</span></span>
                </label>
                <input type="datetime-local" id="date_fin" name="date_fin" class="form-control"
                       value="<?= htmlspecialchars($old['date_fin'] ?? '') ?>" required>
                <?php if (isset($errors['date_fin'])): ?>
                    <div class="field-error">
                        <?= \App\View\Icons::alertCircle('icon-sm') ?>
                        <span><?= htmlspecialchars($errors['date_fin']) ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-actions">
            <a href="/reservations" class="btn btn-outline">Annuler</a>
            <button type="submit" class="btn btn-primary">
                <?= \App\View\Icons::check('icon-sm') ?>
                <span>Confirmer la réservation</span>
            </button>
        </div>
    </form>
</div>
