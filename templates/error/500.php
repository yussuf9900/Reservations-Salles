<div class="error-page">
    <h1>500</h1>
    <h2>Erreur Interne du Serveur</h2>
    <p>Une anomalie est survenue lors du traitement de votre requête.</p>
    <?php if (!empty($message) && (isset($_ENV['APP_DEBUG']) && $_ENV['APP_DEBUG'] === 'true')): ?>
        <pre style="text-align: left; background: #f1f5f9; padding: 1rem; border-radius: 6px; max-width: 800px; margin: 1rem auto; overflow-x: auto;">
            <?= htmlspecialchars($message) ?>
        </pre>
    <?php endif; ?>
    <a href="/salles" class="btn btn-primary">Retour à l'accueil</a>
</div>
