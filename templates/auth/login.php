<div class="auth-wrapper" style="max-width: 460px; margin: 2rem auto;">
    <div class="card" style="padding: 2rem; box-shadow: var(--shadow-md);">
        <div style="text-align: center; margin-bottom: 1.5rem;">
            <div style="width: 48px; height: 48px; margin: 0 auto 0.75rem; background: var(--primary-light); color: var(--primary); border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center;">
                <?= \App\View\Icons::lock('icon-lg') ?>
            </div>
            <h1 style="font-size: 1.5rem; font-weight: 700; color: var(--slate-900);">Authentification</h1>
            <p style="color: var(--slate-500); font-size: 0.9rem; margin-top: 0.25rem;">Accédez à votre espace de gestion</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger" style="margin-bottom: 1.25rem;">
                <?= \App\View\Icons::alertCircle('icon') ?>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="/login" style="margin-bottom: 1.5rem;">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">

            <div class="form-group" style="margin-bottom: 1rem;">
                <label for="email" class="form-label">Adresse courriel</label>
                <input type="email" id="email" name="email" class="form-control" required autofocus value="<?= htmlspecialchars($old_email ?? '') ?>" placeholder="ex: admin@univ.sn">
            </div>

            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" id="password" name="password" class="form-control" required placeholder="••••••••">
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 0.75rem;">
                <?= \App\View\Icons::check('icon-sm') ?>
                <span>Se connecter</span>
            </button>
        </form>

        <div style="position: relative; text-align: center; margin: 1.5rem 0;">
            <hr style="border: 0; border-top: 1px solid var(--slate-200);">
            <span style="position: absolute; top: -0.65rem; left: 50%; transform: translateX(-50%); background: #ffffff; padding: 0 0.75rem; color: var(--slate-400); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;">
                Connexion Rapide
            </span>
        </div>

        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
            <form method="POST" action="/login/quick">
                <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                <input type="hidden" name="role" value="admin">
                <button type="submit" class="btn btn-outline" style="width: 100%; justify-content: center; background: var(--slate-50); border-color: var(--slate-300); font-weight: 600; color: var(--slate-800);">
                    <?= \App\View\Icons::checkCircle('icon-sm') ?>
                    <span>Connexion Rapide : Administrateur</span>
                </button>
            </form>

            <form method="POST" action="/login/quick">
                <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                <input type="hidden" name="role" value="responsable">
                <button type="submit" class="btn btn-outline" style="width: 100%; justify-content: center; background: var(--slate-50); border-color: var(--slate-300); font-weight: 600; color: var(--slate-800);">
                    <?= \App\View\Icons::checkCircle('icon-sm') ?>
                    <span>Connexion Rapide : Responsable</span>
                </button>
            </form>
        </div>

        <div style="margin-top: 1.5rem; padding: 0.85rem; background: var(--slate-50); border-radius: var(--radius-md); font-size: 0.8rem; color: var(--slate-600); border: 1px dashed var(--slate-300);">
            <div style="font-weight: 600; margin-bottom: 0.25rem;">Comptes de test pré-configurés :</div>
            <div>• Admin : <code>admin@univ.sn</code> / <code>admin123</code></div>
            <div>• Responsable : <code>prof@univ.sn</code> / <code>prof123</code></div>
        </div>
    </div>
</div>
