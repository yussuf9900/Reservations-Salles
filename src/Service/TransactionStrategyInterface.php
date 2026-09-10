<?php

declare(strict_types=1);

namespace App\Service;

interface TransactionStrategyInterface
{
    public function execute(callable $action): mixed;
}
