<?php

declare(strict_types=1);

namespace App\Repository;

use App\Service\TransactionStrategyInterface;
use Illuminate\Database\Capsule\Manager as Capsule;

final class EloquentTransactionStrategy implements TransactionStrategyInterface
{
    public function __construct(private readonly Capsule $database)
    {
    }

    public function execute(callable $action): mixed
    {
        return $this->database->getConnection()->transaction($action);
    }
}
