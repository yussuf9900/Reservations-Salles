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
                <ul style="display: flex; align-items: center; gap: 1rem; list-style: none; margin: 0; padding: 0;">
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
                    <li>
                        <div class="format-switcher" title="Format d'affichage des données (configurable via APP_RESPONSE_FORMAT dans .env)">
                            <a href="<?= htmlspecialchars($urlFormatHtml ?? '?format=html') ?>" class="format-pill <?= ($currentFormat ?? 'html') === 'html' ? 'active' : '' ?>">HTML</a>
                            <a href="<?= htmlspecialchars($urlFormatJson ?? '?format=json') ?>" class="format-pill <?= ($currentFormat ?? 'html') === 'json' ? 'active' : '' ?>">JSON</a>
                        </div>
                    </li>

                    <li style="margin-left: 0.5rem; border-left: 1px solid var(--slate-200); padding-left: 1rem;">
                        <?php if (!empty($currentUser)): ?>
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <span class="badge" style="background: <?= $currentUser->role === 'admin' ? '#eff6ff' : '#f0fdf4' ?>; color: <?= $currentUser->role === 'admin' ? '#1d4ed8' : '#15803d' ?>; font-weight: 600;">
                                    <?= htmlspecialchars($currentUser->nom) ?> (<?= htmlspecialchars($currentUser->role) ?>)
                                </span>
                                <a href="/logout" class="nav-link" style="color: var(--danger); font-size: 0.85rem;" title="Déconnexion">
                                    <?= \App\View\Icons::logOut('icon-sm') ?>
                                    <span>Quitter</span>
                                </a>
                            </div>
                        <?php else: ?>
                            <a href="/login" class="nav-link">
                                <?= \App\View\Icons::logIn('icon-sm') ?>
                                <span>Connexion</span>
                            </a>
                        <?php endif; ?>
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
