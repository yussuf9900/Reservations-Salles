<?php

declare(strict_types=1);

namespace Tests\Double;

use App\Service\TransactionStrategyInterface;

final class InMemoryTransactionStrategy implements TransactionStrategyInterface
{
    public function execute(callable $action): mixed
    {
        return $action();
    }
}
