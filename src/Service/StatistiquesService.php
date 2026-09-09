<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\ReservationRepositoryInterface;
use App\Repository\SalleRepositoryInterface;
use DateTimeImmutable;
use DateTimeInterface;

class StatistiquesService
{
    public function __construct(
        private readonly SalleRepositoryInterface $salles,
        private readonly ReservationRepositoryInterface $reservations
    ) {
    }

    public function getStatistiques(): array
    {
        $allSalles = $this->salles->all();
        $allReservations = $this->reservations->all();

        $totalSalles = count($allSalles);
        $sallesActives = count(array_filter($allSalles, fn($s) => (bool)$s->active));
        $sallesInactives = $totalSalles - $sallesActives;

        $totalReservations = count($allReservations);
        $reservationsConfirmees = count(array_filter($allReservations, fn($r) => $r->statut === 'confirmée'));
        $reservationsAnnulees = count(array_filter($allReservations, fn($r) => $r->statut === 'annulée'));

        $heuresTotalesReservees = 0.0;
        $reservationsParSalle = [];
        $heuresParSalle = [];
        $repartitionTypes = [
            'cours'        => 0,
            'informatique' => 0,
            'laboratoire'  => 0,
            'amphitheatre' => 0,
            'reunion'      => 0,
        ];

        $now = new DateTimeImmutable();
        $prochaines = [];

        foreach ($allReservations as $res) {
            if ($res->statut !== 'confirmée') {
                continue;
            }

            $debut = $res->date_debut instanceof DateTimeInterface
                ? $res->date_debut
                : new DateTimeImmutable((string)$res->date_debut);

            $fin = $res->date_fin instanceof DateTimeInterface
                ? $res->date_fin
                : new DateTimeImmutable((string)$res->date_fin);

            $heures = max(0, ($fin->getTimestamp() - $debut->getTimestamp()) / 3600);
            $heuresTotalesReservees += $heures;

            $salleId = (int)$res->salle_id;
            $reservationsParSalle[$salleId] = ($reservationsParSalle[$salleId] ?? 0) + 1;
            $heuresParSalle[$salleId] = ($heuresParSalle[$salleId] ?? 0.0) + $heures;

            if ($res->salle && isset($repartitionTypes[$res->salle->type])) {
                $repartitionTypes[$res->salle->type]++;
            }

            if ($debut >= $now) {
                $prochaines[] = [
                    'id'          => $res->id,
                    'salle_nom'   => $res->salle?->nom ?? ('Salle #' . $res->salle_id),
                    'responsable' => $res->responsable,
                    'debut'       => $debut->format('d/m/Y H:i'),
                    'fin'         => $fin->format('H:i'),
                    'duree'       => round($heures, 1) . ' h',
                ];
            }
        }

        usort($prochaines, fn($a, $b) => strcmp($a['debut'], $b['debut']));
        $prochaines = array_slice($prochaines, 0, 5);

        $topSalles = [];
        foreach ($allSalles as $salle) {
            $count = $reservationsParSalle[$salle->id] ?? 0;
            $heures = round($heuresParSalle[$salle->id] ?? 0.0, 1);
            $topSalles[] = [
                'id'           => $salle->id,
                'nom'          => $salle->nom,
                'batiment'     => $salle->batiment,
                'type'         => $salle->type,
                'capacite'     => $salle->capacite,
                'active'       => (bool)$salle->active,
                'reservations' => $count,
                'heures'       => $heures,
            ];
        }

        usort($topSalles, function ($a, $b) {
            if ($b['reservations'] === $a['reservations']) {
                return $b['heures'] <=> $a['heures'];
            }
            return $b['reservations'] <=> $a['reservations'];
        });

        $topSalles = array_slice($topSalles, 0, 5);

        $tauxOccupation = $totalSalles > 0
            ? min(100, round(($heuresTotalesReservees / max(1, $totalSalles * 40)) * 100, 1))
            : 0;

        return [
            'total_salles'            => $totalSalles,
            'salles_actives'          => $sallesActives,
            'salles_inactives'        => $sallesInactives,
            'total_reservations'      => $totalReservations,
            'reservations_confirmees' => $reservationsConfirmees,
            'reservations_annulees'   => $reservationsAnnulees,
            'heures_totales'          => round($heuresTotalesReservees, 1),
            'taux_occupation'         => $tauxOccupation,
            'top_salles'              => $topSalles,
            'repartition_types'       => $repartitionTypes,
            'prochaines_reservations' => $prochaines,
        ];
    }
}
