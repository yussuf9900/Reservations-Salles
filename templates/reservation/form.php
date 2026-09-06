<div class="page-header">
    <div class="title-wrap">
        <h1>Effectuer une réservation</h1>
        <p class="subtitle">Sélectionnez une salle et un créneau horaire (durée max : 4 heures).</p>
    </div>
    <div class="action-wrap">
        <a href="/reservations" class="btn btn-outline">&larr; Retour</a>
    </div>
</div>

<div class="card form-card">
    <form method="POST" action="/reservations">
        <div class="form-group <?= isset($errors['salle_id']) ? 'has-error' : '' ?>">
            <label for="salle_id">Salle concernée <span class="required">*</span></label>
            <select id="salle_id" name="salle_id" class="form-control" required>
                <option value="">-- Choisir une salle disponible --</option>
                <?php foreach ($salles as $salle): ?>
                    <?php 
                        $selectedId = (int)($old['salle_id'] ?? $salleIdSelectionnee ?? 0);
                    ?>
                    <option value="<?= (int)$salle->id ?>" <?= $selectedId === (int)$salle->id ? 'selected' : '' ?> <?= !$salle->active ? 'disabled' : '' ?>>
                        <?= htmlspecialchars($salle->nom) ?> (<?= htmlspecialchars($salle->batiment) ?> - <?= (int)$salle->capacite ?> places)
                        <?= !$salle->active ? ' [INACTIVE]' : '' ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors['salle_id'])): ?>
                <div class="field-error"><?= htmlspecialchars($errors['salle_id']) ?></div>
            <?php endif; ?>
        </div>

        <div class="grid grid-2">
            <div class="form-group <?= isset($errors['responsable']) ? 'has-error' : '' ?>">
                <label for="responsable">Nom du responsable <span class="required">*</span></label>
                <input type="text" id="responsable" name="responsable" class="form-control" 
                       value="<?= htmlspecialchars($old['responsable'] ?? '') ?>" placeholder="Ex: Awa Ndiaye" required>
                <?php if (isset($errors['responsable'])): ?>
                    <div class="field-error"><?= htmlspecialchars($errors['responsable']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group <?= isset($errors['email']) ? 'has-error' : '' ?>">
                <label for="email">Adresse email institutionnelle <span class="required">*</span></label>
                <input type="email" id="email" name="email" class="form-control" 
                       value="<?= htmlspecialchars($old['email'] ?? '') ?>" placeholder="Ex: awa.ndiaye@universite.sn" required>
                <?php if (isset($errors['email'])): ?>
                    <div class="field-error"><?= htmlspecialchars($errors['email']) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-group <?= isset($errors['motif']) ? 'has-error' : '' ?>">
            <label for="motif">Motif de la réservation <span class="required">*</span></label>
            <textarea id="motif" name="motif" rows="3" class="form-control" 
                      placeholder="Ex: Cours d'architecture logicielle - Master 1" required><?= htmlspecialchars($old['motif'] ?? '') ?></textarea>
            <?php if (isset($errors['motif'])): ?>
                <div class="field-error"><?= htmlspecialchars($errors['motif']) ?></div>
            <?php endif; ?>
        </div>

        <div class="grid grid-2">
            <div class="form-group <?= isset($errors['date_debut']) ? 'has-error' : '' ?>">
                <label for="date_debut">Date et heure de début <span class="required">*</span></label>
                <input type="datetime-local" id="date_debut" name="date_debut" class="form-control" 
                       value="<?= htmlspecialchars($old['date_debut'] ?? '') ?>" required>
                <?php if (isset($errors['date_debut'])): ?>
                    <div class="field-error"><?= htmlspecialchars($errors['date_debut']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group <?= isset($errors['date_fin']) ? 'has-error' : '' ?>">
                <label for="date_fin">Date et heure de fin <span class="required">*</span></label>
                <input type="datetime-local" id="date_fin" name="date_fin" class="form-control" 
                       value="<?= htmlspecialchars($old['date_fin'] ?? '') ?>" required>
                <?php if (isset($errors['date_fin'])): ?>
                    <div class="field-error"><?= htmlspecialchars($errors['date_fin']) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Confirmer la réservation</button>
            <a href="/reservations" class="btn btn-outline">Annuler</a>
        </div>
    </form>
</div>
