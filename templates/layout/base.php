<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? htmlspecialchars($title) . ' - ' : '' ?>Gestion des Salles Universitaires</title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
    <header class="app-header">
        <div class="container header-container">
            <div class="logo">
                <a href="/salles">🏛️ <strong>UnivSalles</strong></a>
            </div>
            <nav class="main-nav">
                <ul>
                    <li><a href="/salles">Salles</a></li>
                    <li><a href="/reservations">Réservations</a></li>
                    <li><a href="/reservations/create" class="btn btn-sm btn-primary">+ Réserver</a></li>
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
                                <?= htmlspecialchars($msg) ?>
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
            <p>&copy; <?= date('Y') ?> Université — Module Réservation de Salles (ODC-P8 PHP POO)</p>
        </div>
    </footer>
</body>
</html>
