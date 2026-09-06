<?php

declare(strict_types=1);

namespace App\Validation;

use Respect\Validation\Validator as v;

class ReservationValidator implements ValidatorInterface
{
    public function validate(array $data): ValidationResult
    {
        $errors = [];

        // salle_id : entier positif
        $salleId = $data['salle_id'] ?? null;
        if (!v::numericVal()->intVal()->positive()->validate($salleId)) {
            $errors['salle_id'] = 'La salle sélectionnée est invalide.';
        }

        // responsable : 2 à 120 caractères
        $responsable = trim((string)($data['responsable'] ?? ''));
        if (!v::stringType()->length(2, 120)->validate($responsable)) {
            $errors['responsable'] = 'Le nom du responsable est obligatoire et doit contenir entre 2 et 120 caractères.';
        }

        // email : adresse valide
        $email = trim((string)($data['email'] ?? ''));
        if (!v::email()->validate($email)) {
            $errors['email'] = 'L\'adresse électronique est invalide.';
        }

        // motif : 5 à 255 caractères
        $motif = trim((string)($data['motif'] ?? ''));
        if (!v::stringType()->length(5, 255)->validate($motif)) {
            $errors['motif'] = 'Le motif doit contenir entre 5 et 255 caractères.';
        }

        // date_debut : date valide
        $dateDebut = trim((string)($data['date_debut'] ?? ''));
        if (empty($dateDebut) || !v::dateTime()->validate($dateDebut)) {
            $errors['date_debut'] = 'La date et l\'heure de début doivent être une date valide.';
        }

        // date_fin : date valide
        $dateFin = trim((string)($data['date_fin'] ?? ''));
        if (empty($dateFin) || !v::dateTime()->validate($dateFin)) {
            $errors['date_fin'] = 'La date et l\'heure de fin doivent être une date valide.';
        }

        $validatedData = [
            'salle_id'    => (int)$salleId,
            'responsable' => $responsable,
            'email'       => $email,
            'motif'       => $motif,
            'date_debut'  => $dateDebut,
            'date_fin'    => $dateFin,
        ];

        return new ValidationResult(empty($errors), $errors, $validatedData);
    }
}
