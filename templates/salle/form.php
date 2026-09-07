<div class="page-header animate-in">
    <div class="title-wrap">
        <h1><?= isset($salle) ? 'Modifier la salle : ' . htmlspecialchars($salle->nom) : 'Ajouter une nouvelle salle' ?></h1>
        <p class="subtitle">Renseignez les caractéristiques techniques de la salle universitaire.</p>
    </div>
    <div class="action-wrap">
        <a href="/salles" class="btn btn-outline">
            <?= \App\View\Icons::arrowLeft('icon-sm') ?>
            <span>Retour à la liste</span>
        </a>
    </div>
</div>

<div class="card form-card animate-in">
    <form method="POST" action="<?= isset($salle) ? '/salles/' . (int)$salle->id . '/edit' : '/salles' ?>">
        <div class="form-group <?= isset($errors['nom']) ? 'has-error' : '' ?>">
            <label for="nom">
                <?= \App\View\Icons::building('icon-sm') ?>
                <span>Nom de la salle <span class="required">*</span></span>
            </label>
            <input type="text" id="nom" name="nom" class="form-control" 
                   value="<?= htmlspecialchars($old['nom'] ?? $salle->nom ?? '') ?>" 
                   placeholder="Ex: Amphithéâtre A, Salle B12..." required>
            <?php if (isset($errors['nom'])): ?>
                <div class="field-error">
                    <?= \App\View\Icons::alertCircle('icon-sm') ?>
                    <span><?= htmlspecialchars($errors['nom']) ?></span>
                </div>
            <?php endif; ?>
        </div>

        <div class="form-group <?= isset($errors['batiment']) ? 'has-error' : '' ?>">
            <label for="batiment">
                <?= \App\View\Icons::building('icon-sm') ?>
                <span>Bâtiment <span class="required">*</span></span>
            </label>
            <input type="text" id="batiment" name="batiment" class="form-control" 
                   value="<?= htmlspecialchars($old['batiment'] ?? $salle->batiment ?? '') ?>" 
                   placeholder="Ex: Bâtiment Sciences, Bâtiment Administratif..." required>
            <?php if (isset($errors['batiment'])): ?>
                <div class="field-error">
                    <?= \App\View\Icons::alertCircle('icon-sm') ?>
                    <span><?= htmlspecialchars($errors['batiment']) ?></span>
                </div>
            <?php endif; ?>
        </div>

        <div class="grid grid-2">
            <div class="form-group <?= isset($errors['capacite']) ? 'has-error' : '' ?>">
                <label for="capacite">
                    <?= \App\View\Icons::users('icon-sm') ?>
                    <span>Capacité (personnes) <span class="required">*</span></span>
                </label>
                <input type="number" id="capacite" name="capacite" min="1" max="1000" class="form-control" 
                       value="<?= htmlspecialchars((string)($old['capacite'] ?? $salle->capacite ?? '30')) ?>" required>
                <?php if (isset($errors['capacite'])): ?>
                    <div class="field-error">
                        <?= \App\View\Icons::alertCircle('icon-sm') ?>
                        <span><?= htmlspecialchars($errors['capacite']) ?></span>
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-group <?= isset($errors['type']) ? 'has-error' : '' ?>">
                <label for="type">
                    <?= \App\View\Icons::tag('icon-sm') ?>
                    <span>Type d'espace <span class="required">*</span></span>
                </label>
                <?php $currentType = $old['type'] ?? $salle->type ?? 'cours'; ?>
                <select id="type" name="type" class="form-control" required>
                    <option value="cours" <?= $currentType === 'cours' ? 'selected' : '' ?>>Salle de cours</option>
                    <option value="informatique" <?= $currentType === 'informatique' ? 'selected' : '' ?>>Salle informatique</option>
                    <option value="laboratoire" <?= $currentType === 'laboratoire' ? 'selected' : '' ?>>Laboratoire de recherche / TP</option>
                    <option value="amphitheatre" <?= $currentType === 'amphitheatre' ? 'selected' : '' ?>>Amphithéâtre</option>
                    <option value="reunion" <?= $currentType === 'reunion' ? 'selected' : '' ?>>Salle de réunion</option>
                </select>
                <?php if (isset($errors['type'])): ?>
                    <div class="field-error">
                        <?= \App\View\Icons::alertCircle('icon-sm') ?>
                        <span><?= htmlspecialchars($errors['type']) ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="checkbox-group">
            <?php 
                $isActive = isset($old['active']) ? (bool)$old['active'] : ($salle->active ?? true);
            ?>
            <label class="checkbox-label">
                <input type="checkbox" name="active" value="1" <?= $isActive ? 'checked' : '' ?>>
                <span>Salle active (disponible pour les réservations)</span>
            </label>
            <?php if (isset($errors['active'])): ?>
                <div class="field-error">
                    <?= \App\View\Icons::alertCircle('icon-sm') ?>
                    <span><?= htmlspecialchars($errors['active']) ?></span>
                </div>
            <?php endif; ?>
        </div>

        <div class="form-actions">
            <a href="/salles" class="btn btn-outline">Annuler</a>
            <button type="submit" class="btn btn-primary">
                <?= \App\View\Icons::check('icon-sm') ?>
                <span><?= isset($salle) ? 'Enregistrer les modifications' : 'Créer la salle' ?></span>
            </button>
        </div>
    </form>
</div>
