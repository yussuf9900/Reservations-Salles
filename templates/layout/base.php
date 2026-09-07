<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? htmlspecialchars($title) . ' - ' : '' ?>UnivSalles - Gestion Universitaire</title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
    <header class="app-header">
        <div class="container header-container">
            <a href="/salles" class="brand">
                <div class="brand-icon">
                    <?= \App\View\Icons::building('icon') ?>
                </div>
                <div class="brand-text">Univ<span>Salles</span></div>
            </a>
            <nav class="main-nav">
                <ul>
                    <li>
                        <a href="/salles" class="nav-link">
                            <?= \App\View\Icons::building('icon-sm') ?>
                            <span>Salles</span>
                        </a>
                    </li>
                    <li>
                        <a href="/reservations" class="nav-link">
                            <?= \App\View\Icons::calendar('icon-sm') ?>
                            <span>Réservations</span>
                        </a>
                    </li>
                    <li>
                        <a href="/reservations/create" class="btn btn-sm btn-primary">
                            <?= \App\View\Icons::plus('icon-sm') ?>
                            <span>Réserver</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
            <?php if (!empty($flashes)): ?>
                <div class="flash-messages">
                    <?php foreach ($flashes as $type => $messages): ?>
                        <?php foreach ($messages as $msg): ?>
                            <div class="alert alert-<?= htmlspecialchars($type === 'error' ? 'danger' : 'success') ?>">
                                <?= $type === 'error' ? \App\View\Icons::alertCircle('icon') : \App\View\Icons::checkCircle('icon') ?>
                                <span><?= htmlspecialchars($msg) ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?= $content ?? '' ?>
        </div>
    </main>

    <footer class="app-footer">
        <div class="container footer-container">
            <p>&copy; <?= date('Y') ?> Université - Système de Gestion des Salles</p>
            <p class="text-sm text-muted">Architecture Modulaire &bull; PHP POO</p>
        </div>
    </footer>
</body>
</html>
