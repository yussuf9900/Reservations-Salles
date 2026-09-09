<div class="error-page animate-in">
    <div class="error-code">403</div>
    <h1 class="error-title"><?= htmlspecialchars($title ?? 'Accès Refusé') ?></h1>
    <p class="error-desc"><?= htmlspecialchars($message ?? 'Vous n\'avez pas les autorisations requises pour effectuer cette action ou votre session a expiré.') ?></p>
    <?php if (!empty($allowedMethods)): ?>
        <p class="text-sm text-muted">Précision : <strong><?= htmlspecialchars(implode(', ', $allowedMethods)) ?></strong></p>
    <?php endif; ?>
    <div class="action-wrap" style="justify-content: center; gap: 1rem; margin-top: 1.5rem;">
        <a href="/salles" class="btn btn-primary">
            <?= \App\View\Icons::building('icon-sm') ?>
            <span>Retour aux salles</span>
        </a>
        <a href="/login" class="btn btn-outline">
            <?= \App\View\Icons::logIn('icon-sm') ?>
            <span>Se connecter</span>
        </a>
    </div>
</div>
