<div class="page-header">
    <div class="title-wrap">
        <h1><?= isset($salle) ? 'Modifier la salle : ' . htmlspecialchars($salle->nom) : 'Ajouter une nouvelle salle' ?></h1>
        <p class="subtitle">Remplissez les informations relatives à la salle universitaire.</p>
    </div>
    <div class="action-wrap">
        <a href="/salles" class="btn btn-outline">&larr; Retour</a>
    </div>
</div>

<div class="card form-card">
    <form method="POST" action="<?= isset($salle) ? '/salles/' . (int)$salle->id . '/edit' : '/salles' ?>">
        <div class="form-group <?= isset($errors['nom']) ? 'has-error' : '' ?>">
            <label for="nom">Nom de la salle <span class="required">*</span></label>
            <input type="text" id="nom" name="nom" class="form-control" 
                   value="<?= htmlspecialchars($old['nom'] ?? $salle->nom ?? '') ?>" required>
            <?php if (isset($errors['nom'])): ?>
                <div class="field-error"><?= htmlspecialchars($errors['nom']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group <?= isset($errors['batiment']) ? 'has-error' : '' ?>">
            <label for="batiment">Bâtiment <span class="required">*</span></label>
            <input type="text" id="batiment" name="batiment" class="form-control" 
                   value="<?= htmlspecialchars($old['batiment'] ?? $salle->batiment ?? '') ?>" required>
            <?php if (isset($errors['batiment'])): ?>
                <div class="field-error"><?= htmlspecialchars($errors['batiment']) ?></div>
            <?php endif; ?>
        </div>

        <div class="grid grid-2">
            <div class="form-group <?= isset($errors['capacite']) ? 'has-error' : '' ?>">
                <label for="capacite">Capacité (personnes) <span class="required">*</span></label>
                <input type="number" id="capacite" name="capacite" min="1" max="1000" class="form-control" 
                       value="<?= htmlspecialchars((string)($old['capacite'] ?? $salle->capacite ?? '30')) ?>" required>
                <?php if (isset($errors['capacite'])): ?>
                    <div class="field-error"><?= htmlspecialchars($errors['capacite']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group <?= isset($errors['type']) ? 'has-error' : '' ?>">
                <label for="type">Type de salle <span class="required">*</span></label>
                <?php $currentType = $old['type'] ?? $salle->type ?? 'cours'; ?>
                <select id="type" name="type" class="form-control" required>
                    <option value="cours" <?= $currentType === 'cours' ? 'selected' : '' ?>>Cours</option>
                    <option value="informatique" <?= $currentType === 'informatique' ? 'selected' : '' ?>>Informatique</option>
                    <option value="laboratoire" <?= $currentType === 'laboratoire' ? 'selected' : '' ?>>Laboratoire</option>
                    <option value="amphitheatre" <?= $currentType === 'amphitheatre' ? 'selected' : '' ?>>Amphithéâtre</option>
                    <option value="reunion" <?= $currentType === 'reunion' ? 'selected' : '' ?>>Réunion</option>
                </select>
                <?php if (isset($errors['type'])): ?>
                    <div class="field-error"><?= htmlspecialchars($errors['type']) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-group checkbox-group">
            <?php 
                $isActive = isset($old['active']) ? (bool)$old['active'] : ($salle->active ?? true);
            ?>
            <label class="checkbox-label">
                <input type="checkbox" name="active" value="1" <?= $isActive ? 'checked' : '' ?>>
                <span>Salle active (autorise les réservations)</span>
            </label>
            <?php if (isset($errors['active'])): ?>
                <div class="field-error"><?= htmlspecialchars($errors['active']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <?= isset($salle) ? 'Enregistrer les modifications' : 'Créer la salle' ?>
            </button>
            <a href="/salles" class="btn btn-outline">Annuler</a>
        </div>
    </form>
</div>
