<?php

declare(strict_types=1);

namespace App\View;

class ViewRenderer
{
    private string $templateDir;

    public function __construct(?string $templateDir = null)
    {
        $this->templateDir = $templateDir ?? dirname(__DIR__, 2) . '/templates';
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }
    }

    /**
     * Échappe les chaînes HTML contre les attaques XSS
     */
    public static function e(?string $value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * Ajoute un message flash en session
     */
    public function setFlash(string $type, string $message): void
    {
        $_SESSION['flash'][$type][] = $message;
    }

    /**
     * Récupère et vide les messages flash
     * @return array<string, array<string>>
     */
    public function getFlashes(): array
    {
        $flashes = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);
        return $flashes;
    }

    /**
     * Rendu d'une vue dans le layout principal
     *
     * @param string $view Chemin relatif du template sans .php (ex: 'salle/index')
     * @param array<string, mixed> $data Données transmises au template
     * @param string $layout Layout à utiliser (défaut 'layout/base')
     */
    public function render(string $view, array $data = [], string $layout = 'layout/base'): string
    {
        $data['flashes'] = $this->getFlashes();

        // Rendre la vue spécifique
        $content = $this->renderViewOnly($view, $data);

        // Si aucun layout n'est demandé
        if ($layout === '') {
            return $content;
        }

        // Rendre dans le layout
        $data['content'] = $content;
        return $this->renderViewOnly($layout, $data);
    }

    private function renderViewOnly(string $view, array $data): string
    {
        $file = $this->templateDir . '/' . ltrim($view, '/') . '.php';

        if (!file_exists($file)) {
            throw new \RuntimeException("Fichier de vue introuvable : {$file}");
        }

        // Extrait les variables pour la vue
        extract($data, EXTR_SKIP);

        // Helper disponible dans les vues
        $e = fn(?string $val) => self::e($val);

        ob_start();
        require $file;
        return (string)ob_get_clean();
    }
}
