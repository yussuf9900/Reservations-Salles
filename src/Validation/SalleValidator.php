<?php

declare(strict_types=1);

namespace App\Validation;

use App\Model\Salle;
use Respect\Validation\Validator as v;

class SalleValidator implements ValidatorInterface
{
    public function validate(array $data): ValidationResult
    {
        $errors = [];

        // Nom : obligatoire, 2 à 100 caractères
        $nom = trim((string)($data['nom'] ?? ''));
        if (!v::stringType()->length(2, 100)->validate($nom)) {
            $errors['nom'] = 'Le nom de la salle est obligatoire et doit contenir entre 2 et 100 caractères.';
        }

        // Bâtiment : obligatoire, 2 à 100 caractères
        $batiment = trim((string)($data['batiment'] ?? ''));
        if (!v::stringType()->length(2, 100)->validate($batiment)) {
            $errors['batiment'] = 'Le bâtiment est obligatoire et doit contenir entre 2 et 100 caractères.';
        }

        // Capacité : entier compris entre 1 et 1000
        $capacite = $data['capacite'] ?? null;
        if (!v::numericVal()->intVal()->between(1, 1000)->validate($capacite)) {
            $errors['capacite'] = 'La capacité doit être un entier compris entre 1 et 1 000 places.';
        }

        // Type : valeur autorisée
        $type = (string)($data['type'] ?? '');
        if (!v::in(Salle::TYPES_AUTORISES)->validate($type)) {
            $errors['type'] = 'Le type de salle est invalide. Valeurs autorisées : ' . implode(', ', Salle::TYPES_AUTORISES) . '.';
        }

        // Active : booléen
        $activeVal = $data['active'] ?? true;
        $active = filter_var($activeVal, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        if ($active === null) {
            $errors['active'] = 'Le champ active doit être un booléen.';
        }

        $validatedData = [
            'nom'      => $nom,
            'batiment' => $batiment,
            'capacite' => (int)$capacite,
            'type'     => $type,
            'active'   => (bool)$active,
        ];

        return new ValidationResult(empty($errors), $errors, $validatedData);
    }
}
