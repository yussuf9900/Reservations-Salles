<div class="error-page animate-in">
    <div class="error-code">405</div>
    <h1 class="error-title">Méthode Non Autorisée</h1>
    <p class="error-desc">La méthode HTTP demandée n'est pas autorisée sur cette ressource.</p>
    <?php if (!empty($allowedMethods)): ?>
        <p class="text-sm text-muted">Méthodes autorisées : <strong><?= htmlspecialchars(implode(', ', $allowedMethods)) ?></strong></p>
    <?php endif; ?>
    <a href="/salles" class="btn btn-primary">
        <?= \App\View\Icons::building('icon-sm') ?>
        <span>Retour aux salles</span>
    </a>
</div>
