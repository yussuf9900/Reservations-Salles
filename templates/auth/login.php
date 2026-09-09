<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-icon">
                <?= \App\View\Icons::lock('icon-lg') ?>
            </div>
            <h1 class="auth-title">Authentification</h1>
            <p class="auth-subtitle">Accédez à votre espace de gestion</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <?= \App\View\Icons::alertCircle('icon') ?>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="/login<?= !empty($redirect) ? '?redirect=' . urlencode($redirect) : '' ?>" class="auth-form">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
            <?php if (!empty($redirect)): ?>
                <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">
            <?php endif; ?>

            <div class="form-group">
                <label for="email" class="form-label">Adresse courriel</label>
                <input type="email" id="email" name="email" class="form-control" required autofocus value="<?= htmlspecialchars($old_email ?? '') ?>" placeholder="ex: admin@univ.sn">
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" id="password" name="password" class="form-control" required placeholder="••••••••">
            </div>

            <button type="submit" class="btn btn-primary auth-submit">
                <?= \App\View\Icons::check('icon-sm') ?>
                <span>Se connecter</span>
            </button>
        </form>

        <div class="auth-divider">
            <hr>
            <span>Connexion Rapide</span>
        </div>

        <div class="auth-quick-buttons">
            <form method="POST" action="/login/quick<?= !empty($redirect) ? '?redirect=' . urlencode($redirect) : '' ?>">
                <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                <input type="hidden" name="role" value="admin">
                <?php if (!empty($redirect)): ?>
                    <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">
                <?php endif; ?>
                <button type="submit" class="btn auth-quick-btn">
                    <?= \App\View\Icons::checkCircle('icon-sm') ?>
                    <span>Connexion Rapide : Administrateur</span>
                </button>
            </form>

            <form method="POST" action="/login/quick<?= !empty($redirect) ? '?redirect=' . urlencode($redirect) : '' ?>">
                <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                <input type="hidden" name="role" value="responsable">
                <?php if (!empty($redirect)): ?>
                    <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">
                <?php endif; ?>
                <button type="submit" class="btn auth-quick-btn">
                    <?= \App\View\Icons::checkCircle('icon-sm') ?>
                    <span>Connexion Rapide : Responsable</span>
                </button>
            </form>
        </div>

        <div class="auth-info">
            <div class="auth-info-title">Comptes de test pré-configurés :</div>
            <div>• Admin : <code>admin@univ.sn</code> / <code>admin123</code></div>
            <div>• Responsable : <code>prof@univ.sn</code> / <code>prof123</code></div>
        </div>
    </div>
</div>
