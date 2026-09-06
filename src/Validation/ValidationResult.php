<?php

declare(strict_types=1);

namespace App\Validation;

class ValidationResult
{
    /**
     * @param bool $valid
     * @param array<string, string> $errors
     * @param array<string, mixed> $data
     */
    public function __construct(
        private readonly bool $valid,
        private readonly array $errors = [],
        private readonly array $data = []
    ) {
    }

    public function isValid(): bool
    {
        return $this->valid;
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function error(string $field): ?string
    {
        return $this->errors[$field] ?? null;
    }

    public function hasError(string $field): bool
    {
        return isset($this->errors[$field]);
    }

    public function validated(): array
    {
        return $this->data;
    }
}
