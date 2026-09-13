<?php
declare(strict_types=1);

final class Validator
{
    private array $errors = [];
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function required(string $field, string $label): self
    {
        $val = trim((string) ($this->data[$field] ?? ''));
        if ($val === '') {
            $this->errors[$field] = "$label is required.";
        }
        return $this;
    }

    public function email(string $field, string $label): self
    {
        $val = (string) ($this->data[$field] ?? '');
        if ($val !== '' && !filter_var($val, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = "$label must be a valid email.";
        }
        return $this;
    }

    public function minLength(string $field, int $len, string $label): self
    {
        $val = (string) ($this->data[$field] ?? '');
        if ($val !== '' && mb_strlen($val) < $len) {
            $this->errors[$field] = "$label must be at least $len characters.";
        }
        return $this;
    }

    public function date(string $field, string $label): self
    {
        $val = (string) ($this->data[$field] ?? '');
        if ($val !== '' && !strtotime($val)) {
            $this->errors[$field] = "$label must be a valid date.";
        }
        return $this;
    }

    public function numeric(string $field, string $label): self
    {
        $val = (string) ($this->data[$field] ?? '');
        if ($val !== '' && !is_numeric($val)) {
            $this->errors[$field] = "$label must be numeric.";
        }
        return $this;
    }

    public function min(string $field, float $min, string $label): self
    {
        $val = $this->data[$field] ?? null;
        if ($val !== null && $val !== '' && is_numeric($val) && (float) $val < $min) {
            $this->errors[$field] = "$label must be at least $min.";
        }
        return $this;
    }

    public function custom(string $field, bool $condition, string $message): self
    {
        if (!$condition) {
            $this->errors[$field] = $message;
        }
        return $this;
    }

    public function fails(): bool { return $this->errors !== []; }
    public function errors(): array { return $this->errors; }
    public function firstError(): string { return (string) reset($this->errors); }
}