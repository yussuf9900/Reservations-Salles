<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="UnivSalles — Système de gestion et de réservation des salles universitaires. Consultez la disponibilité, réservez et administrez les espaces.">
    <title><?= isset($title) ? htmlspecialchars($title) . ' — ' : '' ?>UnivSalles</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
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
                    <?php if (!empty($currentUser)): ?>
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
                            <a href="/dashboard" class="nav-link">
                                <?= \App\View\Icons::barChart('icon-sm') ?>
                                <span>Tableau de bord</span>
                            </a>
                        </li>
                        <li>
                            <a href="/reservations/create" class="btn btn-sm btn-primary">
                                <?= \App\View\Icons::plus('icon-sm') ?>
                                <span>Réserver</span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <li>
                        <div class="format-switcher" title="Format d'affichage des données">
                            <a href="<?= htmlspecialchars($urlFormatHtml ?? '?format=html') ?>" class="format-pill <?= ($currentFormat ?? 'html') === 'html' ? 'active' : '' ?>">HTML</a>
                            <a href="<?= htmlspecialchars($urlFormatJson ?? '?format=json') ?>" class="format-pill <?= ($currentFormat ?? 'html') === 'json' ? 'active' : '' ?>">JSON</a>
                        </div>
                    </li>
                    <?php if (!empty($currentUser)): ?>
                        <li class="nav-separator">
                            <div class="user-info">
                                <span class="badge badge-accent">
                                    <?= htmlspecialchars($currentUser->nom) ?> (<?= htmlspecialchars($currentUser->role) ?>)
                                </span>
                                <a href="/logout" class="nav-link nav-logout" title="Déconnexion">
                                    <?= \App\View\Icons::logOut('icon-sm') ?>
                                    <span>Quitter</span>
                                </a>
                            </div>
                        </li>
                    <?php endif; ?>
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
            <p>&copy; <?= date('Y') ?> Université — Système de Gestion des Salles</p>
            <p class="text-sm"><span class="footer-accent">Architecture Modulaire</span> &bull; PHP POO</p>
        </div>
    </footer>
</body>
</html>
