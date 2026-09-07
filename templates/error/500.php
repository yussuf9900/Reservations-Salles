<div class="error-page animate-in">
    <div class="error-code">500</div>
    <h1 class="error-title">Erreur Interne du Serveur</h1>
    <p class="error-desc">Une anomalie inattendue est survenue lors du traitement de votre requête.</p>
    <?php if (!empty($message) && (isset($_ENV['APP_DEBUG']) && $_ENV['APP_DEBUG'] === 'true')): ?>
        <pre style="text-align: left; background: var(--slate-100); padding: 1rem; border-radius: var(--radius-sm); max-width: 800px; margin: 1rem auto 2rem; overflow-x: auto; font-size: 0.85rem;">
            <?= htmlspecialchars($message) ?>
        </pre>
    <?php endif; ?>
    <a href="/salles" class="btn btn-primary">
        <?= \App\View\Icons::building('icon-sm') ?>
        <span>Retour à l'accueil</span>
    </a>
</div>
