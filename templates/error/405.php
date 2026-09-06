<div class="error-page">
    <h1>405</h1>
    <h2>Méthode non autorisée</h2>
    <p>La méthode HTTP demandée n'est pas acceptée pour cette ressource.</p>
    <?php if (!empty($allowedMethods)): ?>
        <p>Méthodes autorisées : <strong><?= htmlspecialchars(implode(', ', $allowedMethods)) ?></strong></p>
    <?php endif; ?>
    <a href="/salles" class="btn btn-primary">Retour à la liste des salles</a>
</div>
